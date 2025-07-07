<?php

namespace App\Models;

use PDO;
use Exception;

class ProdutoModelBase extends BaseModel
{
    protected $table = 'produtos';
    protected $primaryKey = 'id_produto';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'id_empresa',
        'id_categoria',
        'nome',
        'descricao',
        'descricao_completa',
        'valor_de_custo',
        'valor_de_venda',
        'preco_promocional',
        'quantidade',
        'codigo_de_barras',
        'codigo_interno',
        'peso_bruto',
        'altura',
        'largura',
        'comprimento',
        'imagem',
        'mostrar_na_loja',
        'destaque_na_loja',
        'ordem_na_loja',
        'slug',
        'taxa_imposto',
        'ativo',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = '';

    /**
     * Constantes para status padrão
     */
    const STATUS_ATIVO = 1;
    const STATUS_INATIVO = 0;

    /**
     * Formata dados comuns de produtos
     * 
     * @param array $produtos Lista de produtos para formatar
     * @return array Produtos formatados
     */
    protected function formatarProdutos(array $produtos): array
    {
       
        foreach ($produtos as &$produto) {
            // Garantir que id_produto existe
            if (!isset($produto['id_produto'])) {
                $produto['id_produto'] = 0;
            }
            
            // Gerar slug a partir do nome do produto
            $produto['slug'] = url_title($produto['nome'] ?? '', '-', true);
            
            // // Garantir caminho de imagem ou padrão
            // if (empty($produto['arquivo'])) {
            //     $produto['arquivo'] = 'produto-default.jpg';
            // }

            // Formatar valores monetários
            $produto['preco'] = $produto['preco'] ?? 0;
            $produto['preco_formatado'] = 'R$ ' . number_format($produto['preco'], 2, ',', '.');
           
            // Tratar preço promocional e calcular desconto
            if (!empty($produto['preco_promocional']) && $produto['preco_promocional'] > 0) {
                $produto['preco_promocional_formatado'] = 'R$ ' . number_format($produto['preco_promocional'], 2, ',', '.');
                // Adiciona preço antigo para exibição em promoções
                $produto['preco_antigo'] = $produto['preco'];
                $produto['preco_antigo_formatado'] = $produto['preco_formatado'];
                $produto['preco'] = $produto['preco_promocional'];
                $produto['preco_formatado'] = $produto['preco_promocional_formatado'];

                // Calcula o desconto
                if ($produto['preco_antigo'] > 0) {
                    $produto['desconto'] = round((($produto['preco_antigo'] - $produto['preco']) / $produto['preco_antigo']) * 100);
                } else {
                    $produto['desconto'] = 0;
                }
            } else {
                // Garantir que os campos de promoção existam mesmo quando não há promoção
                $produto['preco_promocional'] = 0;
                $produto['preco_promocional_formatado'] = '';
                $produto['preco_antigo'] = 0;
                $produto['preco_antigo_formatado'] = '';
                $produto['desconto'] = 0;
            }
        }

        return $produtos;
    }

    // public function getProdutosByCategoria(int $id_categoria, $limit = 12, $offset = 0, $sortBy = 'nome', $sortOrder = 'asc')
    // {
    //     return $this->select('produtos.*, categorias.nome as categoria_nome')
    //         ->join('categorias', 'categorias.id_categoria = produtos.id_categoria')
    //         ->where('produtos.id_categoria', $id_categoria)
    //         ->where('produtos.ativo', 1) // caso tenha controle de ativação
    //         ->orderBy($sortBy, $sortOrder)
    //         ->findAll($limit, $offset);
    // }

}