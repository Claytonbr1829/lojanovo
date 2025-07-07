<?php
namespace App\Models;

class ProdutoModelCategoria extends ProdutoModelBase
{
    /**
     * Obtém produtos de uma categoria específica
     *
     * @param int $idCategoria ID da categoria
     * @param int $limit Limite de produtos por página
     * @param int $offset Offset para paginação
     * @param string $sortBy Campo para ordenação
     * @param string $sortOrder Direção da ordenação (asc/desc)
     * @return array Lista de produtos da categoria
     */
    public function getProdutosByCategoria($id_categoria, $limit = 12, $offset = 0, $sortBy = 'nome', $sortOrder = 'asc'): array
    {
        try {
            // Verifica campos válidos para ordenação
            $validSortFields = ['nome', 'preco', 'mais_vendidos', 'mais_recentes'];
            
            if (!in_array($sortBy, $validSortFields)) {
                $sortBy = 'nome';
            }
            
            // Verifica direção válida
            $sortOrder = strtolower($sortOrder) === 'desc' ? 'DESC' : 'ASC';
            $builder = $this->db->table('produtos p');
            
            $builder->select('
                p.id_produto,
                p.nome,
                p.descricao,
                p.valor_de_venda as preco,
                p.preco_promocional,
                p.arquivo as imagem,
                p.quantidade as estoque,
                p.url_amigavel as slug,
                CASE 
                    WHEN p.preco_promocional > 0 THEN 
                        ROUND(((p.valor_de_venda - p.preco_promocional) / p.valor_de_venda) * 100)
                    ELSE 0
                END as desconto,
                (SELECT COUNT(*) FROM produtos_combinados pc WHERE pc.id_produto = p.id_produto AND pc.ativo = 1) > 0 as tem_variacoes
            ');

            $builder->where('p.mostrar_na_loja', 1);
            $builder->where('p.ativo', 1);
            $builder->where('p.id_empresa', $this->idEmpresa);
            $builder->where('p.id_categoria', $id_categoria);
            

            // Configura a ordenação
            switch ($sortBy) {
                case 'preco':
                    $builder->orderBy('CASE WHEN p.preco_promocional > 0 THEN p.preco_promocional ELSE p.valor_de_venda END', $sortOrder);
                    break;
                case 'mais_vendidos':
                    $builder->select('(SELECT COALESCE(SUM(pi.quantidade), 0) FROM pedidos_itens pi JOIN pedidos pe ON pi.id_pedido = pe.id_pedido WHERE pi.id_produto = p.id_produto AND pe.status != "cancelado") as total_vendas');
                    $builder->orderBy('total_vendas', 'DESC');
                    break;
                case 'mais_recentes':
                    $builder->orderBy('p.updated_at', 'DESC');
                    break;
                default:
                    $builder->orderBy('p.nome', $sortOrder);
            }

            $builder->limit($limit, $offset);
            
            // echo $builder->getCompiledSelect();
            $query = $builder->get();
            $produtos = $query->getResultArray();
            
            
            return $this->formatarProdutos($produtos);
        } catch (\Exception $e) {
            log_message('error', "Erro ao buscar produtos da categoria: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Conta o total de produtos em uma categoria
     *
     * @param int $idCategoria ID da categoria
     * @return int Total de produtos
     */
    public function countProdutosByCategoria($id_categoria):int
    {
        try {
            $builder = $this->db->table('produtos p');
            //$builder->join('produtos_categorias pc', 'p.id_produto = pc.id_produto', 'inner');
            $builder->where('p.mostrar_na_loja', 1);
            $builder->where('p.ativo', 1);
            $builder->where('p.id_empresa', $this->idEmpresa);
            $builder->where('p.id_categoria', $id_categoria);
            
            return $builder->countAllResults();
            
        } catch (\Exception $e) {
            log_message('error', "Erro ao contar produtos da categoria: " . $e->getMessage());
            return 0;
        }
    }
} 