<?php

/**
 * URL Helper para o SwapShop
 * 
 * Funções auxiliares para manipulação de URLs
 */

if (!function_exists('build_url')) {
    /**
     * Constrói uma URL com os parâmetros fornecidos
     *
     * @param array $params Parâmetros atuais
     * @param array $newParams Novos parâmetros para adicionar/sobrescrever
     * @param string $baseUrl URL base (opcional)
     * @return string URL construída
     */
    function build_url(array $params, array $newParams = [], string $baseUrl = '/produtos'): string
    {
        // Mescla os parâmetros atuais com os novos
        $allParams = array_merge($params, $newParams);
        
        // Remove parâmetros vazios
        $allParams = array_filter($allParams, function($value) {
            return $value !== null && $value !== '';
        });
        
        // Constrói a query string
        $queryString = http_build_query($allParams);
        
        return $baseUrl . ($queryString ? '?' . $queryString : '');
    }
}

if (!function_exists('asset_url')) {
    /**
     * Gera uma URL para um arquivo de asset (CSS, JS, imagem, etc)
     *
     * @param string $path Caminho do arquivo dentro da pasta pública
     * @return string URL completa para o asset
     */
    function asset_url(string $path): string
    {
        return base_url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('produto_url')) {
    /**
     * Gera uma URL para um produto
     *
     * @param string $slug Slug do produto
     * @return string URL do produto
     */
    function produto_url(string $slug): string
    {
        return base_url('produto/' . $slug);
    }
}

if (!function_exists('categoria_url')) {
    /**
     * Gera uma URL para uma categoria
     *
     * @param string $slug Slug da categoria
     * @return string URL da categoria
     */
    function categoria_url(string $slug): string
    {
        return base_url('categoria/' . $slug);
    }
} 