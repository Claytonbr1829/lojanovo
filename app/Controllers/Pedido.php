<?php
namespace App\Controllers;

use App\Models\ClienteModel;
use App\Models\PedidoModel;
use App\Models\PedidoItemModel;
use App\Models\ProdutoModel;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use MercadoPago\SDK as MercadoPagoSDK;
use MercadoPago\Payment;

class Pedido extends BaseController
{
    protected $pedidoModel;
    protected $pedidoItemModel;
    protected $produtoModel;
    protected $clienteModel;
    protected $db;

    public function __construct()
    {
        $this->pedidoModel = new PedidoModel();
        $this->pedidoItemModel = new PedidoItemModel();
        $this->produtoModel = new ProdutoModel();
        $this->db = \Config\Database::connect();
        $this->clienteModel = new ClienteModel();
    }

    public function view($idPedido)
    {
        try {
            // Verifica se o ID é válido
            if (!is_numeric($idPedido)) {
                throw new \RuntimeException('ID do pedido inválido');
            }

            // Busca o pedido
            $pedido = $this->pedidoModel->find($idPedido);
            if (!$pedido) {
                return redirect()->to('/')->with('error', 'Pedido não encontrado.');
            }

            // Valores padrão
            $defaults = [
                'valor_frete' => 0,
                'preco_total' => 0,
                'forma_de_pagamento' => 'Não informado',
                'status_pedido' => 'pendente',
                'endereco_rua' => '',
                'numero_rua' => '',
                'complemento' => '',
                'bairro' => '',
                'cidade' => '',
                'estado' => '',
                'cep' => ''
            ];

            $pedido = array_merge($defaults, $pedido);

            // Formatação
            $pedido['data'] = date('d/m/Y', strtotime($pedido['created_at'] ?? 'now'));
            $pedido['hora'] = date('H:i', strtotime($pedido['created_at'] ?? 'now'));
            $pedido['status_pedido'] = strtolower($pedido['status_pedido']);

            // Itens do pedido
            $itensPedido = $this->pedidoItemModel->where('id_pedido', $idPedido)->findAll();

            return $this->renderView('detalhes', [
                'pedido' => $pedido,
                'itensPedido' => $itensPedido,
                'titulo' => 'Detalhes do Pedido #' . $idPedido,
                'mostrarBotaoVoltarCheckout' => session()->has('ultimo_pedido')
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Erro ao visualizar pedido: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ocorreu um erro ao carregar o pedido');
        }
    }

    public function pagamento($idPedido)
    {
        $pedido = $this->pedidoModel->find($idPedido);

        if (!$pedido) {
            return redirect()->to('/')->with('error', 'Pedido não encontrado.');
        }

        // Converter status para formato esperado
        $statusPedido = strtolower($pedido['status_pedido'] ?? 'pendente');

        if ($statusPedido !== 'pendente') {
            return redirect()->to('pedido/$idPedido')
                ->with('info', 'Este pedido já foi processado.');
        }

        // Restante do método permanece igual...
    }
    protected function processarPix($pedido)
    {
        try {
            // Configurações básicas
            $valor = number_format($pedido['preco_total'], 2, '.', '');
            $chavePix = 'sua_chave_pix@seudominio.com'; // Ou CPF/CNPJ
            $beneficiario = 'Nome da Sua Empresa';
            $cidade = 'Sua Cidade';
            $txId = 'PED' . $pedido['id_pedido'] . '-' . time();

            // Gera o payload PIX
            $payload = [
                'chave' => $chavePix,
                'valor' => $valor,
                'infoAdicional' => 'Pagamento Pedido ' . $pedido['id_pedido'],
                'beneficiario' => $beneficiario,
                'cidade' => $cidade,
                'txId' => $txId,
            ];

            // Se estiver usando API de algum banco/PSP:
            // $apiPix = new ApiPixService(); // Sua classe de integração
            // $response = $apiPix->gerarCobranca($payload);

            // Para implementação simples (geração manual):
            $pixCode = $this->gerarPayloadPixManual($payload);

            // Salva os dados do pagamento
            $dadosPagamento = [
                'id_pedido' => $pedido['id_pedido'],
                'metodo' => 'pix',
                'valor' => $valor,
                'status_pedido' => 'pendente',
                'codigo_pix' => $pixCode,
                'qr_code' => $this->gerarQrCodePix($pixCode),
                'data_expiracao' => date('Y-m-d H:i:s', strtotime('+30 minutes')),
                'txid' => $txId
            ];

            $this->db->table('pedidos_pagamento')->insert($dadosPagamento);

            return [
                'success' => true,
                'pix_code' => $pixCode,
                'qr_code' => $dadosPagamento['qr_code'],
                'expires_at' => $dadosPagamento['data_expiracao']
            ];

        } catch (\Exception $e) {
            log_message('error', 'Erro ao processar PIX: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Falha ao gerar pagamento PIX'
            ];
        }
    }

    protected function gerarPayloadPixManual(array $payload): string
    {
        // Implementação básica do payload PIX
        $payloadFormatado = "000201" . // Início do payload
            "2636" . // GUI do BR Code
            "0014br.gov.bcb.pix" . // Chave PIX
            "01" . sprintf('%02d', strlen($payload['chave'])) . $payload['chave'] . // Chave
            "52040000" . // Categoria comercial
            "5303986" . // Moeda (Real)
            "54" . sprintf('%02d', strlen($payload['valor'])) . $payload['valor'] . // Valor
            "5802BR" . // País
            "59" . sprintf('%02d', strlen($payload['beneficiario'])) . $payload['beneficiario'] . // Beneficiário
            "60" . sprintf('%02d', strlen($payload['cidade'])) . $payload['cidade'] . // Cidade
            "62" . // Additional data field
            "05" . sprintf('%02d', strlen($payload['txId'])) . $payload['txId'] . // TXID
            "6304"; // CRC16

        $crc = crc16($payloadFormatado);
        return $payloadFormatado . strtoupper(dechex($crc));
    }

    protected function gerarQrCodePix(string $payload): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle(400),
            new ImagickImageBackEnd()
        );

        $writer = new Writer($renderer);
        $qrCode = $writer->writeString($payload);

        // Salva em storage ou converte para base64
        $path = WRITEPATH . 'uploads/qrcodes/' . uniqid() . '.png';
        file_put_contents($path, $qrCode);

        return base_url('writable/uploads/qrcodes/' . basename($path));
    }

    protected function processarCartao($pedido)
    {
        try {
            $cliente = session()->get('cliente');

            // Obter dados do cartão do POST
            $dadosCartao = $this->request->getPost();

            // Validação básica
            if (
                empty($dadosCartao['numero_cartao']) || empty($dadosCartao['validade']) ||
                empty($dadosCartao['cvv']) || empty($dadosCartao['nome_titular'])
            ) {
                throw new \Exception('Dados do cartão incompletos');
            }

            // Configura gateway de pagamento (exemplo com Mercado Pago)
            MercadoPagoSDK::setAccessToken('SEU_ACCESS_TOKEN');

            $payment = new Payment();
            $payment->transaction_amount = (float) $pedido['valor_a_pagar'];
            $payment->token = $dadosCartao['token'];
            $payment->description = "Pedido #{$pedido['id_pedido']}";
            $payment->installments = (int) ($dadosCartao['parcelas'] ?? 1);
            $payment->payment_method_id = $dadosCartao['bandeira'];
            $payment->payer = [
                'email' => $cliente['email'],
                'first_name' => explode(' ', $pedido['nome'])[0],
                'last_name' => explode(' ', $pedido['nome'])[1] ?? '',
                'identification' => [
                    'type' => 'CPF',
                    'number' => preg_replace('/[^0-9]/', '', $cliente['cpf'])
                ]
            ];

            // Salva tentativa de pagamento
            $dadosPagamento = [
                'id_pedido' => $pedido['id_pedido'],
                'metodo' => 'cartao',
                'valor' => $pedido['valor_a_pagar'],
                'status' => 'processando',
                'dados_cartao' => json_encode([
                    'ultimos_digitos' => substr($dadosCartao['numero_cartao'], -4),
                    'bandeira' => $dadosCartao['bandeira'],
                    'parcelas' => $payment->installments
                ]),
                'data_processamento' => date('Y-m-d H:i:s')
            ];

            $idPagamento = $this->db->table('pedidos_pagamento')->insert($dadosPagamento);

            // Processa o pagamento
            $payment->save();

            // Atualiza status
            $status = $payment->status === 'approved' ? 'pago' : 'recusado';

            $this->db->table('pedidos_pagamento')
                ->where('id', $idPagamento)
                ->update([
                    'status' => $status,
                    'codigo_transacao' => $payment->id,
                    'resposta_gateway' => json_encode($payment),
                    'data_resposta' => date('Y-m-d H:i:s')
                ]);

            if ($payment->status !== 'approved') {
                throw new \Exception($payment->status_detail ?? 'Pagamento recusado');
            }

            return [
                'success' => true,
                'transaction_id' => $payment->id,
                'status' => $status
            ];

        } catch (\Exception $e) {
            log_message('error', 'Erro ao processar cartão: ' . $e->getMessage());

            // Atualiza como falha se houve tentativa
            if (!empty($idPagamento)) {
                $this->db->table('pedidos_pagamento')
                    ->where('id', $idPagamento)
                    ->update([
                        'status' => 'falha',
                        'resposta_gateway' => $e->getMessage(),
                        'data_resposta' => date('Y-m-d H:i:s')
                    ]);
            }

            return [
                'success' => false,
                'message' => 'Falha no processamento do cartão: ' . $e->getMessage()
            ];
        }
    }

    protected function processarBoleto($pedido)
    {
        try {
            $cliente = session()->get('cliente');

            // Configuração básica
            $valor = number_format($pedido['valor_a_pagar'], 2, '.', '');
            $vencimento = date('Y-m-d', strtotime('+3 days'));
            $beneficiario = 'Nome da Sua Empresa';
            $cnpjBeneficiario = '00.000.000/0001-00';
            $instrucoes = "Pedido #{$pedido['id']} - Não receber após o vencimento";

            // Exemplo com integração ao Banco do Brasil
            $client = new \GuzzleHttp\Client();

            $response = $client->post('https://api.bb.com.br/cobrancas/v2/boletos', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->getTokenBB(),
                    'Content-Type' => 'application/json',
                    'x-developer-application' => 'SEU_APP_ID'
                ],
                'json' => [
                    'numeroConvenio' => 1234567,
                    'numeroCarteira' => 17,
                    'numeroVariacaoCarteira' => 19,
                    'codigoModalidade' => 1,
                    'dataEmissao' => date('Y-m-d'),
                    'dataVencimento' => $vencimento,
                    'valorOriginal' => $valor,
                    'numeroTituloCliente' => 'PED' . $pedido['id_pedido'],
                    'pagador' => [
                        'tipoInscricao' => 1, // 1-CPF, 2-CNPJ
                        'numeroInscricao' => preg_replace('/[^0-9]/', '', $cliente['cpf']),
                        'nome' => $cliente['nome'],
                        'endereco' => $pedido['endereco']['logradouro'],
                        'cep' => preg_replace('/[^0-9]/', '', $pedido['endereco']['cep']),
                        'cidade' => $pedido['endereco']['cidade'],
                        'uf' => $pedido['endereco']['uf']
                    ],
                    'beneficiario' => [
                        'nome' => $beneficiario,
                        'numeroInscricao' => preg_replace('/[^0-9]/', '', $cnpjBeneficiario),
                    ],
                    'instrucoes' => $instrucoes
                ]
            ]);

            $boleto = json_decode($response->getBody(), true);

            // Salva os dados do pagamento
            $dadosPagamento = [
                'id_pedido' => $pedido['id_pedido'],
                'metodo' => 'boleto',
                'valor' => $valor,
                'status' => 'pendente',
                'codigo_barras' => $boleto['codigoBarraNumerico'],
                'linha_digitavel' => $boleto['linhaDigitavel'],
                'url_boleto' => $boleto['url'],
                'data_vencimento' => $vencimento,
                'nosso_numero' => $boleto['nossoNumero'],
                'codigo_transacao' => $boleto['numero']
            ];

            $this->db->table('pedidos_pagamento')->insert($dadosPagamento);

            return [
                'success' => true,
                'linha_digitavel' => $boleto['linhaDigitavel'],
                'codigo_barras' => $boleto['codigoBarraNumerico'],
                'url_boleto' => $boleto['url'],
                'vencimento' => $vencimento
            ];

        } catch (\Exception $e) {
            log_message('error', 'Erro ao gerar boleto: ' . $e->getMessage());

            // Fallback para boleto manual se a API falhar
            return $this->gerarBoletoManual($pedido);
        }
    }

    protected function gerarBoletoManual($pedido)
    {
        try {
            $valor = $pedido['total'];
            $vencimento = date('d/m/Y', strtotime('+3 days'));

            // Gera dados básicos do boleto
            $nossoNumero = date('Ymd') . str_pad($pedido['id_pedido'], 6, '0', STR_PAD_LEFT);
            $codigoBarras = '341' . // Código do banco (ex: 341-Itaú)
                '9' . // Código de moeda (9-Real)
                '0' . // DV do código de barras (será calculado)
                '2' . // Fator de vencimento (dias desde 07/10/1997)
                str_pad(number_format($valor, 2, '', ''), 10, '0', STR_PAD_LEFT) .
                '0000000000000000000000000000000000000000'; // Campo livre

            // Calcula DV do código de barras
            $dv = $this->calculaDVCodigoBarras($codigoBarras);
            $codigoBarras = substr_replace($codigoBarras, $dv, 4, 1);

            // Formata linha digitável
            $linhaDigitavel = substr($codigoBarras, 0, 4) . substr($codigoBarras, 19, 5) . '.' .
                substr($codigoBarras, 24, 10) . ' ' . substr($codigoBarras, 34, 10) . ' ' .
                substr($codigoBarras, 4, 1) . ' ' . substr($codigoBarras, 5, 14);

            // Salva os dados do pagamento
            $dadosPagamento = [
                'id_pedido' => $pedido['id_pedido'],
                'metodo' => 'boleto',
                'valor' => $valor,
                'status' => 'pendente',
                'codigo_barras' => $codigoBarras,
                'linha_digitavel' => $linhaDigitavel,
                'data_vencimento' => date('Y-m-d', strtotime('+3 days')),
                'nosso_numero' => $nossoNumero
            ];

            $this->db->table('pedidos_pagamento')->insert($dadosPagamento);

            return [
                'success' => true,
                'linha_digitavel' => $linhaDigitavel,
                'codigo_barras' => $codigoBarras,
                'vencimento' => $vencimento,
                'manual' => true // Indica que foi gerado manualmente
            ];

        } catch (\Exception $e) {
            log_message('error', 'Erro ao gerar boleto manual: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Falha ao gerar boleto bancário'
            ];
        }
    }

    protected function calculaDVCodigoBarras($codigo)
    {
        $soma = 0;
        $peso = 2;

        for ($i = strlen($codigo) - 1; $i >= 0; $i--) {
            $soma += $codigo[$i] * $peso;
            $peso = $peso === 9 ? 2 : $peso + 1;
        }

        $resto = $soma % 11;
        $dv = 11 - $resto;

        if ($dv === 0 || $dv === 10 || $dv === 11) {
            return 1;
        }

        return $dv;
    }

    protected function getTokenBB()
    {
        // Implemente a obtenção do token OAuth do Banco do Brasil
        // Normalmente isso envolve uma chamada POST com client_id e client_secret
        // Cacheie o token para evitar múltiplas chamadas

        $cache = \Config\Services::cache();
        if ($token = $cache->get('bb_oauth_token')) {
            return $token;
        }

        $client = new \GuzzleHttp\Client();
        $response = $client->post('https://oauth.bb.com.br/oauth/token', [
            'auth' => ['SEU_CLIENT_ID', 'SEU_CLIENT_SECRET'],
            'form_params' => [
                'grant_type' => 'client_credentials',
                'scope' => 'cobrancas.boletos-info cobrancas.boletos-requisicao'
            ]
        ]);

        $data = json_decode($response->getBody(), true);
        $cache->save('bb_oauth_token', $data['access_token'], $data['expires_in'] - 60);

        return $data['access_token'];
    }

    public function comprovante($idPedido)
    {
        // Gera comprovante de pagamento (PDF)
        $pedido = $this->pedidoModel->find($idPedido);

        if (!$pedido) {
            return redirect()->to('/')
                ->with('error', 'Pedido não encontrado.');
        }

        $itensPedido = $this->pedidoItemModel->where('id_pedido', $idPedido)->findAll();

        $data = [
            'pedido' => $pedido,
            'itensPedido' => $itensPedido
        ];

        // Retorna view para PDF
        return $this->renderView('pedido/comprovante', $data);
    }
}

// Função auxiliar para CRC16
function crc16($data)
{
    $crc = 0xFFFF;
    for ($i = 0; $i < strlen($data); $i++) {
        $crc ^= ord($data[$i]) << 8;
        for ($j = 0; $j < 8; $j++) {
            $crc = ($crc & 0x8000) ? (($crc << 1) ^ 0x1021) : ($crc << 1);
        }
    }
    return $crc & 0xFFFF;
}