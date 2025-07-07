<?php
namespace App\Models;

use Exception;

class ConfiguracaoModelBase extends BaseModel
{
    protected $table = 'loja_config';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'id_empresa', 'nome_loja', 'descricao', 'email_contato', 
        'telefone', 'endereco', 'horario_funcionamento', 
        'mostrar_precos', 'mostrar_depoimentos', 'mostrar_contato_rodape',
        'mostrar_mais_vendidos', 'mostrar_marcas_parceiras', 'mostrar_assine_Newletter',
        'facebook_url', 'linkedin_url', 'twitter_url', 'instagram', 'youtube',
        'url', 'status', 'created_at', 'updated_at','gateway_pagamento'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'data_criacao';
    protected $updatedField = 'data_atualizacao';
    
    /**
     * Retorna as configurações padrão em caso de erro ou dados ausentes
     *
     * @return array Configurações padrão
     */
    protected function getConfiguracoesDefault(): array
    {
        return [
            'mostrar_precos' => true,
            'mostrar_depoimentos' => true,
            'cor_primaria' => '#007bff',
            'cor_secundaria' => '#6c757d',
            'cor_texto' => '#212529',
            'cor_fundo' => '#ffffff',
            'fonte' => 'Roboto, sans-serif',
            'meta_titulo' => 'SwapShop - Sua loja virtual',
            'meta_descricao' => 'SwapShop - Sua loja virtual completa',
            'logo' => 'logo-default.png',
            'banner_principal' => 'banner-default.jpg',
            'og_image' => 'og-image-default.jpg'
        ];
    }
    
    /**
     * Ajusta o brilho de uma cor hexadecimal
     *
     * @param string $hex Cor hexadecimal
     * @param int $steps Passos para ajustar (-255 a 255)
     * @return string Cor ajustada
     */
    protected function adjustBrightness(string $hex, int $steps): string
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
} 