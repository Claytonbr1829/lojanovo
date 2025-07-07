<?php
namespace App\Models;

use Exception;

class ConfiguracaoModelEstilo extends ConfiguracaoModelBase
{
    /**
     * Gera o CSS dinâmico com base nas configurações da loja
     *
     * @return string CSS gerado
     */
    public function gerarCSS(): string
    {
        try {
            // Obtém as configurações de consulta
            $modelConsulta = new ConfiguracaoModelConsulta();
            $config = $modelConsulta->getConfiguracoes();
            
            $css = "
                :root {
                    --cor-primaria: " . ($config['cor_primaria'] ?? '#007bff') . ";
                    --cor-secundaria: " . ($config['cor_secundaria'] ?? '#6c757d') . ";
                    --cor-texto: " . ($config['cor_texto'] ?? '#212529') . ";
                    --cor-fundo: " . ($config['cor_fundo'] ?? '#ffffff') . ";
                    --fonte-principal: '" . ($config['fonte'] ?? 'Roboto, sans-serif') . "';
                }
                
                body {
                    font-family: var(--fonte-principal);
                    color: var(--cor-texto);
                    background-color: var(--cor-fundo);
                }
                
                .bg-primary {
                    background-color: var(--cor-primaria) !important;
                }
                
                .bg-secondary {
                    background-color: var(--cor-secundaria) !important;
                }
                
                .text-primary {
                    color: var(--cor-primaria) !important;
                }
                
                .text-secondary {
                    color: var(--cor-secundaria) !important;
                }
                
                .btn-primary {
                    background-color: var(--cor-primaria);
                    border-color: var(--cor-primaria);
                }
                
                .btn-primary:hover {
                    background-color: " . $this->adjustBrightness($config['cor_primaria'] ?? '#007bff', -15) . ";
                    border-color: " . $this->adjustBrightness($config['cor_primaria'] ?? '#007bff', -20) . ";
                }
                
                .btn-secondary {
                    background-color: var(--cor-secundaria);
                    border-color: var(--cor-secundaria);
                }
                
                .btn-secondary:hover {
                    background-color: " . $this->adjustBrightness($config['cor_secundaria'] ?? '#6c757d', -15) . ";
                    border-color: " . $this->adjustBrightness($config['cor_secundaria'] ?? '#6c757d', -20) . ";
                }
                
                .product-card {
                    border-color: " . $this->adjustBrightness($config['cor_secundaria'] ?? '#6c757d', 30) . ";
                }
                
                .discount-badge {
                    background-color: var(--cor-primaria);
                }
                
                .categories-sidebar .category-link:hover {
                    color: var(--cor-primaria);
                }
                
                .products-heading {
                    background-color: var(--cor-primaria);
                }
                
                .page-item.active .page-link {
                    background-color: var(--cor-primaria);
                    border-color: var(--cor-primaria);
                }
                
                .page-link {
                    color: var(--cor-primaria);
                }
                
                .page-link:hover {
                    color: " . $this->adjustBrightness($config['cor_primaria'] ?? '#007bff', -20) . ";
                }
            ";
            
            return $css;
        } catch (Exception $e) {
            log_message('error', "Erro ao gerar CSS dinâmico: " . $e->getMessage());
            
            // Retorna um CSS básico em caso de erro
            return "
                :root {
                    --cor-primaria: #007bff;
                    --cor-secundaria: #6c757d;
                    --cor-texto: #212529;
                    --cor-fundo: #ffffff;
                    --fonte-principal: 'Roboto, sans-serif';
                }
                
                body {
                    font-family: var(--fonte-principal);
                    color: var(--cor-texto);
                    background-color: var(--cor-fundo);
                }
            ";
        }
    }
    
    /**
     * Gera o CSS para o tema escuro
     *
     * @return string CSS gerado para o tema escuro
     */
    public function gerarCSSEscuro(): string
    {
        try {
            // Obtém as configurações de consulta
            $modelConsulta = new ConfiguracaoModelConsulta();
            $config = $modelConsulta->getConfiguracoes();
            
            // Inverter cores para tema escuro
            $corPrimaria = $config['cor_primaria'] ?? '#007bff';
            $corTexto = '#f8f9fa'; // Texto claro
            $corFundo = '#343a40'; // Fundo escuro
            
            $css = "
                :root {
                    --cor-primaria: " . $corPrimaria . ";
                    --cor-secundaria: #6c757d;
                    --cor-texto: " . $corTexto . ";
                    --cor-fundo: " . $corFundo . ";
                    --cor-fundo-card: #454d55;
                    --cor-borda: #6c757d;
                    --fonte-principal: '" . ($config['fonte'] ?? 'Roboto, sans-serif') . "';
                }
                
                body {
                    font-family: var(--fonte-principal);
                    color: var(--cor-texto);
                    background-color: var(--cor-fundo);
                }
                
                .card, .product-card {
                    background-color: var(--cor-fundo-card);
                    border-color: var(--cor-borda);
                }
                
                .navbar {
                    background-color: #212529 !important;
                }
                
                .footer {
                    background-color: #212529;
                    color: var(--cor-texto);
                }
                
                .btn-primary {
                    background-color: var(--cor-primaria);
                    border-color: var(--cor-primaria);
                }
                
                .page-item.active .page-link {
                    background-color: var(--cor-primaria);
                    border-color: var(--cor-primaria);
                }
                
                .page-link {
                    color: var(--cor-primaria);
                    background-color: var(--cor-fundo-card);
                    border-color: var(--cor-borda);
                }
                
                input, select, textarea {
                    background-color: var(--cor-fundo-card) !important;
                    color: var(--cor-texto) !important;
                    border-color: var(--cor-borda) !important;
                }
            ";
            
            return $css;
        } catch (Exception $e) {
            log_message('error', "Erro ao gerar CSS tema escuro: " . $e->getMessage());
            
            // Retorna um CSS básico em caso de erro
            return "
                :root {
                    --cor-primaria: #007bff;
                    --cor-secundaria: #6c757d;
                    --cor-texto: #f8f9fa;
                    --cor-fundo: #343a40;
                    --fonte-principal: 'Roboto, sans-serif';
                }
                
                body {
                    font-family: var(--fonte-principal);
                    color: var(--cor-texto);
                    background-color: var(--cor-fundo);
                }
            ";
        }
    }
} 