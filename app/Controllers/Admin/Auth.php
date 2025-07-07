<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ConfiguracaoModel;
use App\Models\LoginModel;

class Auth extends BaseController
{
    protected $configuracaoModel;

    public function __construct()
    {
        $this->configuracaoModel = new ConfiguracaoModel();
    }

    /**
     * Exibe o formulário de login
     */
    public function index()
    {
        // Se já estiver logado, redireciona para o dashboard
        if (session()->get('admin_logado') === true) {
            return redirect()->to('/admin/dashboard')
                ->with('success', 'Você já está logado!');
        }

        // Busca as configurações da loja
        $config = $this->configuracaoModel->getConfiguracoes();

        // Prepara os dados para a view
        $data = [
            'config' => $config,
            'titulo' => 'Login - Painel Administrativo'
        ];

        // Verifica se há mensagens flash
        if (session()->has('error')) {
            $data['error'] = session()->getFlashdata('error');
        }

        if (session()->has('success')) {
            $data['success'] = session()->getFlashdata('success');
        }

        // Renderiza a view
        return view('cliente/login', $data);
    }

    /**
     * Processa o formulário de login
     */
    public function authenticate()
    {
        $email = $this->request->getPost('email');
        $senha = $this->request->getPost('senha');

        if (empty($email) || empty($senha)) {
            return redirect()->to('/admin/login')
                ->with('error', 'Por favor, preencha todos os campos!');
        }

        $loginModel = new LoginModel();
        $usuario = $loginModel->where('email', $email)->first();
        
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            // Login bem-sucedido
            $adminData = [
                'admin_logado' => true,
                'admin_nome' => 'Administrador',
                'admin_email' => $usuario
            ];

            session()->set($adminData);

            return redirect()->to('/admin/dashboard')
                ->with('success', 'Login realizado com sucesso!');
        } else {
            // Login falhou
            return redirect()->to('/admin/login')
                ->with('error', 'E-mail ou senha incorretos!');
        }
    }

    /**
     * Realiza o logout
     */
    public function logout()
    {
        // Limpa as variáveis de sessão relacionadas ao admin
        session()->remove('admin_logado');
        session()->remove('admin_nome');
        session()->remove('admin_email');

        return redirect()->to('/admin/login')
            ->with('success', 'Logout realizado com sucesso!');
    }
}