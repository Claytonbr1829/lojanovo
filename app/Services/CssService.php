<?php

namespace App\Services;

use App\Models\ConfiguracaoModel;

class CssService
{
    private $configuracaoModel;
    
    public function __construct()
    {
        $this->configuracaoModel = new ConfiguracaoModel();
    }
    
    /**
     * Gera o CSS dinâmico com base nas configurações da loja
     *
     * @return string CSS gerado
     */
    public function gerarCss(): string
    {
        $config = $this->configuracaoModel->getConfiguracoes();
        
        return $this->gerarCssComConfiguracoes($config);
    }
    
    /**
     * Gera o CSS dinâmico com base nas configurações fornecidas
     *
     * @param array $config Configurações da loja
     * @return string CSS gerado
     */
    public function gerarCssComConfiguracoes(array $config): string
    {
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
            
            .navbar {
                background-color: var(--cor-primaria);
            }
            
            .navbar-brand, .navbar-nav .nav-link {
                color: #fff;
            }
            
            .navbar-nav .nav-link:hover {
                color: " . $this->adjustBrightness($config['cor_primaria'] ?? '#007bff', 50) . ";
            }
            
            .footer {
                background-color: var(--cor-secundaria);
                color: #fff;
            }
            
            .footer a {
                color: #fff;
            }
            
            .footer a:hover {
                color: " . $this->adjustBrightness($config['cor_secundaria'] ?? '#6c757d', 50) . ";
            }
            
            .carousel-caption {
                background-color: rgba(" . $this->hexToRgb($config['cor_primaria'] ?? '#007bff') . ", 0.7);
                color: #fff;
                padding: 15px;
                border-radius: 5px;
            }
            
            .testimonial-card {
                border-color: " . $this->adjustBrightness($config['cor_secundaria'] ?? '#6c757d', 30) . ";
            }
            
            .testimonial-text {
                color: var(--cor-texto);
            }
        ";
        
        return $css;
    }
    
    /**
     * Ajusta o brilho de uma cor hexadecimal
     *
     * @param string $hex Cor hexadecimal
     * @param int $steps Passos para ajustar (-255 a 255)
     * @return string Cor ajustada
     */
    private function adjustBrightness(string $hex, int $steps): string
    {
        // Remover o # se existir
        $hex = ltrim($hex, '#');
        
        // Converter para RGB
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        // Ajustar brilho
        $r = max(0, min(255, $r + $steps));
        $g = max(0, min(255, $g + $steps));
        $b = max(0, min(255, $b + $steps));
        
        // Converter de volta para hexadecimal
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }
    
    /**
     * Converte uma cor hexadecimal para RGB
     *
     * @param string $hex Cor hexadecimal
     * @return string Valores RGB separados por vírgula
     */
    private function hexToRgb(string $hex): string
    {
        // Remover o # se existir
        $hex = ltrim($hex, '#');
        
        // Converter para RGB
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        return "{$r}, {$g}, {$b}";
    }
} 