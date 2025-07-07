<?php

namespace App\Controllers;

use App\Models\ProdutoModel;
use App\Models\CategoriaModel;
use App\Models\AvaliacaoModel;

class Produto extends BaseController
{
    /**
     * Exibe a página de detalhes de um produto específico
     *
     * @param string $slug Slug do produto
     * 
     */
    public function index($slug = null)
    {
        try {
            // Se não foi fornecido um slug, redireciona para a página de produtos
            if (empty($slug)) {
                return redirect()->to('produtos');
            }
            
            // Carrega os modelos necessários
            $produtoModel = new ProdutoModel();
            $categoriaModel = new CategoriaModel();
            
            // Busca o produto pelo slug
            $produto = $produtoModel->getProdutoBySlug($slug);
            
            // Se o produto não for encontrado, exibe uma página de erro 404
            if (empty($produto)) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Produto não encontrado');
            }
            
            // Incrementa o contador de visualizações
            $produtoModel->incrementarVisualizacoes($produto['id_produto']);
            
            // Busca a categoria do produto apenas se houver um id_categoria válido
            $categoria = null;
            if (!empty($produto['id_categoria'])) {
                $categoria = $categoriaModel->getCategoria((int)$produto['id_categoria']);
            }
            
            // Se a categoria não for encontrada, cria um array com valores padrão
            if (empty($categoria)) {
                $categoria = [
                    'id_categoria' => 0,
                    'nome' => 'Sem categoria',
                    'slug' => 'sem-categoria'
                ];
            }
            
            // Busca produtos relacionados apenas se houver uma categoria válida
            $produtosRelacionados = [];
            if (!empty($produto['id_categoria'])) {
                $produtosRelacionados = $produtoModel->getProdutosRelacionados($produto['id_produto'], $produto['id_categoria'], 4);
            }
            
            // Verifica se o produto está no carrinho
            $session = session();
            $carrinho = $session->get('carrinho') ?? [];
            $noCarrinho = false;
            
            foreach ($carrinho as $item) {
                if ($item['id_produto'] == $produto['id_produto']) {
                    $noCarrinho = true;
                    break;
                }
            }
            
            // Prepara os dados para a view
            $data = [
                'title' => $produto['nome'],
                'produto' => $produto,
                'categoria' => $categoria,
                'produtosRelacionados' => $produtosRelacionados,
                'noCarrinho' => $noCarrinho
            ];
            
            // Verifica se existe modelo de avaliações
            if (class_exists('\App\Models\AvaliacaoModel')) {
                $avaliacaoModel = new AvaliacaoModel();
                $avaliacoes = $avaliacaoModel->getAvaliacoesProduto($produto['id_produto']);
                $mediaAvaliacoes = $avaliacaoModel->getMediaAvaliacoesProduto($produto['id_produto']);
                
                $data['avaliacoes'] = $avaliacoes;
                $data['mediaAvaliacoes'] = $mediaAvaliacoes;
                $data['totalAvaliacoes'] = count($avaliacoes);
            }
            
            // Renderiza a view
            return $this->renderView('produto', $data);
            
        } catch (\CodeIgniter\Exceptions\PageNotFoundException $e) {
            throw $e; // Repassar exceção de página não encontrada
        } catch (\Exception $e) {
            // Registra o erro no log
            log_message('error', 'Erro na página do produto: ' . $e->getMessage());
            
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
    public function adicionarAoCarrinho($id = null)
    {
        // Verifica se é uma requisição AJAX
        if ($this->request->isAJAX()) {
            try {
                // Obtém os dados da requisição
                $quantidade = $this->request->getPost('quantidade') ?? 1;
                
                // Verifica se o ID foi informado
                if (empty($id)) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Produto não encontrado'
                    ]);
                }
                
                // Carrega o modelo de produtos
                $produtoModel = new ProdutoModel();
                
                // Busca o produto
                $produto = $produtoModel->getProduto($id);
                
                // Verifica se o produto existe
                if (!$produto) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Produto não encontrado'
                    ]);
                }
                
                // Verifica estoque (simplificado)
                if ($produto['quantidade'] < $quantidade) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Quantidade solicitada indisponível em estoque'
                    ]);
                }
                
                // Inicia a sessão de carrinho se não existir
                $session = session();
                $carrinho = $session->get('carrinho') ?? [];
                
                // Adiciona ou atualiza o produto no carrinho
                if (isset($carrinho[$id])) {
                    $carrinho[$id]['quantidade'] += $quantidade;
                } else {
                    $carrinho[$id] = [
                        'id' => $produto['id_produto'],
                        'nome' => $produto['nome'],
                        'preco' => $produto['preco_promocional'] > 0 ? $produto['preco_promocional'] : $produto['preco'],
                        'imagem' => $produto['arquivo'],
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
                
                // Retorna o sucesso
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Produto adicionado ao carrinho com sucesso',
                    'total_itens' => $totalItens
                ]);
                
            } catch (\Exception $e) {
                // Registra o erro no log
                log_message('error', 'Erro ao adicionar produto ao carrinho: ' . $e->getMessage());
                
                // Retorna erro
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Erro ao adicionar produto ao carrinho'
                ]);
            }
        } else {
            // Se não for AJAX, redireciona para a página do produto
            return redirect()->to('produto/' . $id);
        }
    }

    /**
     * Adiciona uma avaliação para o produto
     *
     * @param int $id_produto ID do produto
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function avaliar($id_produto = null)
    {
        try {
            // Verifica se o produto existe
            $produtoModel = new ProdutoModel();
            $produto = $produtoModel->getProduto($id_produto);
            
            if (empty($produto)) {
                return redirect()->to('produtos')->with('error', 'Produto não encontrado');
            }
            
            // Verifica se o modelo de avaliações existe
            if (!class_exists('\App\Models\AvaliacaoModel')) {
                return redirect()->to('produto/' . $produto['slug'])->with('error', 'Sistema de avaliações não disponível');
            }
            
            // Obtém os dados do formulário
            $avaliacao = $this->request->getPost('avaliacao');
            $comentario = $this->request->getPost('comentario');
            $nome = $this->request->getPost('nome');
            $email = $this->request->getPost('email');
            
            // Valida os dados
            $validation = \Config\Services::validation();
            $validation->setRules([
                'avaliacao' => 'required|integer|greater_than[0]|less_than_equal_to[5]',
                'comentario' => 'required|min_length[10]|max_length[500]',
                'nome' => 'required|min_length[3]|max_length[100]',
                'email' => 'required|valid_email|max_length[100]'
            ]);
            
            if (!$validation->withRequest($this->request)->run()) {
                return redirect()->back()->withInput()->with('errors', $validation->getErrors());
            }
            
            // Verifica se o usuário já avaliou este produto
            $session = session();
            $usuario_id = $session->get('usuario')['id_usuario'] ?? null;
            
            // Salva a avaliação
            $avaliacaoModel = new AvaliacaoModel();
            $dados = [
                'id_produto' => $id_produto,
                'id_usuario' => $usuario_id,
                'nome' => $nome,
                'email' => $email,
                'avaliacao' => $avaliacao,
                'comentario' => $comentario,
                'data_avaliacao' => date('Y-m-d H:i:s'),
                'ip' => $this->request->getIPAddress(),
                'status' => 'P' // P = Pendente de aprovação
            ];
            
            $sucesso = $avaliacaoModel->insert($dados);
            
            // Redireciona com mensagem apropriada
            if ($sucesso) {
                return redirect()->to('produto/' . $produto['slug'])->with('success', 'Sua avaliação foi enviada com sucesso e está aguardando aprovação.');
            } else {
                return redirect()->to('produto/' . $produto['slug'])->with('error', 'Não foi possível enviar sua avaliação. Por favor, tente novamente.');
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Erro ao avaliar produto: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ocorreu um erro ao processar sua avaliação. Por favor, tente novamente.');
        }
    }
} 