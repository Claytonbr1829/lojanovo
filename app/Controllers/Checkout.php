<?php

namespace App\Controllers;

use App\Models\ProdutoModel;
use App\Models\ClienteModel;
use App\Models\UfModel;
use App\Models\MunicipioModel;
use App\Models\PedidoModel;
use App\Models\PedidosPagamentoModel;
use App\Models\PedidoItemModel;
use App\Models\EnderecoEntregaModel;

class Checkout extends BaseController
{
    protected $produtoModel;
    protected $clienteModel;
    protected $ufModel;
    protected $municipioModel;
    protected $pedidoModel;
    protected $pedidoItemModel;
    protected $pedidosPagamentoModel;
    protected $enderecoEntregaModel;


    public function __construct()
    {
        $this->produtoModel = new ProdutoModel();
        $this->clienteModel = new ClienteModel();
        $this->ufModel = new UfModel();
        $this->municipioModel = new MunicipioModel();
        $this->pedidoModel = new PedidoModel();
        $this->pedidoItemModel = new PedidoItemModel();
        $this->pedidosPagamentoModel = new PedidosPagamentoModel();
        $this->enderecoEntregaModel = new EnderecoEntregaModel();
    }

    /**
     * Exibe a página de checkout
     */
    public function index()
    {
        // Verifica se há itens no carrinho
        if (!session()->has('carrinho') || empty(session()->get('carrinho'))) {
            if ($pedidoTemp = session()->getTempdata('ultimo_pedido')) {
                return $this->recarregarCheckoutComPedido($pedidoTemp);
            }

            return redirect()->to('/carrinho')->with('error', 'Seu carrinho está vazio.');
        }

        $carrinho = session()->get('carrinho');
        $total = 0;
        $itens = [];

        foreach ($carrinho as $idProduto => $item) {
            $produto = $this->produtoModel->find($idProduto);

            if ($produto) {
                $preco = (float) $item['preco']; // Usa o preço do carrinho (pode ter promoção)
                $qtd = (int) $item['quantidade'];
                $subtotal = $preco * $qtd;
                $total += $subtotal;

                $itens[] = [
                    'produto' => $produto,
                    'quantidade' => $qtd,
                    'preco' => $preco,
                    'subtotal' => $subtotal
                ];
            }
        }

        // Cliente
        $cliente = null;
        $municipios = [];
        $endereco_preenchido = null;
        $enderecoEntrega = null;
        $clienteSessao = session()->get('cliente');

        if ($clienteSessao) {
            // Obter ID do cliente
            $idCliente = is_array($clienteSessao) ?
                ($clienteSessao['id'] ?? $clienteSessao['id_cliente'] ?? null) :
                ($clienteSessao->id ?? $clienteSessao->id_cliente ?? null);

            if ($idCliente) {
                $clienteCompleto = (new ClienteModel())->find($idCliente);

                if ($clienteCompleto) {
                    $cliente = (array) $clienteCompleto;

                    // Buscar endereço de entrega mais recente
                    $enderecoEntrega = $this->enderecoEntregaModel
                        ->where('id_cliente', $idCliente)
                        ->orderBy('created_at', 'DESC')
                        ->first();

                    // Preencher campos com endereço existente ou dados do cliente
                    if ($enderecoEntrega) {
                        $endereco_preenchido = [
                            'cep' => $enderecoEntrega['cep'],
                            'logradouro' => $enderecoEntrega['logradouro'],
                            'numero' => $enderecoEntrega['numero'],
                            'complemento' => $enderecoEntrega['complemento'],
                            'bairro' => $enderecoEntrega['bairro'],
                            'municipio' => $enderecoEntrega['municipio'],
                            'estado' => $enderecoEntrega['estado'],
                            //'id_uf' => $this->getUfIdBySigla($enderecoEntrega['estado'])
                        ];
                    } else {
                        // Fallback para dados do cadastro do cliente
                        $endereco_preenchido = [
                            'cep' => $cliente['cep'] ?? '',
                            'logradouro' => $cliente['endereco'] ?? '',
                            'numero' => $cliente['numero'] ?? '',
                            'complemento' => $cliente['complemento'] ?? '',
                            'bairro' => $cliente['bairro'] ?? '',
                            'municipio' => $cliente['municipio'] ?? '',
                            'estado' => $cliente['estado'] ?? '',
                            //'id_uf' => $cliente['id_uf'] ?? ''
                        ];
                    }
                }
            }
        }

        $estados = $this->ufModel->findAll();

        $data = [
            'itens' => $itens,
            'total' => $total,
            'cliente' => $cliente,
            'estados' => $estados,
            'municipios' => $municipios,
            'endereco_preenchido' => $endereco_preenchido,
            'enderecoEntrega' => $enderecoEntrega
        ];

        // Mensagens flash
        if (session()->has('error')) {
            $data['error'] = session()->getFlashdata('error');
        }
        if (session()->has('success')) {
            $data['success'] = session()->getFlashdata('success');
        }

        return $this->renderView('checkout', $data);
    }

    public function processar()
    {
        $carrinho = session()->get('carrinho') ?? [];

        if (empty($carrinho)) {
            return redirect()->to('/carrinho')
                ->with('error', 'Seu carrinho está vazio.');
        }

        // Validação do formulário
        $rules = [
            'cep' => 'required',
            'logradouro' => 'required',
            'numero' => 'required',
            'bairro' => 'required',
            'id_municipio' => 'required',
            'id_uf' => 'required',
            'valor_total' => 'required|numeric',
            'frete_valor' => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Por favor, preencha todos os campos obrigatórios.');
        }

        // Obter todos os dados do POST
        $dados = $this->request->getPost();
        $freteValor = (float) $dados['frete_valor'];

        // Processamento do cliente
        $idCliente = session()->has('cliente')
            ? session()->get('cliente')['id']
            : $this->cadastrarNovoCliente($dados);

        // Busca informações de estado e cidade
        $estadoModel = new UfModel();
        $estado = $estadoModel->where('id_uf', $dados['id_uf'])->first();

        if (!$estado) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Estado não encontrado.');
        }
        $nomeEstado = $estado['estado'];

        // Inicializando o subtotal
        $subtotal = 0;
        $itensPedido = [];

        // Recuperando as quantidades do POST
        $quantidadesPost = $this->request->getPost('quantidade');

        if (!is_array($quantidadesPost)) {
            $quantidadesPost = [];
            foreach ($carrinho as $idProduto => $item) {
                if (isset($item['quantidade'])) {
                    $quantidadesPost[$idProduto] = $item['quantidade'];
                } else {
                    $quantidadesPost[$idProduto] = 1; // Valor padrão se a quantidade não existir
                }
            }
        }

        // Processa os itens do carrinho
        foreach ($carrinho as $idProduto => $itemCarrinho) {
            $produto = $this->produtoModel->getProduto($idProduto);
            if ($produto) {
                $preco = (float) $produto['preco'];
                $qtd = (int) $quantidadesPost[$idProduto]; // Usa o valor do POST ou o valor default
                $subtotalItem = $preco * $qtd;
                $subtotal += $subtotalItem;

                $itensPedido[] = [
                    'id_produto' => $idProduto,
                    'quantidade' => $qtd,
                    'preco_unitario' => $preco,
                    'preco_total' => $subtotalItem,
                    'nome' => $produto['nome'] ?? $idProduto,
                ];
            }
        }

        $valorTotal = $subtotal + $freteValor;

        // INÍCIO DA TRANSAÇÃO - Tudo ou nada
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // SALVAR/ATUALIZAR ENDEREÇO (apenas para clientes logados)
            if (session()->has('cliente')) {
                $dadosEndereco = [
                    'id_cliente' => $idCliente,
                    'cep' => preg_replace('/[^0-9]/', '', $dados['cep']),
                    'logradouro' => $dados['logradouro'],
                    'numero' => $dados['numero'],
                    'complemento' => $dados['complemento'] ?? null,
                    'bairro' => $dados['bairro'],
                    'municipio' => $dados['id_municipio'],
                    'estado' => $nomeEstado,
                    'principal' => 1
                ];

                // Marcar outros endereços como não principais
                $this->enderecoEntregaModel->marcarOutrosComoNaoPrincipal($idCliente);

                $enderecoExiste = $this->enderecoEntregaModel->enderecoExiste($dadosEndereco);

                if (!$enderecoExiste) {
                    $insertID = $this->enderecoEntregaModel->insert($dadosEndereco);
                    if (!$insertID) {
                        throw new \RuntimeException('Falha ao inserir endereço');
                    }
                } else {
                    $this->enderecoEntregaModel->update($enderecoExiste['id'], $dadosEndereco);
                }

            }

            // Cria o pedido com os valores corretos
            $pedido = [
                'id_pedido' => null,
                'id_produto'=> $idProduto,
                'itens' => $itensPedido,
                'valor_recebido' => 0,
                'troco' => null,
                'data' => date('Y-m-d'),
                'hora' => date('H:i:s'),
                'status_pedido' => 'pendente',
                'id_cliente' => $idCliente,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'endereco' => $dados['logradouro'],
                'cep' => $dados['cep'],
                'estado' => $nomeEstado,
                'cidade' => $dados['id_municipio'],
                'bairro' => $dados['bairro'],
                'endereco_rua' => $dados['logradouro'],
                'numero_rua' => $dados['numero'],
                'complemento' => $dados['complemento'] ?? '',
                'preco_total' => $valorTotal,
                'valor_frete' => $freteValor,
                'preco_unitario' => $preco ?? 0,
                'previsao_entrega' => $dados['frete_prazo'] ?? null,
                'tipo_frete' => $dados['frete_servico'] ?? null,
                'n_itens' => count($itensPedido),
                'soma_qtdes' => array_sum(array_column($itensPedido, 'quantidade')),
                'total_itens' => count($itensPedido),
                'total_venda_resumo' => $valorTotal,
                'token' => bin2hex(random_bytes(16)),
            ];

            // Insere o pedido principal
            $idPedido = $this->pedidoModel->insert($pedido);

            if (!$idPedido) {
                throw new \RuntimeException('Erro ao criar pedido principal');
            }

            // Insere os itens do pedido
            foreach ($itensPedido as $item) {
                $item['id_pedido'] = $idPedido;
                if (!$this->pedidoItemModel->insert($item)) {
                    throw new \RuntimeException('Erro ao inserir itens do pedido');
                }
            }

            // Insere o pagamento
            $pedidos_pagamento = [
                'id_pedido' => $idPedido,
                'data_inicial_pagamento' => date('Y-m-d'),
                'vencimento_pagamento' => date('Y-m-d', strtotime('+1 day')),
                'valor_pagamento' => $valorTotal
            ];

            if (!$this->pedidosPagamentoModel->insert($pedidos_pagamento)) {
                throw new \RuntimeException('Erro ao registrar pagamento');
            }

            // Gera e atualiza o token do pedido
            $token = $this->gerarTokenPedido($idPedido);
            $this->pedidoModel->update($idPedido, [
                'token' => $token,
                'titulo' => 'Detalhes do Pedido #' . $idPedido
            ]);

            // FINALIZA TRANSAÇÃO - Se chegou aqui sem erros, confirma tudo
            $db->transComplete();

            // Verifica se a transação foi bem sucedida
            if ($db->transStatus() === false) {
                throw new \RuntimeException('Falha na transação de banco de dados');
            }

            // Salva os dados temporários do pedido
            $pedidoData = [
                'itens' => $itensPedido,
                'endereco' => $dados,
                'frete' => [
                    'valor' => $freteValor,
                    'prazo' => $dados['frete_prazo'] ?? null,
                    'servico' => $dados['frete_servico'] ?? null
                ]
            ];
            session()->setTempdata('ultimo_pedido', $pedidoData, 3600);

            // Limpa o carrinho
            session()->remove('carrinho');

            // Redireciona para o método `index` do controller `Pagamento`
            return redirect()->to("pedido/pagar/$token")
                ->with('success', 'Pedido criado com sucesso! Complete o pagamento.');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->to('/checkout')
                ->withInput()
                ->with('error', 'Erro ao processar pedido: ' . $e->getMessage());
        }
    }

    public function salvarEndereco()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acesso não autorizado']);
        }

        $json = $this->request->getJSON(true);
        $clienteSessao = session()->get('cliente');

        if (!$clienteSessao) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Cliente não autenticado']);
        }

        // Obter ID do cliente de forma robusta
        $idCliente = is_array($clienteSessao)
            ? ($clienteSessao['id'] ?? $clienteSessao['id_cliente'] ?? null)
            : (is_object($clienteSessao)
                ? ($clienteSessao->id ?? $clienteSessao->id_cliente ?? null)
                : null);

        if (!$idCliente) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'ID do cliente não encontrado na sessão'
            ]);
        }

        // Validação dos campos obrigatórios
        $required = ['cep', 'logradouro', 'numero', 'bairro', 'municipio', 'estado'];
        foreach ($required as $field) {
            if (empty($json[$field])) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Campo obrigatório faltando: ' . $field
                ]);
            }
        }

        try {
            // Preparar dados para inserção/atualização
            $dadosEndereco = [
                'id_cliente' => $idCliente,
                'cep' => preg_replace('/[^0-9]/', '', $json['cep']),
                'logradouro' => $json['logradouro'],
                'numero' => $json['numero'],
                'complemento' => $json['complemento'] ?? null,
                'bairro' => $json['bairro'],
                'municipio' => $json['municipio'],
                'estado' => $json['estado'],
                'principal' => 1,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // PRIMEIRO: Marcar TODOS os outros endereços como não principais
            $this->enderecoEntregaModel->marcarOutrosComoNaoPrincipal($idCliente);

            // Verificar se endereço similar já existe
            $enderecoExistente = $this->enderecoEntregaModel
                ->where('id_cliente', $idCliente)
                ->where('cep', $dadosEndereco['cep'])
                ->where('logradouro', $dadosEndereco['logradouro'])
                ->where('numero', $dadosEndereco['numero'])
                ->first();

            if ($enderecoExistente) {
                // Atualizar endereço existente
                $this->enderecoEntregaModel->update($enderecoExistente['id'], $dadosEndereco);
                $message = 'Endereço atualizado com sucesso!';
            } else {
                // Inserir novo endereço
                $insertID = $this->enderecoEntregaModel->insert($dadosEndereco);
                if (!$insertID) {
                    throw new \RuntimeException('Falha ao inserir endereço');
                }
                $message = 'Endereço salvo com sucesso!';
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Erro ao salvar endereço: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Erro ao salvar endereço',
                'exception' => $e->getMessage()
            ]);
        }
    }

    public function salvarEnderecoTemp()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $dados = $this->request->getPost();
        session()->set('endereco_entrega_temp', $dados);

        return $this->response->setJSON(['success' => true]);
    }

    private function recarregarCheckoutComPedido($pedidoTemp)
    {
        $estados = $this->ufModel->findAll();
        $municipios = ['municipios' => $pedidoTemp['endereco']['id_municipio'] ?? ''];

        // Força estrutura parecida com 'produto' para cada item
        foreach ($pedidoTemp['itens'] as &$item) {
            if (!isset($item['produto']) && isset($item['nome'])) {
                $item['produto'] = ['nome' => $item['nome']];
            }
        }

        $data = [
            'itens' => $pedidoTemp['itens'],
            'total' => array_sum(array_column($pedidoTemp['itens'], 'preco_total')),
            'cliente' => session()->get('cliente') ?? [],
            'estados' => $estados,
            'municipios' => $municipios,
            'endereco_preenchido' => $pedidoTemp['endereco'],
            'frete_selecionado' => $pedidoTemp['frete'],
            'success' => session()->getFlashdata('success') ?? null,
            'error' => session()->getFlashdata('error') ?? null,
        ];

        return $this->renderView('checkout', $data);
    }

    // Método auxiliar para cadastrar novo cliente se necessário
    private function cadastrarNovoCliente($dados)
    {
        $clienteData = [
            'nome' => $dados['nome'] ?? '',
            'email' => $dados['email'] ?? '',
            'telefone' => $dados['telefone'] ?? '',
            'cpf' => $dados['cpf'] ?? '',
            'cep' => $dados['cep'] ?? '',
            'logradouro' => $dados['logradouro'] ?? '',
            'numero' => $dados['numero'] ?? '',
            'complemento' => $dados['complemento'] ?? '',
            'bairro' => $dados['bairro'] ?? '',
            'id_municipio' => $dados['id_municipio'] ?? '',
            'id_uf' => $dados['id_uf'] ?? ''
        ];

        $idCliente = (new ClienteModel())->insert($clienteData);

        if (!$idCliente) {
            throw new \RuntimeException('Erro ao cadastrar cliente.');
        }

        return $idCliente;
    }


    protected function gerarTokenPedido($idPedido)
    {
        return md5($idPedido . time() . bin2hex(random_bytes(8)));
    }

    /**
     * Retorna os estados para o formulário via AJAX
     */
    public function getEstados()
    {
        $estados = $this->ufModel->getAll();
        return $this->response->setJSON(['estado' => $estados]);
    }

    /**
     * Retorna as cidades de um estado via AJAX
     */
    public function getMunicipios($uf = null)
    {
        if (!$uf) {
            return $this->response->setJSON(['error' => 'UF não informada']);
        }

        // Permite CORS se necessário
        header('Access-Control-Allow-Origin: *');

        $municipios = $this->municipioModel->getByUf($uf);
        return $this->response->setJSON(['municipio' => $municipios]);
    }
}