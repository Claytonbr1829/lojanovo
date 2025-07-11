<?php

namespace App\Controllers;

use App\Models\ProdutoModel;
use App\Models\CategoriaModel;

class Produtos extends BaseController
{
    /**
     * Exibe todos os produtos da loja
     */
    public function index()
    {
        try {
            // Carrega os modelos necessários
            $produtoModel = new ProdutoModel();
            $categoriaModel = new CategoriaModel();

            // Obtém a página atual
            $pager = service('pager');
            $page = $this->request->getVar('page') ? (int) $this->request->getVar('page') : 1;

            // Define a quantidade de produtos por página
            $perPage = 12;

            // Obtém as opções de filtro
            $categoria = $this->request->getVar('categoria');
            $preco_min = $this->request->getVar('preco_min');
            $preco_max = $this->request->getVar('preco_max');
            $busca = $this->request->getVar('q');

            // Obtém as opções de ordenação
            $sortBy = $this->request->getVar('sort') ?? 'nome';
            $sortOrder = $this->request->getVar('order') ?? 'asc';

            // Busca os produtos com filtros
            $produtos = $produtoModel->getProdutos(
                $perPage,
                ($page - 1) * $perPage,
                $sortBy,
                $sortOrder,
                $categoria,
                $preco_min,
                $preco_max,
                $busca
            );

            // Obtém o total de produtos com os filtros
            $totalProdutos = $produtoModel->countProdutos(
                $categoria,
                $preco_min,
                $preco_max,
                $busca
            );

            // Configura o paginador
            $pager->setPath('produtos');
            $pager->makeLinks($page, $perPage, $totalProdutos);

            // Busca todas as categorias para o filtro
            $categorias = $categoriaModel->getCategorias();

            // Prepara os dados para a view
            $data = [
                'title' => 'Produtos',
                'produtos' => $produtos,
                'pager' => $pager,
                'totalProdutos' => $totalProdutos,
                'categorias' => $categorias,
                'sortBy' => $sortBy,
                'sortOrder' => $sortOrder,
                'filtros' => [
                    'categoria' => $categoria,
                    'preco_min' => $preco_min,
                    'preco_max' => $preco_max,
                    'busca' => $busca
                ]
            ];

            // Renderiza a view
            return $this->renderView('produtos', $data);

        } catch (\Exception $e) {
            // Registra o erro no log
            log_message('error', 'Erro na página de produtos: ' . $e->getMessage());

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
     * Exibe produtos em destaque
     */
    public function destaque()
    {
        try {
            // Carrega o modelo de produtos
            $produtoModel = new ProdutoModel();

            // Obtém a página atual
            $pager = service('pager');
            $page = $this->request->getVar('page') ? (int) $this->request->getVar('page') : 1;

            // Define a quantidade de produtos por página
            $perPage = 12;

            // Busca os produtos em destaque
            $produtos = $produtoModel->getProdutosDestaque(
                $perPage,
                ($page - 1) * $perPage
            );

            // Prepara os dados para a view
            $data = [
                'title' => 'Produtos em Destaque',
                'produtos' => $produtos,
                'pager' => $pager,
                'totalProdutos' => count($produtos),
                'tipoListagem' => 'destaque'
            ];

            // Renderiza a view
            return $this->renderView('produtos', $data);

        } catch (\Exception $e) {
            // Registra o erro no log
            log_message('error', 'Erro na página de produtos em destaque: ' . $e->getMessage());

            // Em ambiente de produção, mostrar uma página mais amigável
            if (ENVIRONMENT === 'production') {
                return $this->renderView('errors/html/error_exception', [
                    'title' => 'Oops! Algo deu errado',
                    'message' => 'Estamos enfrentando dificuldades técnicas. Por favor, tente novamente mais tarde.'
                ]);
            }

            // Em ambiente de desenvolvimento, lança a exceção para mostrar detalhes
            throw $e;
        }
    }

    /**
     * Exibe produtos mais vendidos
     */
    public function maisVendidos()
    {
        try {
            // Carrega o modelo de produtos
            $produtoModel = new ProdutoModel();

            // Obtém a página atual
            $pager = service('pager');
            $page = $this->request->getVar('page') ? (int) $this->request->getVar('page') : 1;

            // Define a quantidade de produtos por página
            $perPage = 12;

            // Busca os produtos mais vendidos
            $produtos = $produtoModel->getProdutosMaisVendidos(
                $perPage,
                ($page - 1) * $perPage
            );

            // Prepara os dados para a view
            $data = [
                'title' => 'Produtos Mais Vendidos',
                'produtos' => $produtos,
                'pager' => $pager,
                'totalProdutos' => count($produtos),
                'tipoListagem' => 'mais_vendidos'
            ];

            // Renderiza a view
            return $this->renderView('produtos', $data);

        } catch (\Exception $e) {
            // Registra o erro no log
            log_message('error', 'Erro na página de produtos mais vendidos: ' . $e->getMessage());

            // Em ambiente de produção, mostrar uma página mais amigável
            if (ENVIRONMENT === 'production') {
                return $this->renderView('errors/html/error_exception', [
                    'title' => 'Oops! Algo deu errado',
                    'message' => 'Estamos enfrentando dificuldades técnicas. Por favor, tente novamente mais tarde.'
                ]);
            }

            // Em ambiente de desenvolvimento, lança a exceção para mostrar detalhes
            throw $e;
        }
    }

    /**
     * Exibe produtos novos
     */
    public function novidades()
    {
        try {
            // Carrega o modelo de produtos
            $produtoModel = new ProdutoModel();

            // Obtém a página atual
            $pager = service('pager');
            $page = $this->request->getVar('page') ? (int) $this->request->getVar('page') : 1;

            // Define a quantidade de produtos por página
            $perPage = 12;

            // Busca os produtos novos
            $produtos = $produtoModel->getProdutosNovos(
                $perPage,
                ($page - 1) * $perPage
            );

            // Prepara os dados para a view
            $data = [
                'title' => 'Novidades',
                'produtos' => $produtos,
                'pager' => $pager,
                'totalProdutos' => count($produtos),
                'tipoListagem' => 'novidades'
            ];

            // Renderiza a view
            return View('produtos', $data);

        } catch (\Exception $e) {
            // Registra o erro no log
            log_message('error', 'Erro na página de novidades: ' . $e->getMessage());

            // Em ambiente de produção, mostrar uma página mais amigável
            if (ENVIRONMENT === 'production') {
                return $this->renderView('errors/html/error_exception', [
                    'title' => 'Oops! Algo deu errado',
                    'message' => 'Estamos enfrentando dificuldades técnicas. Por favor, tente novamente mais tarde.'
                ]);
            }

            // Em ambiente de desenvolvimento, lança a exceção para mostrar detalhes
            throw $e;
        }
    }

    /**
     * Busca de produtos
     */
    public function buscar()
    {
        try {
            // Obtém o termo de busca
            $busca = $this->request->getVar('q');

            // Se não houver termo de busca, redireciona para a página de produtos
            if (empty($busca)) {
                return redirect()->to('produtos');
            }

            // Carrega os modelos necessários
            $produtoModel = new ProdutoModel();
            $categoriaModel = new CategoriaModel();

            // Obtém a página atual
            $pager = service('pager');
            $page = $this->request->getVar('page') ? (int) $this->request->getVar('page') : 1;

            // Define a quantidade de produtos por página
            $perPage = 12;

            // Busca os produtos
            $produtos = $produtoModel->buscarProdutos(
                $busca,
                $perPage,
                ($page - 1) * $perPage
            );

            // Obtém o total de produtos
            $totalProdutos = count($produtos);

            // Configura o paginador
            $pager->setPath('produtos/buscar');
            $pager->makeLinks($page, $perPage, $totalProdutos);

            // Busca todas as categorias para o filtro
            $categorias = $categoriaModel->getCategorias();

            // Prepara os dados para a view
            $data = [
                'title' => 'Resultados da busca: ' . $busca,
                'produtos' => $produtos,
                'pager' => $pager,
                'totalProdutos' => $totalProdutos,
                'categorias' => $categorias,
                'filtros' => [
                    'busca' => $busca
                ]
            ];

            // Renderiza a view
            return $this->renderView('produtos', $data);

        } catch (\Exception $e) {
            // Registra o erro no log
            log_message('error', 'Erro na busca de produtos: ' . $e->getMessage());

            // Em ambiente de produção, mostrar uma página mais amigável
            if (ENVIRONMENT === 'production') {
                return $this->renderView('errors/html/error_exception', [
                    'title' => 'Oops! Algo deu errado',
                    'message' => 'Estamos enfrentando dificuldades técnicas. Por favor, tente novamente mais tarde.'
                ]);
            }

            // Em ambiente de desenvolvimento, lança a exceção para mostrar detalhes
            throw $e;
        }
    }

    public function detalhesProdutos($idproduto)
    {
        
        $produtoModel = new ProdutoModel();
        $categoriaModel = new CategoriaModel();
        

        $produto = $produtoModel->find($idproduto); // Busca apenas o produto pelo ID
        $categoria = $categoriaModel->find($produto['id_categoria']);


        if (!$produto) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Produto não encontrado.");
        }
        $data = [
            'produto' => $produto,
            'categoria' => $categoria
        ];

        return $this->renderView('detalhesProduto', $data); // envia com nome correto
    }

}