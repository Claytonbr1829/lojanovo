<?php 
namespace App\Controllers;

/**
 * Controlador para páginas de erro
 */
class Erro extends BaseController
{
    // Sobrescreve o construtor para evitar verificação de empresa
    public function __construct()
    {
        // Não chama o construtor pai para evitar redirecionamento
        $this->helpers = ['html', 'url'];
        $this->request = \Config\Services::request();
        $this->response = \Config\Services::response();
    }
    
    /**
     * Exibe página de erro informando que a loja não está configurada
     */
    public function loja()
    {
        // Define o status HTTP
        $this->response->setStatusCode(404);
        
        // Exibe a view de erro
        return view('erros/loja', [
            'titulo' => 'Loja não configurada',
            'mensagem' => 'Esta loja não está configurada ou não existe.',
        ]);
    }
} 