<?php
namespace App\Models;

class ProdutoModelHome extends ProdutoModelBase
{
    /**
     * Obtém produtos em destaque para a página inicial
     *
     * @param int $limit Limite de produtos a retornar
     * @return array Lista de produtos em destaque
     */
    public function getProdutosDestaque(int $limit = 8): array
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
                p.quantidade as estoque,
                (SELECT COUNT(*) FROM produtos_combinados pc WHERE pc.id_produto = p.id_produto AND pc.ativo = 1) > 0 as tem_variacoes
            ');
            $builder->where('p.mostrar_na_loja', 1);
            $builder->where('p.destaque_na_loja', 1);
            $builder->where('p.ativo', 1);
            $builder->where('p.id_empresa', $this->idEmpresa);
            $builder->orderBy('p.ordem_na_loja', 'ASC');
            $builder->orderBy('p.nome', 'ASC');
            $builder->limit($limit);
            
            $query = $builder->get();
            $produtos = $query->getResultArray();
            
            return $this->formatarProdutos($produtos);
        } catch (\Exception $e) {
            log_message('error', "Erro ao buscar produtos em destaque: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtém os produtos mais vendidos
     *
     * @param int $limit Limite de produtos a retornar
     * @return array Lista de produtos mais vendidos
     */
    public function getProdutosMaisVendidos(int $limit = 4): array
    {
        try {
            $builder = $this->db->table('pedidos_itens pi');
            $builder->select('
                p.id_produto,
                p.nome,
                p.descricao,
                p.valor_de_venda as preco,
                p.preco_promocional,
                p.arquivo as imagem,
                p.quantidade as estoque,
                SUM(pi.quantidade) as qtd_vendida,
                (SELECT COUNT(*) FROM produtos_combinados pc WHERE pc.id_produto = p.id_produto AND pc.ativo = 1) > 0 as tem_variacoes
            ');
            $builder->join('produtos p', 'pi.id_produto = p.id_produto', 'inner');
            $builder->join('pedidos pe', 'pi.id_pedido = pe.id_pedido', 'inner');
            $builder->where('p.mostrar_na_loja', 1);
            $builder->where('p.ativo', 1);
            $builder->where('p.id_empresa', $this->idEmpresa);
            $builder->where('pe.status !=', 'cancelado');
            $builder->groupBy('p.id_produto');
            $builder->orderBy('qtd_vendida', 'DESC');
            $builder->limit($limit);
            
            $query = $builder->get();
            $produtos = $query->getResultArray();
            
            return $this->formatarProdutos($produtos);
        } catch (\Exception $e) {
            log_message('error', "Erro ao buscar produtos mais vendidos: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtém os produtos mais recentes
     *
     * @param int $limit Limite de produtos a retornar
     * @return array Lista de produtos mais recentes
     */
    public function getProdutosNovos(int $limit = 4): array
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
                p.quantidade as estoque,
                (SELECT COUNT(*) FROM produtos_combinados pc WHERE pc.id_produto = p.id_produto AND pc.ativo = 1) > 0 as tem_variacoes
            ');
            $builder->where('p.mostrar_na_loja', 1);
            $builder->where('p.ativo', 1);
            $builder->where('p.id_empresa', $this->idEmpresa);
            $builder->orderBy('p.updated_at', 'DESC');
            $builder->limit($limit);
            
            $query = $builder->get();
            $produtos = $query->getResultArray();
            
            return $this->formatarProdutos($produtos);
        } catch (\Exception $e) {
            log_message('error', "Erro ao buscar produtos novos: " . $e->getMessage());
            return [];
        }
    }
} 