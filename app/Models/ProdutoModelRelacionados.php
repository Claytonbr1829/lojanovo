<?php
namespace App\Models;

class ProdutoModelRelacionados extends ProdutoModelBase
{
    /**
     * Obtém produtos relacionados (da mesma categoria)
     *
     * @param int $idProduto ID do produto atual (para excluir da lista)
     * @param int $idCategoria ID da categoria para buscar produtos relacionados
     * @param int $limit Limite de produtos
     * @return array Lista de produtos relacionados
     */
    public function getProdutosRelacionados(int $idProduto, int $idCategoria, int $limit = 4): array
    {
        if ($idCategoria <= 0) {
            return [];
        }
        
        try {
            $builder = $this->db->table('produtos p');
            $builder->select('
                p.id_produto,
                p.nome,
                p.descricao,
                COALESCE(MIN(pc.venda_atributo), p.valor_de_venda) as preco,
                p.preco_promocional,
                p.arquivo as imagem,
                p.slug,
                CASE 
                    WHEN p.preco_promocional > 0 THEN 
                        ROUND(((p.valor_de_venda - p.preco_promocional) / p.valor_de_venda) * 100)
                    ELSE 0
                END as desconto,
                p.quantidade as estoque
            ');
            $builder->where('p.id_categoria', $idCategoria);
            $builder->where('p.id_produto !=', $idProduto);
            $builder->where('p.mostrar_na_loja', 1);
            $builder->where('p.ativo', 1);
            $builder->where('p.id_empresa', $this->idEmpresa);
            $builder->orderBy('RAND()');
            $builder->limit($limit);
            
            $query = $builder->get();
            $produtos = $query->getResultArray();
            
            return $this->formatarProdutos($produtos);
            
        } catch (\Exception $e) {
            log_message('error', "Erro ao buscar produtos relacionados: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Busca produtos relacionados a um produto
     *
     * @param int $id_produto ID do produto atual
     * @param int $id_categoria ID da categoria do produto atual
     * @param int $limit Limite de produtos
     * @return array
     */
    public function getProdutosRelacionadosCompleto(int $id_produto, int $id_categoria, int $limit = 4): array
    {
        try {
            // Busca produtos da mesma categoria, exceto o produto atual
            $produtos = $this->select('p.*, c.nome as categoria_nome')
                ->from($this->table . ' p')
                ->join('categorias_dos_produtos c', 'p.id_categoria = c.id_categoria', 'left')
                ->where('p.id_produto !=', $id_produto)
                ->where('p.id_categoria', $id_categoria)
                ->where('p.ativo', 1)
                ->where('p.id_empresa', $this->session->get('empresa')['id_empresa'])
                ->orderBy('RAND()')
                ->limit($limit)
                ->get()
                ->getResultArray();
            
            // Se não encontrou produtos suficientes, busca em categorias diferentes
            if (count($produtos) < $limit) {
                $complemento = $this->select('p.*, c.nome as categoria_nome')
                    ->from($this->table . ' p')
                    ->join('categorias_dos_produtos c', 'p.id_categoria = c.id_categoria', 'left')
                    ->where('p.id_produto !=', $id_produto)
                    ->where('p.id_categoria !=', $id_categoria)
                    ->where('p.ativo', 1)
                    ->where('p.id_empresa', $this->session->get('empresa')['id_empresa'])
                    ->orderBy('RAND()')
                    ->limit($limit - count($produtos))
                    ->get()
                    ->getResultArray();
                
                $produtos = array_merge($produtos, $complemento);
            }
            
            return $this->formatarProdutos($produtos);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar produtos relacionados: ' . $e->getMessage());
            return [];
        }
    }
} 