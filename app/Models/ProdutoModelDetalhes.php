<?php
namespace App\Models;

use PDO;
use Exception;

class ProdutoModelDetalhes extends ProdutoModelBase
{
    /**
     * Busca um produto pelo ID com suas informações detalhadas
     *
     * @param int $id ID do produto
     * @return array|null Dados do produto ou null se não encontrado
     */
    public function getById(int $id): ?array
    {
        try {
            $query = "
                SELECT 
                    p.*,
                    c.nome as categoria_nome,
                    c.slug as categoria_slug
                FROM 
                    produtos p
                LEFT JOIN
                    categorias_dos_produtos c ON p.id_categoria = c.id_categoria
                WHERE 
                    p.id_produto = :id
                    AND p.id_empresa = :id_empresa
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':id_empresa', $this->idEmpresa, PDO::PARAM_INT);
            $stmt->execute();
            
            $produto = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($produto) {
                // Adiciona informações adicionais
                $produto['imagens'] = $this->getImagensProduto($id);
                $produto['variacoes'] = $this->getVariacoesProduto($id);
                $produto['atributos'] = $this->getAtributosProduto($id);
                
                // Formata valores monetários
                $produto['preco_formatado'] = 'R$ ' . number_format($produto['valor_de_venda'], 2, ',', '.');
                $produto['preco_promocional_formatado'] = $produto['preco_promocional'] > 0 
                    ? 'R$ ' . number_format($produto['preco_promocional'], 2, ',', '.') 
                    : '';
            }
            
            return $produto ?: null;
        } catch (Exception $e) {
            error_log("Erro ao buscar produto por ID: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Busca um produto pelo slug
     *
     * @param string $slug Slug do produto
     * @return array|null Dados do produto ou null se não encontrado
     */
    public function getBySlug(string $slug): ?array
    {
        try {
            $query = "
                SELECT 
                    p.*,
                    c.nome as categoria_nome,
                    c.slug as categoria_slug
                FROM 
                    produtos p
                LEFT JOIN
                    categorias_dos_produtos c ON p.id_categoria = c.id_categoria
                WHERE 
                    p.slug = :slug
                    AND p.id_empresa = :id_empresa
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
            $stmt->bindParam(':id_empresa', $this->idEmpresa, PDO::PARAM_INT);
            $stmt->execute();
            
            $produto = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($produto) {
                // Adiciona informações adicionais
                $produto['imagens'] = $this->getImagensProduto($produto['id_produto']);
                $produto['variacoes'] = $this->getVariacoesProduto($produto['id_produto']);
                $produto['atributos'] = $this->getAtributosProduto($produto['id_produto']);
                
                // Formata valores monetários
                $produto['preco_formatado'] = 'R$ ' . number_format($produto['valor_de_venda'], 2, ',', '.');
                $produto['preco_promocional_formatado'] = $produto['preco_promocional'] > 0 
                    ? 'R$ ' . number_format($produto['preco_promocional'], 2, ',', '.') 
                    : '';
            }
            
            return $produto ?: null;
        } catch (Exception $e) {
            error_log("Erro ao buscar produto por slug: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Obtém as imagens adicionais de um produto
     *
     * @param int $idProduto ID do produto
     * @return array Lista de imagens do produto
     */
    public function getImagensProduto(int $idProduto): array
    {
        try {
            $query = "
                SELECT *
                FROM produtos_imagens
                WHERE id_produto = :id_produto
                ORDER BY ordem ASC
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_produto', $idProduto, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erro ao buscar imagens do produto: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtém as variações de um produto
     *
     * @param int $idProduto ID do produto
     * @return array Lista de variações do produto
     */
    public function getVariacoesProduto(int $idProduto): array
    {
        try {
            $query = "
                SELECT *
                FROM produtos_combinados
                WHERE id_produto = :id_produto AND ativo = 1
                ORDER BY id_atributo_pai ASC, id_atributo_filho ASC
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_produto', $idProduto, PDO::PARAM_INT);
            $stmt->execute();
            
            $variacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Formata valores monetários
            foreach ($variacoes as &$variacao) {
                $variacao['preco_formatado'] = 'R$ ' . number_format($variacao['venda_atributo'], 2, ',', '.');
            }
            
            return $variacoes;
        } catch (Exception $e) {
            error_log("Erro ao buscar variações do produto: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtém os atributos de um produto
     *
     * @param int $idProduto ID do produto
     * @return array Lista de atributos do produto
     */
    public function getAtributosProduto(int $idProduto): array
    {
        try {
            $query = "
                SELECT 
                    a.id_atributo, a.nome, a.tipo,
                    pa.valor
                FROM 
                    produtos_atributos pa
                JOIN 
                    atributos a ON pa.id_atributo = a.id_atributo
                WHERE 
                    pa.id_produto = :id_produto
                ORDER BY 
                    a.ordem ASC
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_produto', $idProduto, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erro ao buscar atributos do produto: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtém produtos relacionados (da mesma categoria)
     *
     * @param int $idProduto ID do produto atual
     * @param int $idCategoria ID da categoria
     * @param int $limit Limite de produtos a retornar
     * @return array Lista de produtos relacionados
     */
    public function getProdutosRelacionados(int $idProduto, int $idCategoria, int $limit = 4): array
    {
        try {
            $query = "
                SELECT 
                    p.id_produto,
                    p.nome,
                    p.descricao,
                    COALESCE(MIN(pc.venda_atributo), p.valor_de_venda) as preco,
                    p.preco_promocional,
                    p.arquivo as imagem,
                    p.slug
                FROM 
                    produtos p
                LEFT JOIN
                    produtos_combinados pc ON p.id_produto = pc.id_produto AND pc.ativo = 1
                WHERE 
                    p.id_categoria = :id_categoria
                    AND p.id_produto != :id_produto
                    AND p.mostrar_na_loja = 1
                    AND p.ativo = 1
                    AND p.id_empresa = :id_empresa
                GROUP BY
                    p.id_produto
                ORDER BY 
                    RAND()
                LIMIT :limit
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_categoria', $idCategoria, PDO::PARAM_INT);
            $stmt->bindParam(':id_produto', $idProduto, PDO::PARAM_INT);
            $stmt->bindParam(':id_empresa', $this->idEmpresa, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $this->formatarProdutos($produtos);
        } catch (Exception $e) {
            error_log("Erro ao buscar produtos relacionados: " . $e->getMessage());
            return [];
        }
    }
} 