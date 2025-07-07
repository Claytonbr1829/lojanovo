<?php

namespace App\Controllers;

use App\Models\ProdutoModel;

class Carrinho extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Exibe a página do carrinho de compras
     */
    public function index()
    {
        // Inicializa os dados do carrinho
        $carrinhoItens = [];
        $total = 0;

        try {
            // Obtém o carrinho da sessão
            $session = session();
            $carrinho = $session->get('carrinho') ?? [];

            // Se o carrinho não estiver vazio, busca os detalhes dos produtos
            if (!empty($carrinho)) {
                $produtoModel = new ProdutoModel();

                // Busca os produtos do carrinho
                foreach ($carrinho as $id => $item) {
                    // Busca o produto atualizado
                    $produto = $produtoModel->getProduto($id);

                    // Se o produto existir e estiver ativo, adiciona ao carrinho
                    if ($produto) {
                        $preco = $produto['preco_promocional'] > 0 ? $produto['preco_promocional'] : $produto['preco'];

                        $carrinhoItens[$id] = [
                            'id' => $id,
                            'nome' => $produto['nome'],
                            'slug' => $produto['slug'] ?? '',
                            'preco' => $preco,
                            'preco_formatado' => 'R$ ' . number_format($preco, 2, ',', '.'),
                            'imagem' => $produto['imagem'],
                            'quantidade' => $item['quantidade'],
                            'subtotal' => $preco * $item['quantidade'],
                            'subtotal_formatado' => 'R$ ' . number_format($preco * $item['quantidade'], 2, ',', '.'),
                            'estoque' => $produto['quantidade']
                        ];

                        // Soma ao total
                        $total += $carrinhoItens[$id]['subtotal'];
                    }
                }
            }

            // Prepara os dados para a view
            $data = [
                'title' => 'Carrinho de Compras',
                'itens' => $carrinhoItens,
                'total' => $total,
                'total_formatado' => 'R$ ' . number_format($total, 2, ',', '.')
            ];

            // Renderiza a view
            return $this->renderView('carrinho', $data);

        } catch (\Exception $e) {
            // Registra o erro no log
            log_message('error', 'Erro na página do carrinho: ' . $e->getMessage());

            // Em ambiente de produção, mostrar uma página mais amigável
            if (ENVIRONMENT === 'production') {
                // Dados para a view de erro
                $data = [
                    'title' => 'Oops! Algo deu errado',
                    'message' => 'Estamos enfrentando dificuldades técnicas. Por favor, tente novamente mais tarde.'
                ];

                // Renderiza a view de erro
                return $this->renderView('errors/html/error_exception', $data);
            }

            // Em ambiente de desenvolvimento, lança a exceção para mostrar detalhes
            throw $e;
        }
    }

    /**
     * Adiciona um produto ao carrinho
     * 
     * @param int $id ID do produto
     * @return mixed
     */
    public function adicionar($id = null)
    {
        // Log inicial para debug
        log_message('debug', '[Carrinho/adicionar] Método iniciado. ID do produto: ' . $id);
        log_message('debug', '[Carrinho/adicionar] Headers: ' . json_encode(getallheaders()));
        log_message('debug', '[Carrinho/adicionar] ID da Empresa: ' . $this->idEmpresa);

        // Verifica se o ID foi informado
        if (empty($id)) {
            // Se for AJAX, retorna JSON
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Produto não encontrado'
                ]);
            }

            // Se não for AJAX, redireciona com mensagem de erro
            return redirect()->to('/carrinho')
                ->with('error', 'Produto não encontrado');
        }

        try {

            // Carrega o modelo de produtos com o ID da empresa atual
            $produtoModel = new ProdutoModel();

            // Garante que o ID da empresa está configurado corretamente no modelo
            if ($this->idEmpresa) {
                $produtoModel->setIdEmpresa($this->idEmpresa);
            }

            // Busca o produto
            $produto = $produtoModel->getProduto($id);

            // Log do produto encontrado (ou não)
            log_message('debug', '[Carrinho/adicionar] Produto encontrado: ' . ($produto ? 'SIM' : 'NÃO'));
            if (!$produto) {
                log_message('debug', '[Carrinho/adicionar] SQL último executado: ' . $this->db->getLastQuery());
            }

            // Verifica se o produto existe
            if (!$produto) {
                // Tenta recuperar o produto diretamente da tabela
                $builder = $this->db->table('produtos');
                $builder->select('id_produto, nome, valor_de_venda as preco, preco_promocional, arquivo as imagem, quantidade');
                $builder->where('id_produto', $id);
                $builder->where('ativo', 1);

                // Adiciona filtro por empresa se existir
                if ($this->idEmpresa) {
                    $builder->where('id_empresa', $this->idEmpresa);
                }

                $query = $builder->get();
                $produto = $query->getRowArray();

                log_message('debug', '[Carrinho/adicionar] Tentativa direta: ' . ($produto ? 'Sucesso' : 'Falha'));

                if (!$produto) {
                    // Se for AJAX, retorna JSON
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Produto não encontrado'
                        ]);
                    }

                    // Se não for AJAX, redireciona com mensagem de erro
                    return redirect()->to('/carrinho')
                        ->with('error', 'Produto não encontrado');
                }
            }

            // Define a quantidade (normalmente 1 para adições rápidas)
            $quantidade = 1;

            // Se houver um valor de quantidade na requisição, usa-o
            if ($this->request->getPost('quantidade')) {
                $quantidade = (int) $this->request->getPost('quantidade');
            }

            // Verifica estoque
            if ($produto['quantidade'] < $quantidade) {
                // Se for AJAX, retorna JSON
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Quantidade solicitada indisponível em estoque'
                    ]);
                }

                // Se não for AJAX, redireciona com mensagem de erro
                return redirect()->to('/carrinho')
                    ->with('error', 'Quantidade solicitada indisponível em estoque');
            }

            // Obtém o carrinho da sessão
            $session = session();
            $carrinho = $session->get('carrinho') ?? [];

            // Adiciona ou atualiza o produto no carrinho
            if (isset($carrinho[$id])) {
                // Verifica se a nova quantidade ultrapassa o estoque
                $novaQuantidade = $carrinho[$id]['quantidade'] + $quantidade;
                if ($novaQuantidade > $produto['quantidade']) {
                    // Se for AJAX, retorna JSON
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Quantidade total ultrapassa o estoque disponível'
                        ]);
                    }

                    // Se não for AJAX, redireciona com mensagem de erro
                    return redirect()->to('/carrinho')
                        ->with('error', 'Quantidade total ultrapassa o estoque disponível');
                }

                $carrinho[$id]['quantidade'] = $novaQuantidade;
            } else {
                $carrinho[$id] = [
                    'id' => $produto['id_produto'],
                    'nome' => $produto['nome'],
                    'preco' => $produto['preco_promocional'] > 0 ? $produto['preco_promocional'] : $produto['preco'],
                    'imagem' => $produto['imagem'] ?? 'produto-default.jpg',
                    'quantidade' => $quantidade
                ];
            }

            // Salva o carrinho na sessão
            $session->set('carrinho', $carrinho);

            // Conta o total de itens no carrinho
            $totalItens = 0;
            foreach ($carrinho as $item) {
                $totalItens += $item['quantidade'];
            }

            // Atualiza a contagem total do carrinho na sessão
            $session->set('carrinho_quantidade', $totalItens);

            log_message('debug', '[Carrinho/adicionar] Produto adicionado com sucesso. Total de itens: ' . $totalItens);

            // Se for AJAX, retorna JSON
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Produto adicionado ao carrinho com sucesso',
                    'cartCount' => $totalItens
                ]);
            }

            // Se não for AJAX, redireciona para o carrinho
            return redirect()->to('/carrinho')
                ->with('success', 'Produto adicionado ao carrinho com sucesso');

        } catch (\Exception $e) {
            // Registra o erro no log
            log_message('error', '[Carrinho/adicionar] Erro: ' . $e->getMessage());
            log_message('error', '[Carrinho/adicionar] Stack trace: ' . $e->getTraceAsString());

            // Se for AJAX, retorna JSON
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Erro ao adicionar produto ao carrinho: ' . $e->getMessage()
                ]);
            }

            // Se não for AJAX, redireciona com mensagem de erro
            return redirect()->to('/carrinho')
                ->with('error', 'Ocorreu um erro ao adicionar o produto ao carrinho.');
        }
    }

    /**
     * Atualiza a quantidade de um item no carrinho
     * 
     * @return mixed
     */
    public function atualizar()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('carrinho');
        }

        // Inicializa a resposta padrão
        $response = [
            'success' => false,
            'message' => 'Erro desconhecido',
            'data' => null
        ];

        try {
            $id = (int) $this->request->getPost('id');
            $quantidade = (int) $this->request->getPost('quantidade');

            // Validação básica
            if ($id <= 0 || $quantidade < 0) {
                $response['message'] = 'Parâmetros inválidos';
                return $this->response->setJSON($response);
            }

            $produtoModel = new ProdutoModel();
            $produto = $produtoModel->getProduto($id);

            if (!$produto) {
                $response['message'] = 'Produto não encontrado';
                return $this->response->setJSON($response);
            }

            // Verificação de estoque (ajuste o nome da coluna conforme seu banco)
            if (isset($produto['quantidade_estoque']) && $produto['quantidade_estoque'] < $quantidade) {
                $response['message'] = 'Estoque insuficiente. Disponível: ' . $produto['quantidade_estoque'];
                $response['data'] = ['estoque_disponivel' => $produto['quantidade_estoque']];
                return $this->response->setJSON($response);
            }

            $session = session();
            $carrinho = $session->get('carrinho') ?? [];

            // Verifica se o produto está no carrinho
            if (!isset($carrinho[$id])) {
                $response['message'] = 'Produto não está no carrinho';
                return $this->response->setJSON($response);
            }

            // Atualiza a quantidade
            $carrinho[$id]['quantidade'] = $quantidade;

            // Remove se quantidade for zero
            if ($quantidade <= 0) {
                unset($carrinho[$id]);
            }

            // Atualiza a sessão
            $session->set('carrinho', $carrinho);

            // Cálculos
            $subtotal = isset($carrinho[$id]) ? $carrinho[$id]['preco'] * $quantidade : 0;
            $total = array_reduce($carrinho, function ($sum, $item) {
                return $sum + ($item['preco'] * $item['quantidade']);
            }, 0);

            // Prepara a resposta de sucesso
            $response = [
                'success' => true,
                //'message' => 'Carrinho atualizado com sucesso',
                'data' => [
                    'subtotal' => number_format($subtotal, 2, ',', '.'),
                    'total' => number_format($total, 2, ',', '.'),
                    'total_itens' => count($carrinho),
                    'estoque_disponivel' => $produto['quantidade_estoque'] ?? 0,
                    'item_id' => $id
                ]
            ];

        } catch (\Exception $e) {
            log_message('error', 'Erro no carrinho: ' . $e->getMessage());
            $response['message'] = 'Erro interno ao atualizar carrinho: ' . $e->getMessage();
        }

        return $this->response->setJSON($response);
    }

    /**
     * Remove um item do carrinho
     * 
     * @param int $id ID do produto
     * @return mixed
     */
    public function remover($id = null)
    {
        // Verifica se o ID foi informado
        if (empty($id)) {
            return redirect()->to('carrinho');
        }

        try {
            // Obtém o carrinho da sessão
            $session = session();
            $carrinho = $session->get('carrinho') ?? [];

            // Verifica se o produto está no carrinho
            if (isset($carrinho[$id])) {
                // Remove o produto do carrinho
                unset($carrinho[$id]);

                // Salva o carrinho na sessão
                $session->set('carrinho', $carrinho);

                // Define mensagem de sucesso
                $session->setFlashdata('success', 'Produto removido do carrinho com sucesso.');
            }

            // Redireciona para o carrinho
            return redirect()->to('carrinho');

        } catch (\Exception $e) {
            // Registra o erro no log
            log_message('error', 'Erro ao remover produto do carrinho: ' . $e->getMessage());

            // Define mensagem de erro
            session()->setFlashdata('error', 'Ocorreu um erro ao remover o produto do carrinho.');

            // Redireciona para o carrinho
            return redirect()->to('carrinho');
        }
    }

    /**
     * Limpa todos os itens do carrinho
     * 
     * @return mixed
     */
    public function limpar()
    {
        try {
            // Limpa o carrinho da sessão
            $session = session();
            $session->remove('carrinho');

            // Define mensagem de sucesso
            $session->setFlashdata('success', 'Carrinho limpo com sucesso.');

        } catch (\Exception $e) {
            // Registra o erro no log
            log_message('error', 'Erro ao limpar carrinho: ' . $e->getMessage());

            // Define mensagem de erro
            session()->setFlashdata('error', 'Ocorreu um erro ao limpar o carrinho.');
        }

        // Redireciona para o carrinho
        return redirect()->to('carrinho');
    }
}