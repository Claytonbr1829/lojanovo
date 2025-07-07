<?php
namespace App\Models;

use Exception;

class CategoriaModelBase extends BaseModel
{
    protected $table = 'categorias_dos_produtos';
    protected $primaryKey = 'id_categoria';
    protected $returnType = 'array';
    protected $allowedFields = [
        'nome', 'descricao', 'imagem', 'exibir_na_loja', 'destaque_na_loja',
        'ordem_na_loja', 'meta_titulo', 'meta_descricao', 'ativo', 
        'id_empresa', 'slug'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    
    /**
     * Determina o ícone correspondente à categoria com base no nome
     *
     * @param string $nomeCategoria Nome da categoria
     * @return string Classe do ícone FontAwesome
     */
    protected function obterIconeCategoria(string $nomeCategoria): string
    {
        $nomeCategoria = strtolower($nomeCategoria);
        
        $mapeamentoIcones = [
            'impressos' => 'fas fa-print',
            'avulsos' => 'fas fa-copy',
            'adesivos' => 'fas fa-tag',
            'etiquetas' => 'fas fa-tags',
            'rótulos' => 'fas fa-tag',
            'cadernos' => 'fas fa-book',
            'moleskines' => 'fas fa-book-open',
            'cartões' => 'fas fa-id-card',
            'visita' => 'fas fa-id-card',
            'folders' => 'fas fa-folder-open',
            'flyers' => 'fas fa-file',
            'panfletos' => 'fas fa-file-alt',
            'folhetos' => 'fas fa-file-alt',
            'livros' => 'fas fa-book',
            'banners' => 'fas fa-image',
            'lonas' => 'fas fa-image',
            'canecas' => 'fas fa-mug-hot',
            'camisetas' => 'fas fa-tshirt',
            'brindes' => 'fas fa-gift',
            'promocionais' => 'fas fa-bullhorn',
            'venda' => 'fas fa-store',
            'editorial' => 'fas fa-newspaper',
            'convites' => 'fas fa-envelope',
            'embalagens' => 'fas fa-box-open',
            'comunicação' => 'fas fa-images',
            'visual' => 'fas fa-image',
            'arquitetura' => 'fas fa-drafting-compass',
            'engenharia' => 'fas fa-hard-hat',
            'tinta' => 'fas fa-paint-brush',
            'branca' => 'fas fa-brush'
        ];
        
        foreach ($mapeamentoIcones as $chave => $icone) {
            if (strpos($nomeCategoria, $chave) !== false) {
                return $icone;
            }
        }
        
        // Ícone padrão caso não encontre correspondência
        return 'fas fa-folder';
    }
    
    /**
     * Retorna categorias básicas para quando não há conexão com o banco
     *
     * @param int|null $idEmpresa ID da empresa (opcional)
     * @return array Lista de categorias básicas
     */
    protected function getCategoriasBasicas(?int $idEmpresa = null): array
    {
        // Se não foi informado, tenta usar o ID da empresa atual
        if ($idEmpresa === null) {
            $idEmpresa = $this->idEmpresa ?? 0;
        }
        
        return [
            [
                'id_categoria' => 1,
                'nome' => 'Impressos Avulsos',
                'descricao' => 'Impressos de alta qualidade em diversos formatos',
                'imagem' => null,
                'icone' => 'fas fa-print',
                'exibir_na_loja' => 1,
                'destaque_na_loja' => 1,
                'ordem_na_loja' => 1,
                'total_produtos' => 2,
                'id_empresa' => $idEmpresa,
                'slug' => 'impressos-avulsos',
                'ativo' => 1
            ],
            [
                'id_categoria' => 2,
                'nome' => 'Adesivos e Etiquetas',
                'descricao' => 'Adesivos, etiquetas e rótulos para diversas finalidades',
                'imagem' => null,
                'icone' => 'fas fa-tag',
                'exibir_na_loja' => 1,
                'destaque_na_loja' => 1,
                'ordem_na_loja' => 2,
                'total_produtos' => 1,
                'id_empresa' => $idEmpresa,
                'slug' => 'adesivos-e-etiquetas',
                'ativo' => 1
            ],
            [
                'id_categoria' => 3,
                'nome' => 'Cadernos',
                'descricao' => 'Cadernos personalizados para sua empresa',
                'imagem' => null,
                'icone' => 'fas fa-book',
                'exibir_na_loja' => 1,
                'destaque_na_loja' => 1,
                'ordem_na_loja' => 3,
                'total_produtos' => 1,
                'id_empresa' => $idEmpresa,
                'slug' => 'cadernos',
                'ativo' => 1
            ],
            [
                'id_categoria' => 4,
                'nome' => 'Cartões',
                'descricao' => 'Cartões de visita e outros formatos',
                'imagem' => null,
                'icone' => 'fas fa-id-card',
                'exibir_na_loja' => 1,
                'destaque_na_loja' => 0,
                'ordem_na_loja' => 4,
                'total_produtos' => 1,
                'id_empresa' => $idEmpresa,
                'slug' => 'cartoes',
                'ativo' => 1
            ],
            [
                'id_categoria' => 5,
                'nome' => 'Livros',
                'descricao' => 'Livros e publicações personalizadas',
                'imagem' => null,
                'icone' => 'fas fa-book',
                'exibir_na_loja' => 1,
                'destaque_na_loja' => 0,
                'ordem_na_loja' => 5,
                'total_produtos' => 1,
                'id_empresa' => $idEmpresa,
                'slug' => 'livros',
                'ativo' => 1
            ]
        ];
    }
} 