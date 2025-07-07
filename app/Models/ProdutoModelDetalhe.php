<?php
namespace App\Models;

class ProdutoModelDetalhe extends ProdutoModelBase
{
    /**
     * Obtém um produto específico pelo ID
     *
     * @param int $id ID do produto
     * @return array|null Dados do produto ou null se não encontrar
     */
    public function getProduto($id): ?array
    {
        try {
            $builder = $this->db->table('produtos p');
            $builder->select("
                p.id_produto,
                p.nome,
                p.descricao,
                p.valor_de_venda as preco,
                p.preco_promocional,
                p.arquivo as imagem,
                p.quantidade,
                p.id_categoria,
                p.peso_bruto,
                p.largura,
                p.altura,
                p.comprimento,
                p.meta_titulo,
                p.meta_descricao,
                p.meta_palavras_chave,
                CASE 
                    WHEN p.preco_promocional > 0 THEN 
                        ROUND(((p.valor_de_venda - p.preco_promocional) / p.valor_de_venda) * 100)
                    ELSE 0
                END as desconto,
                c.nome as categoria_nome,
                (SELECT COUNT(*) FROM produtos_combinados pc WHERE pc.id_produto = p.id_produto AND pc.ativo = 1) > 0 as tem_variacoes,
                (SELECT pc.sku_atributo FROM produtos_combinados pc WHERE pc.id_produto = p.id_produto AND pc.ativo = 1 LIMIT 1) as sku
            ");
            $builder->join('categorias_dos_produtos c', 'p.id_categoria = c.id_categoria', 'left');
            $builder->where('p.id_produto', $id);
            $builder->where('p.ativo', 1);
            $builder->where('p.id_empresa', $this->idEmpresa);
            
            $query = $builder->get();
            $produto = $query->getRowArray();

            
            if ($produto) {
                // Garantir caminho de imagem ou padrão
                // if (empty($produto['imagem'])) {
                //     $produto['imagem'] = 'produto-default.jpg';
                // }
                
                // Garantir que id_categoria seja inteiro ou null
                $produto['id_categoria'] = !empty($produto['id_categoria']) ? (int)$produto['id_categoria'] : null;
                
                // Formatar valores monetários
                $produto['preco_formatado'] = 'R$ ' . number_format($produto['preco'], 2, ',', '.');
                if (!empty($produto['preco_promocional']) && $produto['preco_promocional'] > 0) {
                    $produto['preco_promocional_formatado'] = 'R$ ' . number_format($produto['preco_promocional'], 2, ',', '.');
                    $produto['preco_antigo'] = $produto['preco'];
                    $produto['preco_antigo_formatado'] = $produto['preco_formatado'];
                    $produto['preco'] = $produto['preco_promocional'];
                    $produto['preco_formatado'] = $produto['preco_promocional_formatado'];
                }
                
                // Para manter compatibilidade
                $produto['estoque'] = $produto['quantidade'];
                $produto['peso'] = $produto['peso_bruto'];
                
                return $produto;
            }
            
            return null;
        } catch (\Exception $e) {
            log_message('error', "Erro ao buscar produto ID $id: " . $e->getMessage());
            return null;
        }
    }
    
    
    /**
     * Obtém um produto pelo seu slug ou ID
     *
     * @param string $identificador Slug ou ID do produto
     * @return array|null Dados do produto ou null se não encontrar
     */
    public function getProdutoBySlug(string $identificador): ?array
    {
        try {
            $builder = $this->db->table('produtos p');
            $builder->select('
                p.id_produto,
                p.nome,
                p.descricao,
                p.valor_de_venda as preco,
                p.preco_promocional,
                p.arquivo as imagem,
                p.quantidade,
                p.id_categoria,
                p.peso_bruto,
                p.largura,
                p.altura,
                p.comprimento,
                CASE 
                    WHEN p.preco_promocional > 0 THEN 
                        ROUND(((p.valor_de_venda - p.preco_promocional) / p.valor_de_venda) * 100)
                    ELSE 0
                END as desconto,
                c.nome as categoria_nome,
                c.id_categoria,
                IFNULL((SELECT COUNT(*) FROM produtos_combinados pc WHERE pc.id_produto = p.id_produto AND pc.ativo = 1), 0) > 0 as tem_variacoes,
                (SELECT pc.sku_atributo FROM produtos_combinados pc WHERE pc.id_produto = p.id_produto AND pc.ativo = 1 LIMIT 1) as sku
            ');
            $builder->join('categorias_dos_produtos c', 'p.id_categoria = c.id_categoria', 'left');
            
            // Verifica se o identificador é um número (ID) ou string (slug)
            if (is_numeric($identificador)) {
                $builder->where('p.id_produto', $identificador);
            } else {
                // Aqui você pode implementar a lógica de busca por slug quando tiver o campo
                $builder->where('p.id_produto', $identificador);
            }
            
            $builder->where('p.ativo', 1);
            $builder->where('p.id_empresa', $this->idEmpresa);
            
            $query = $builder->get();
            $produto = $query->getRowArray();
            
            if ($produto) {
                // Garantir caminho de imagem ou padrão
                if (empty($produto['imagem'])) {
                    $produto['imagem'] = 'produto-default.jpg';
                }
                
                // Garantir que id_categoria seja inteiro ou null
                $produto['id_categoria'] = !empty($produto['id_categoria']) ? (int)$produto['id_categoria'] : null;
                
                // Formatar valores monetários
                $produto['preco_formatado'] = 'R$ ' . number_format($produto['preco'], 2, ',', '.');
                if (!empty($produto['preco_promocional']) && $produto['preco_promocional'] > 0) {
                    $produto['preco_promocional_formatado'] = 'R$ ' . number_format($produto['preco_promocional'], 2, ',', '.');
                    $produto['preco_antigo'] = $produto['preco'];
                    $produto['preco_antigo_formatado'] = $produto['preco_formatado'];
                    $produto['preco'] = $produto['preco_promocional'];
                    $produto['preco_formatado'] = $produto['preco_promocional_formatado'];
                }
                
                // Para manter compatibilidade
                $produto['estoque'] = $produto['quantidade'];
                $produto['peso'] = $produto['peso_bruto'];
                
                return $produto;
            }
            
            return null;
        } catch (\Exception $e) {
            log_message('error', "Erro ao buscar produto por slug $identificador: " . $e->getMessage());
            return null;
        }
    }
} 