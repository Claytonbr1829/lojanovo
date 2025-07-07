<?php

namespace App\Controllers;

use App\Models\MarcaParceiraModel;

class MarcasParceiras extends BaseController
{
    protected $marcaParceiraModel;

    public function __construct()
    {
        $this->marcaParceiraModel = new MarcaParceiraModel();
    }

    /**
     * Exibe a página de marcas parceiras
     */
    public function index()
    {
        try {
            // Busca todas as marcas parceiras ativas
            $marcasParceiras = $this->marcaParceiraModel->getMarcasParceiras(100);
            
            // Prepara os dados para a view
            $data = [
                'title' => 'Marcas Parceiras',
                'marcasParceiras' => $marcasParceiras,
            ];
            
            // Renderiza a view
            return $this->renderView('marcas_parceiras', $data);
            
        } catch (\Exception $e) {
            // Registra o erro no log
            log_message('error', 'Erro na página de marcas parceiras: ' . $e->getMessage());
            
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
     * Exibe os detalhes de uma marca parceira específica
     */
    public function show($id = null)
    {
        try {
            if (empty($id)) {
                return redirect()->to('/marcas-parceiras');
            }
            
            // Busca a marca parceira
            $marca = $this->marcaParceiraModel->getMarcaParceira($id);
            
            if (!$marca) {
                return redirect()->to('/marcas-parceiras')
                    ->with('error', 'Marca parceira não encontrada.');
            }
            
            // Prepara os dados para a view
            $data = [
                'title' => $marca['nome'],
                'marca' => $marca,
            ];
            
            // Renderiza a view
            return $this->renderView('marca_parceira_detalhes', $data);
            
        } catch (\Exception $e) {
            // Registra o erro no log
            log_message('error', 'Erro na página de detalhes da marca parceira: ' . $e->getMessage());
            
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
} 