<?php

namespace App\Controllers;

use App\Models\AparenciaModel;
use App\Models\ConfiguracaoModel;

class Css extends BaseController
{
    /**
     * Gera o CSS dinâmico baseado nas configurações da loja
     */
    public function index()
    {
        try {
            // Carrega o modelo de aparência
            $aparenciaModel = new AparenciaModel();
            
            // Obtém o CSS dinâmico
            $css = $aparenciaModel->getCssDinamico(1); // ID da empresa (ajustar conforme necessário)
            
            // Define o tipo de conteúdo como CSS
            $this->response->setHeader('Content-Type', 'text/css');
            $this->response->setHeader('Cache-Control', 'public, max-age=3600'); // Cache por 1 hora
            
            // Retorna o CSS
            return $this->response->setBody($css);
            
        } catch (\Exception $e) {
            // Registra o erro no log
            log_message('error', 'Erro ao gerar CSS dinâmico: ' . $e->getMessage());
            
            // Retorna CSS vazio em caso de erro
            $this->response->setHeader('Content-Type', 'text/css');
            return $this->response->setBody('/* Erro ao gerar CSS dinâmico */');
        }
    }
    
    /**
     * Gera o CSS dinâmico para o tema escuro
     */
    public function dark()
    {
        try {
            // Define o CSS para o tema escuro
            $css = ":root {
                --cor-texto: #f8f9fa;
                --cor-fundo: #121212;
                --cor-secundaria: #2d2d2d;
            }
            
            body {
                color: var(--cor-texto);
                background-color: var(--cor-fundo);
            }
            
            .card, .footer, .bg-light {
                background-color: #1e1e1e !important;
                color: var(--cor-texto);
            }
            
            .card {
                border-color: #333;
            }
            
            .top-bar, .main-header {
                background-color: #1a1a1a;
                color: var(--cor-texto);
            }
            
            .top-bar a, .footer a {
                color: #aaa;
            }
            
            .top-bar a:hover, .footer a:hover {
                color: #fff;
            }
            
            .text-muted {
                color: #aaa !important;
            }
            
            .product-card {
                background-color: #1e1e1e;
            }
            
            .product-title a {
                color: var(--cor-texto);
            }
            
            .product-title a:hover {
                color: var(--cor-primaria);
            }
            
            .copyright {
                background-color: #111 !important;
            }";
            
            // Define o tipo de conteúdo como CSS
            $this->response->setHeader('Content-Type', 'text/css');
            $this->response->setHeader('Cache-Control', 'public, max-age=3600'); // Cache por 1 hora
            
            // Retorna o CSS
            return $this->response->setBody($css);
            
        } catch (\Exception $e) {
            // Registra o erro no log
            log_message('error', 'Erro ao gerar CSS do tema escuro: ' . $e->getMessage());
            
            // Retorna CSS vazio em caso de erro
            $this->response->setHeader('Content-Type', 'text/css');
            return $this->response->setBody('/* Erro ao gerar CSS do tema escuro */');
        }
    }
} 