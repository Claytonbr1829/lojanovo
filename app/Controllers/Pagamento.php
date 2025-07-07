<?php

namespace App\Controllers;

use App\Models\PedidoItemModel;
use App\Models\PedidoModel;
use App\Models\ProdutoModel;
use App\Models\GatewayPagamentoModel;
use App\Models\ConfiguracaoModel;
use App\Models\ClienteModelBase;
use App\Models\CobrancaModel;
use App\Models\PedidosPagamentoModel;
use Exception;
use GuzzleHttp\Client;

class Pagamento extends BaseController
{
    protected $pedidoModel;
    protected $produtoModel;
    protected $gatewayModel;
    protected $configuracaoModel;
    protected $pedidosPagamentoModel;
    protected $pedidoItemModel;

    public function __construct()
    {
        $this->pedidoModel = new PedidoModel();
        $this->produtoModel = new ProdutoModel();
        $this->gatewayModel = new GatewayPagamentoModel();
        $this->configuracaoModel = new ConfiguracaoModel();
        $this->pedidosPagamentoModel = new PedidosPagamentoModel();
        $this->pedidoItemModel = new PedidoItemModel();
    }

    public function index($token = null)
    {
        if ($redirect = $this->verificaEmpresaOuRedireciona()) {
            return $redirect;
        }

        if (empty($token)) {
            return redirect()->to('/meus-pedidos')->with('error', 'Pedido não encontrado.');
        }

        $pedido = $this->pedidoModel->findPedido($token);
        if (!$pedido) {
            return redirect()->to('/meus-pedidos')->with('error', 'Pedido não encontrado.');
        }

        // $pagamento = $this->pedidosPagamentoModel->getPagamentoByPedidoId($pedido['id_pedido']);
        // if ($pagamento) {
        //     $pedido = array_merge($pedido, $pagamento);
        // }

        $produtos = $this->produtoModel->findPedidoItens($pedido['id_pedido']);
        $config = $this->configuracaoModel->getConfiguracoes($this->idEmpresa);

        $gateway_pagamento = $this->gatewayModel
            ->where('id_empresa', $this->idEmpresa)
            ->where('id', $config['gateway_pagamento'] ?? 0)
            ->first();

        $itensPedido = $this->pedidoItemModel->where('id_pedido', $pedido['id_pedido'])->findAll();

        $data = [
            'pedido' => $pedido,
            'produtos' => $produtos,
            'itensPedido' => $itensPedido, // Nome consistente com a view
            'gateway_pagamento' => $gateway_pagamento,
        ];

        return $this->renderView('pagamento', $data);
    }

    public function processarPagamento($token = null)
    {
        if ($redirect = $this->verificaEmpresaOuRedireciona()) {
            return $redirect;
        }

        $model = new CobrancaModel();
        $token = $this->request->getPost('token') ?? $token;

        $pedido = $this->pedidoModel->findPedido($token);
        if (!$pedido) {
            return $this->response->setJSON(['status' => 1, "mensagem" => "Pedido não encontrado."]);
        }

        $formaPagamento = $this->request->getPost('formaPagamento') ?? 'pix';
        $valorTotal = (float) preg_replace('/[^0-9.]/', '', str_replace(',', '.', $pedido['valor_pagamento']));
        $descricao = "Pagamento referente ao pedido #{$pedido['id_pedido']}";

        $apiKey = '$aact_hmlg_000MzkwODA2MWY2OGM3MWRlMDU2NWM3MzJlNzZmNGZhZGY6OjM1YmU1NzcyLTBhZWItNDg2MS1hOTY2LTIzMzgyNTI3Mzk0Njo6JGFhY2hfNDI1MjY4ODQtYzdjMi00MWRkLTgxZjAtYzZlYTcxMTNlNmQx';

        $cliente_model = new ClienteModelBase();
        $cliente = $cliente_model
            ->where('email', $pedido['email'])
            ->where('id_empresa', $this->idEmpresa)
            ->first();

        if (!$cliente) {
            return $this->response->setJSON(['status' => 1, "mensagem" => "Cliente não encontrado."]);
        }

        if ((is_null($cliente->cpf) || $cliente->cpf == '') && (is_null($cliente->cnpj) || $cliente->cnpj == '')) {
            return $this->response->setJSON(['status' => 1, "mensagem" => "Cliente sem documento de registro. Por favor, cadastre em 'Minha Conta'."]);
        }

        $documento = !empty($cliente->cpf) ? $cliente->cpf : $cliente->cnpj;
        if (!$this->validarDocumento($documento)) {
            return $this->response->setJSON(['status' => 1, "mensagem" => "Documento inválido. Por favor, verifique os dados cadastrados em 'Minha Conta'."]);
        }

        // A partir daqui, irá fazer requisição no Asaas
        try {
            $id_asaas = $cliente->id_gateway;

            if (empty($id_asaas) || is_null($id_asaas)) {
                // Se o cliente não tiver sido cadastrado no Asaas, ele cria na função abaixo (necessário pra prosseguir)
                $id_asaas = $this->criarCliente($apiKey, $pedido['email']);
                if (is_array($id_asaas) && isset($id_asaas['status'])) {
                    return $this->response->setJSON($id_asaas);
                }
            }

            // Se o cliente já tem cadastro, já passa direto pra cá.
            switch ($formaPagamento) {
                case 'pix':
                    $response = $this->gerarCobrancaPix($apiKey, $id_asaas, $valorTotal);
                    $vencimento = date('Y-m-d', strtotime('+1 day'));
                    break;
                case 'boleto':
                    $response = $this->gerarCobrancaBoleto($apiKey, $id_asaas, $valorTotal);
                    $vencimento = date('Y-m-d', strtotime('+3 days'));
                    break;
                case 'credit_card':
                    $response = $this->gerarCobrancaCartao($apiKey, $id_asaas, $valorTotal, $documento);
                    $vencimento = date('Y-m-d', strtotime('+3 days'));
                    break;
                default:
                    return $this->response->setJSON(['status' => 1, "mensagem" => "Forma de pagamento inválida."]);
            }

            // Salvar a forma de pagamento na tabela pedido
            $this->pedidoModel->update($pedido['id_pedido'], [
                'forma_de_pagamento' => $formaPagamento
            ]);

            
            if (isset($response->errors)) {
                return $this->response->setJSON(['status' => 1, "mensagem" => $response->errors[0]->description]);
            }

            $this->salvarDadosPagamento(
                $pedido['id_pedido'],
                $valorTotal,
                $vencimento,
                $descricao
            );

            do {
                $random = rand(0, 99999);
                $existingTransacao = $model->where('id_transacao', $random)->first();
            } while ($existingTransacao);

            $dataTransacao = [
                'id_transacao' => $random,
                'valor' => $valorTotal,
                'forma_pagamento' => $formaPagamento,
                'status_pagamento' => 'EM PROCESSAMENTO',
                'data_criacao' => date('Y-m-d H:i:s'),
                'id_empresa' => $this->idEmpresa,
                'id_usuario' => $cliente->id_cliente,
                'link_pagamento' => $response->invoiceUrl ?? null,
                'id_asaas' => $response->id
            ];


            if ($formaPagamento === 'pix' && isset($response->encodedImage)) {
                $dataTransacao['pix_qr_code'] = $response->encodedImage;
            } elseif ($formaPagamento === 'boleto') {
                // Requisição para gerar linha digitavel do boleto
                $id_pagamento = $response->id;

                $boletoUrl = 'https://api-sandbox.asaas.com/v3/payments/' . $id_pagamento . '/identificationField';

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $boletoUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'access_token: ' . $apiKey,
                    'User-Agent: Mozilla/5.0 (compatible; MyApp/1.0; +http://www.example.com)'  // Adiciona o User-Agent
                ]);
                $boletoResponse = curl_exec($ch);

                curl_close($ch);

                $boletoData = json_decode($boletoResponse, true);

                $dataTransacao['boleto_linha_digitavel'] = $boletoData['identificationField'];
            } elseif ($formaPagamento === 'credit_card') {
                $dataTransacao['comprovante'] = $response->transactionReceiptUrl;
            }

            $model->save($dataTransacao);

            return $this->response->setJSON([
                "status" => 0,
                "mensagem" => "Pagamento processado com sucesso!",
                "data" => $dataTransacao
            ]);

        } catch (Exception $e) {
            log_message('error', 'Erro no processamento de pagamento: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 1,
                'mensagem' => 'Ocorreu um erro ao processar seu pagamento. Por favor, tente novamente.'
            ]);
        }
    }

    private function salvarDadosPagamento($id_pedido, $valor, $vencimento, $descricao = '', $status = 'pendente')
    {
        $this->pedidosPagamentoModel->save([
            'id_pedido' => $id_pedido,
            'data_inicial_pagamento' => date('Y-m-d'),
            'vencimento_pagamento' => $vencimento,
            'descricao_pagamento' => $descricao,
            'valor_pagamento' => $valor,
            'status' => $status
        ]);
    }

    public function criarCliente($apiKey, $email)
    {
        $model = new ClienteModelBase();
        $usuario = $model->where('email', $email)->where('id_empresa', $this->idEmpresa)->first();

        if (!$usuario) {
            return ['status' => 1, 'mensagem' => 'Cliente não encontrado'];
        }

        $data = [
            'name' => $usuario->nome,
            'cpfCnpj' => $usuario->cpf,
            'email' => $usuario->email,
            'mobilePhone' => $usuario->celular_1
        ];

        $client = new Client();
        try {
            $response = $client->post('https://api-sandbox.asaas.com/v3/customers', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'access_token' => $apiKey
                ],
                'json' => $data
            ]);

            $responseData = json_decode($response->getBody());
            // Aqui ele deve criar e cadastrar o cliente

            if (isset($responseData->id)) {
                $this->atualizarIdGateway($usuario->id_cliente, $responseData->id);
                return $responseData->id;
            } else {
                $error = $responseData->errors[0]->description ?? 'Erro desconhecido';
                return ['status' => 1, 'mensagem' => $error];
            }
        } catch (Exception $e) {
            return ['status' => 1, 'mensagem' => $e->getMessage()];
        }
    }

    public function atualizarIdGateway($idCliente, $idGateway)
    {
        try {
            $model = new ClienteModelBase();
            return $model->update($idCliente, ['id_gateway' => $idGateway]);
        } catch (Exception $e) {
            log_message('error', "Erro ao atualizar id_gateway: " . $e->getMessage());
            return false;
        }
    }

    // public function gerarCobrancaPix2($apiKey, $id_asaas, $valor)
    // {
    //     $url = 'https://api-sandbox.asaas.com/v3/payments';

    //     $pedido_total = str_replace(['R$', ','], ['', '.'], $valor);
    //     $pedido_total = substr($pedido_total, 1);
    //     $pedido_total = preg_replace('/^[^\w\d.]+/', '', $pedido_total);

    //     $data = [
    //         'value' => $pedido_total,
    //         'description' => 'Compra na loja',
    //         'customer' => $id_asaas, // Certifique-se de que o cliente está vinculado corretamente
    //         'billingType' => 'PIX', // Dependendo da forma de pagamento, você pode enviar os dados correspondentes
    //         'dueDate' => date('Y-m-d', strtotime('+1 days')), // Data de vencimento em 1 dia
    //     ];

    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_URL, $url);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, [
    //         'Content-Type: application/json',
    //         'access_token: ' . $apiKey,
    //         'User-Agent: Mozilla/5.0 (compatible; MyApp/1.0; +http://www.example.com)'  // Adiciona o User-Agent
    //     ]);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    //     $response = curl_exec($ch);
    //     curl_close($ch);

    //     $response = json_decode($response, true);

    //     var_dump($response);exit;

    //     return $response;
    // }

    private function gerarCobrancaPix($apiKey, $id_asaas, $valor)
    {
        $client = new Client();
        $response = $client->post('https://api-sandbox.asaas.com/v3/payments', [
            'headers' => [
                'Content-Type' => 'application/json',
                'access_token' => $apiKey
            ],
            'json' => [
                'customer' => $id_asaas,
                'billingType' => 'PIX',
                'value' => $valor,
                'dueDate' => date('Y-m-d', strtotime('+1 day')),
                'description' => 'Pagamento via PIX'
            ]
        ]);

        $responseData = json_decode($response->getBody());

        if (isset($responseData->id)) {
            $qrResponse = $client->get('https://api-sandbox.asaas.com/v3/payments/' . $responseData->id . '/pixQrCode', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'access_token' => $apiKey
                ]
            ]);

            $qrData = json_decode($qrResponse->getBody());
            $responseData->encodedImage = $qrData->encodedImage ?? null;
        }

        return $responseData;
    }

    private function gerarCobrancaBoleto($apiKey, $id_asaas, $valor)
    {
        $client = new Client();
        $response = $client->post('https://api-sandbox.asaas.com/v3/payments', [
            'headers' => [
                'Content-Type' => 'application/json',
                'access_token' => $apiKey
            ],
            'json' => [
                'customer' => $id_asaas,
                'billingType' => 'BOLETO',
                'value' => $valor,
                'dueDate' => date('Y-m-d', strtotime('+3 days')),
                'description' => 'Pagamento via Boleto'
            ]
        ]);

        return json_decode($response->getBody());
    }

    private function gerarCobrancaCartao($apiKey, $id_asaas, $valor, $documento)
    {
        $client = new Client();
        $dadosCliente = new ClienteModelBase();

        $cliente = $dadosCliente->where('id_gateway', $id_asaas)->first();

        $response = $client->post('https://api-sandbox.asaas.com/v3/payments', [
            'headers' => [
                'Content-Type' => 'application/json',
                'access_token' => $apiKey
            ],
            'json' => [
                'customer' => $id_asaas,
                'billingType' => 'CREDIT_CARD',
                'value' => $valor,
                'dueDate' => date('Y-m-d', strtotime('+3 days')),
                'description' => 'Pagamento via Cartão de Crédito',
                'creditCard' => [
                    "holderName" => $this->request->getVar('nome_titular'),
                    "number" => $this->request->getVar('numero_cartao'),
                    "expiryMonth" => $this->request->getVar('validade_mes'),
                    "expiryYear" => $this->request->getVar('validade_ano'),
                    "ccv" => $this->request->getVar('cvv')
                ],
                'creditCardHolderInfo' => [
                    "name" => $cliente->nome,
                    "email" => $cliente->email,
                    "cpfCnpj" => $documento,
                    "postalCode" => $cliente->cep,
                    "addressNumber" => $cliente->numero,
                    "phone" => '11111111111'
                ]
            ]
        ]);

        return json_decode($response->getBody());
    }


    function validarDocumento($documento)
    {
        // Remove caracteres não numéricos
        $documento = preg_replace('/[^0-9]/', '', $documento);

        // Verifica o tamanho para identificar se é CPF ou CNPJ
        if (strlen($documento) < 18) {
            return $this->validarCPF($documento);
        } else {
            return $this->validarCNPJ($documento);
        }
    }

    function validarCPF($cpf)
    {
        // Verifica se todos os dígitos são iguais
        if (preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        // Validação do primeiro dígito verificador
        $soma = 0;
        for ($i = 0; $i < 9; $i++) {
            $soma += $cpf[$i] * (10 - $i);
        }
        $resto = $soma % 11;
        $dv1 = ($resto < 2) ? 0 : 11 - $resto;

        if ($cpf[9] != $dv1) {
            return false;
        }

        // Validação do segundo dígito verificador
        $soma = 0;
        for ($i = 0; $i < 10; $i++) {
            $soma += $cpf[$i] * (11 - $i);
        }
        $resto = $soma % 11;
        $dv2 = ($resto < 2) ? 0 : 11 - $resto;

        return $cpf[10] == $dv2;
    }

    function validarCNPJ($cnpj)
    {
        // Verifica se todos os dígitos são iguais
        if (preg_match('/^(\d)\1{13}$/', $cnpj)) {
            return false;
        }

        // Validação do primeiro dígito verificador
        $soma = 0;
        $multiplicador = 5;
        for ($i = 0; $i < 12; $i++) {
            $soma += $cnpj[$i] * $multiplicador;
            $multiplicador = ($multiplicador == 2) ? 9 : $multiplicador - 1;
        }
        $resto = $soma % 11;
        $dv1 = ($resto < 2) ? 0 : 11 - $resto;

        if ($cnpj[12] != $dv1) {
            return false;
        }

        // Validação do segundo dígito verificador
        $soma = 0;
        $multiplicador = 6;
        for ($i = 0; $i < 13; $i++) {
            $soma += $cnpj[$i] * $multiplicador;
            $multiplicador = ($multiplicador == 2) ? 9 : $multiplicador - 1;
        }
        $resto = $soma % 11;
        $dv2 = ($resto < 2) ? 0 : 11 - $resto;

        return $cnpj[13] == $dv2;
    }
}
