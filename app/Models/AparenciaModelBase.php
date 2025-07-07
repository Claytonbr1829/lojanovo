<?php

namespace App\Models;

use PDO;
use Exception;

class AparenciaModelBase extends BaseModel
{
    protected $table = 'loja_aparencia';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        'id_empresa', 'cor_primaria', 'cor_secundaria', 'cor_texto', 'cor_fundo', 
        'fonte', 'logo', 'banner_principal', 'banners', 'titulo_site', 'descricao_site', 
        'palavras_chave', 'texto_rodape', 'estilo_cabecalho', 'estilo_rodape', 
        'layout_produto', 'layout_listagem', 'created_at', 'updated_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = '';
    
    /**
     * Retorna as configurações padrão de aparência
     *
     * @return array Configurações padrão
     */
    protected function getConfiguracoesDefault(): array
    {
        return [
            'cor_primaria' => '#007bff',
            'cor_secundaria' => '#6c757d',
            'cor_texto' => '#212529',
            'cor_fundo' => '#ffffff',
            'fonte' => 'Roboto, sans-serif',
            'logo' => 'logo-default.png',
            'banner_principal' => 'banner-default.png',
            'banners' => json_encode([
                ['imagem' => 'banner1.png', 'titulo' => 'Produtos em Promoção', 'subtitulo' => 'Confira nossas ofertas por tempo limitado com descontos especiais.', 'link' => 'produtos/destaque', 'texto_botao' => 'Ver Ofertas'],
                ['imagem' => 'banner2.png', 'titulo' => 'Os Mais Vendidos', 'subtitulo' => 'Descubra os produtos favoritos entre nossos clientes.', 'link' => 'produtos/mais-vendidos', 'texto_botao' => 'Explorar'],
                ['imagem' => 'banner3.png', 'titulo' => 'Novidades', 'subtitulo' => 'Fique por dentro dos lançamentos e novidades da nossa loja.', 'link' => 'produtos/novidades', 'texto_botao' => 'Descobrir']
            ]),
            'titulo_site' => 'SwapShop',
            'descricao_site' => 'SwapShop - Sua loja virtual completa',
            'palavras_chave' => 'loja, ecommerce, produtos, compras online',
            'texto_rodape' => 'Atendemos em todo o Brasil com rapidez e segurança.',
            'estilo_cabecalho' => 'background-color: #ffffff;',
            'estilo_rodape' => 'background-color: #212529;',
            'layout_produto' => 'padrao',
            'layout_listagem' => 'grid',
            'mostrar_precos' => 1,
            'mostrar_depoimentos' => 1,
            'mostrar_contato_rodape' => 1
        ];
    }
} 