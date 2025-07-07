<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ConfiguracaoModel;
use App\Models\PedidoModel;
use App\Models\ClienteModel;
use App\Models\ProdutoModel;

class Dashboard extends BaseController
{
    protected $configuracaoModel;
    protected $pedidoModel;
    protected $clienteModel;
    protected $produtoModel;

    public function __construct()
    {
        $this->configuracaoModel = new ConfiguracaoModel();
        $this->pedidoModel = new PedidoModel();
        $this->clienteModel = new ClienteModel();
        $this->produtoModel = new ProdutoModel();
    }

    /**
     * Exibe o dashboard administrativo
     */
    public function index()
    {
        try {
            // Verificar autenticação
            if (!session()->get('admin_logado')) {
                return redirect()->to('/admin/login')
                    ->with('error', 'Você precisa fazer login para acessar esta página.');
            }

            // Busca as configurações da loja
            $config = $this->configuracaoModel->getConfiguracoes();
            
            // Busca dados para o dashboard
            $totalPedidos = $this->pedidoModel->getTotalPedidos();
            $totalClientes = $this->clienteModel->getTotalClientes();
            $totalProdutos = $this->produtoModel->getTotalProdutos();
            $pedidosRecentes = $this->pedidoModel->getPedidosRecentes(5);
            
            // Prepara os dados para a view
            $data = [
                'config' => $config,
                'titulo' => 'Dashboard',
                'totalPedidos' => $totalPedidos,
                'totalClientes' => $totalClientes,
                'totalProdutos' => $totalProdutos,
                'pedidosRecentes' => $pedidosRecentes
            ];
            
            // Verifica se há mensagens flash
            if (session()->has('success')) {
                $data['success'] = session()->getFlashdata('success');
            }
            
            if (session()->has('error')) {
                $data['error'] = session()->getFlashdata('error');
            }
            
            // Renderiza a view
            return view('Admin/dashboard', $data);
            
        } catch (\Exception $e) {
            // Log do erro
            log_message('error', $e->getMessage());
            
            return redirect()->to('/admin/login')
                ->with('error', 'Erro ao carregar dashboard: ' . $e->getMessage());
        }
    }
} 