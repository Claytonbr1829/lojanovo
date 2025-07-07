<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProdutoModel;
use CodeIgniter\HTTP\ResponseInterface;

class MinhaConta extends BaseController
{
    protected $produtoModel;

    public function __construct() {
        $this->produtoModel = new ProdutoModel();
    }


    public function index()
    {
        // Verifica se o usuário está logado
        if (!session()->has('cliente')) {
            return redirect()->to('login')->with('error', 'Você precisa estar logado para acessar esta página');
        }

        $categorias = $this->categoriaModel->findAll();
        $produtos = $this->produtoModel->findAll();


        return $this->renderView('minhaConta', [
            'categorias' => $categorias,
            'produtos' => $produtos
        ]);
    }
}
