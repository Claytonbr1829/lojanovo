<?php

namespace App\Controllers;

use App\Models\CategoriaModel;
use App\Models\ProdutoModel;

class Categoria extends BaseController
{
    /**
     * Exibe produtos de uma categoria específica
     *
     * @param string $slug Slug da categoria
     * @return string
     */
    public function index($slug = null)
    {
        try {
            if (empty($slug)) {
                return redirect()->to('/');
            }

            $categoriaModel = new CategoriaModel();
            $produtoModel = new ProdutoModel();

            $categoria = $categoriaModel->getCategoriaBySlug($slug);


            if (!$categoria) {
                return redirect()->to('/');
            }

            $pager = \Config\Services::pager();
            $page = (int) ($this->request->getVar('page') ?? 1);
            $perPage = 12;

            $sortBy = $this->request->getVar('sort') ?? 'nome';
            $sortOrder = $this->request->getVar('order') ?? 'asc';

            $produtos = $produtoModel->getProdutosByCategoria(
                $categoria['id_categoria'],
                $perPage,
                ($page - 1) * $perPage,
                $sortBy,
                $sortOrder
            );

            $totalProdutos = $produtoModel->countProdutosByCategoria($categoria['id_categoria']);

            $pager->setPath('categoria/' . $slug);
            $pager->makeLinks($page, $perPage, $totalProdutos);

            $data = [
                'title' => $categoria['nome'],
                'categoria' => $categoria,
                'produtos' => $produtos,
                'pager' => $pager,
                'totalProdutos' => $totalProdutos,
                'sortBy' => $sortBy,
                'sortOrder' => $sortOrder
            ];


            return $this->renderview('categoria', $data);

        } catch (\Exception $e) {
            log_message('error', 'Erro na página de categoria: ' . $e->getMessage());

            throw $e;
        }
    }

}