<?php
namespace App\Models;

use PDO;
use Exception;

class ProdutoModelListagem extends ProdutoModelBase
{
    protected $session;

    public function __construct()
    {
        parent::__construct();
        $this->session = \Config\Services::session();
    }

    /**
     * Busca produtos com filtros, ordenação e paginação
     *
     * @param int $limit Quantidade de produtos
     * @param int $offset Deslocamento para paginação
     * @param string $sortBy Campo para ordenação
     * @param string $sortOrder Direção da ordenação (asc/desc)
     * @param int|null $categoria ID da categoria para filtrar
     * @param float|null $precoMin Preço mínimo para filtrar
     * @param float|null $precoMax Preço máximo para filtrar
     * @param string|null $busca Termo de busca
     * @return array
     */
    public function getProdutos(
        int $limit = 12,
        int $offset = 0,
        string $sortBy = 'nome',
        string $sortOrder = 'asc',
        ?int $categoria = null,
        ?float $precoMin = null,
        ?float $precoMax = null,
        ?string $busca = null
    ): array {
        try {
            // Inicia a consulta
            $builder = $this->select('p.*, c.nome as categoria_nome')
                ->from($this->table . ' p')
                ->join('categorias_dos_produtos c', 'p.id_categoria = c.id_categoria', 'left')
                ->where('p.id_empresa', $this->idEmpresa)
                ->where('p.ativo', 1);
            
            // Aplica filtro de categoria
            if (!is_null($categoria)) {
                $builder->where('p.id_categoria', $categoria);
            }
            
            // Aplica filtro de preço mínimo
            if (!is_null($precoMin)) {
                $builder->where('p.preco >=', $precoMin);
            }
            
            // Aplica filtro de preço máximo
            if (!is_null($precoMax)) {
                $builder->where('p.preco <=', $precoMax);
            }
            
            // Aplica filtro de busca
            if (!is_null($busca)) {
                $builder->groupStart()
                    ->like('p.nome', $busca)
                    ->orLike('p.descricao', $busca)
                    ->orLike('c.nome', $busca)
                    ->groupEnd();
            }
            
            // Aplica ordenação
            $camposValidos = ['nome', 'preco', 'data_cadastro'];
            $ordemValida = ['asc', 'desc'];
            
            // Garante que os campos de ordenação são válidos
            if (!in_array($sortBy, $camposValidos)) {
                $sortBy = 'nome';
            }
            
            if (!in_array($sortOrder, $ordemValida)) {
                $sortOrder = 'asc';
            }
            
            // Mapeia campos para seus nomes completos na query
            $camposMapeados = [
                'nome' => 'p.nome',
                'preco' => 'p.preco',
                'data_cadastro' => 'p.updated_at'
            ];
            
            $builder->orderBy($camposMapeados[$sortBy], $sortOrder);
            
            // Aplica limite e offset para paginação
            $builder->limit($limit, $offset);
            
            // Executa a query
            $produtos = $builder->get()->getResultArray();
            
            return $this->formatarProdutos($produtos);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar produtos: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Conta o total de produtos com os filtros aplicados
     *
     * @param int|null $categoria ID da categoria para filtrar
     * @param float|null $precoMin Preço mínimo para filtrar
     * @param float|null $precoMax Preço máximo para filtrar
     * @param string|null $busca Termo de busca
     * @return int
     */
    public function countProdutos(
        ?int $categoria = null,
        ?float $precoMin = null,
        ?float $precoMax = null,
        ?string $busca = null
    ): int {
        try {
            // Inicia a consulta
            $builder = $this->select('COUNT(*) as total')
                ->from($this->table . ' p')
                ->join('categorias_dos_produtos c', 'p.id_categoria = c.id_categoria', 'left')
                ->where('p.id_empresa', $this->idEmpresa)
                ->where('p.ativo', 1);
            
            // Aplica filtro de categoria
            if (!is_null($categoria)) {
                $builder->where('p.id_categoria', $categoria);
            }
            
            // Aplica filtro de preço mínimo
            if (!is_null($precoMin)) {
                $builder->where('p.preco >=', $precoMin);
            }
            
            // Aplica filtro de preço máximo
            if (!is_null($precoMax)) {
                $builder->where('p.preco <=', $precoMax);
            }
            
            // Aplica filtro de busca
            if (!is_null($busca)) {
                $builder->groupStart()
                    ->like('p.nome', $busca)
                    ->orLike('p.descricao', $busca)
                    ->orLike('c.nome', $busca)
                    ->groupEnd();
            }
            
            // Executa a query
            $result = $builder->get()->getRowArray();
            
            return isset($result['total']) ? (int)$result['total'] : 0;
        } catch (\Exception $e) {
            log_message('error', 'Erro ao contar produtos: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Obtém todos os produtos ativos para a loja virtual
     *
     * @param int $limit Limite de produtos a retornar
     * @param int $offset Deslocamento para paginação
     * @return array Lista de produtos
     */
    public function getProdutosLoja($limit = 12, $offset = 0): array
    {
        try {
            $query = "
                SELECT 
                    p.id_produto,
                    p.nome,
                    p.descricao,
                    COALESCE(MIN(pc.venda_atributo), p.valor_de_venda) as preco,
                    p.arquivo as imagem,
                    p.mostrar_na_loja,
                    p.destaque_na_loja,
                    p.ordem_na_loja,
                    COALESCE(SUM(pc.quantidade_atributo), p.quantidade) as estoque,
                    CASE 
                        WHEN p.preco_promocional > 0 THEN 
                            ROUND(((COALESCE(MIN(pc.venda_atributo), p.valor_de_venda) - p.preco_promocional) / COALESCE(MIN(pc.venda_atributo), p.valor_de_venda)) * 100)
                        ELSE 0
                    END as desconto,
                    COUNT(pc.id_produto_combinado) > 0 as tem_variacoes
                FROM 
                    produtos p
                LEFT JOIN
                    produtos_combinados pc ON p.id_produto = pc.id_produto AND pc.ativo = 1
                WHERE 
                    p.mostrar_na_loja = 1
                    AND p.ativo = 1
                    AND p.id_empresa = :id_empresa
                GROUP BY
                    p.id_produto
                ORDER BY 
                    p.ordem_na_loja ASC,
                    p.nome ASC
                LIMIT :limit OFFSET :offset
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_empresa', $this->idEmpresa, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            $produtos = $stmt->fetchAll();
            
            // Garantir caminho de imagem ou padrão
            foreach ($produtos as &$produto) {
                if (empty($produto['imagem'])) {
                    $produto['imagem'] = 'produto-default.jpg';
                }
            }
            
            return $produtos;
        } catch (Exception $e) {
            error_log("Erro ao buscar produtos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtém os produtos em destaque para a loja virtual
     *
     * @param int $limit Limite de produtos a retornar
     * @return array Lista de produtos em destaque
     */
    public function getProdutosDestaque(int $limit = 8): array
    {
        try {
            $query = "
                SELECT 
                    p.id_produto,
                    p.nome,
                    p.descricao,
                    COALESCE(MIN(pc.venda_atributo), p.valor_de_venda) as preco,
                    p.arquivo as imagem,
                    p.mostrar_na_loja,
                    p.destaque_na_loja,
                    p.ordem_na_loja,
                    COALESCE(SUM(pc.quantidade_atributo), p.quantidade) as estoque,
                    CASE 
                        WHEN p.preco_promocional > 0 THEN 
                            ROUND(((COALESCE(MIN(pc.venda_atributo), p.valor_de_venda) - p.preco_promocional) / COALESCE(MIN(pc.venda_atributo), p.valor_de_venda)) * 100)
                        ELSE 0
                    END as desconto,
                    COUNT(pc.id_produto_combinado) > 0 as tem_variacoes
                FROM 
                    produtos p
                LEFT JOIN
                    produtos_combinados pc ON p.id_produto = pc.id_produto AND pc.ativo = 1
                WHERE 
                    p.destaque_na_loja = 1
                    AND p.mostrar_na_loja = 1
                    AND p.ativo = 1
                    AND p.id_empresa = :id_empresa
                GROUP BY
                    p.id_produto
                ORDER BY 
                    p.ordem_na_loja ASC,
                    p.nome ASC
                LIMIT :limit
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_empresa', $this->idEmpresa, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            $produtos = $stmt->fetchAll();
            
            // Garantir caminho de imagem ou padrão
            foreach ($produtos as &$produto) {
                if (empty($produto['imagem'])) {
                    $produto['imagem'] = 'produto-default.jpg';
                }
            }
            
            return $produtos;
        } catch (Exception $e) {
            error_log("Erro ao buscar produtos em destaque: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtém os produtos de uma determinada categoria
     *
     * @param int $idCategoria ID da categoria
     * @param int $limit Limite de produtos a retornar
     * @return array Lista de produtos da categoria
     */
    public function getProdutosPorCategoria(int $idCategoria, int $limit = 12): array
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
                    p.mostrar_na_loja,
                    p.destaque_na_loja,
                    p.ordem_na_loja,
                    COALESCE(SUM(pc.quantidade_atributo), p.quantidade) as estoque,
                    CASE 
                        WHEN p.preco_promocional > 0 THEN 
                            ROUND(((COALESCE(MIN(pc.venda_atributo), p.valor_de_venda) - p.preco_promocional) / COALESCE(MIN(pc.venda_atributo), p.valor_de_venda)) * 100)
                        ELSE 0
                    END as desconto,
                    COUNT(pc.id_produto_combinado) > 0 as tem_variacoes
                FROM 
                    produtos p
                LEFT JOIN
                    produtos_combinados pc ON p.id_produto = pc.id_produto AND pc.ativo = 1
                WHERE 
                    p.id_categoria = :id_categoria
                    AND p.mostrar_na_loja = 1
                    AND p.ativo = 1
                    AND p.id_empresa = :id_empresa
                GROUP BY
                    p.id_produto
                ORDER BY 
                    p.ordem_na_loja ASC,
                    p.nome ASC
                LIMIT :limit
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_categoria', $idCategoria, PDO::PARAM_INT);
            $stmt->bindParam(':id_empresa', $this->idEmpresa, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            $produtos = $stmt->fetchAll();
            
            // Garantir caminho de imagem ou padrão
            foreach ($produtos as &$produto) {
                if (empty($produto['imagem'])) {
                    $produto['imagem'] = 'produto-default.jpg';
                }
            }
            
            return $produtos;
        } catch (Exception $e) {
            error_log("Erro ao buscar produtos por categoria: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Busca produtos por termo
     *
     * @param string $termo Termo de busca
     * @param int $limit Limite de produtos a retornar
     * @param int $offset Deslocamento para paginação
     * @return array Lista de produtos encontrados
     */
    public function buscarProdutos($termo, $limit = 12, $offset = 0): array
    {
        try {
            $termoLike = '%' . $termo . '%';
            
            $query = "
                SELECT 
                    p.id_produto,
                    p.nome,
                    p.descricao,
                    COALESCE(MIN(pc.venda_atributo), p.valor_de_venda) as preco,
                    p.arquivo as imagem,
                    p.mostrar_na_loja,
                    p.destaque_na_loja,
                    p.ordem_na_loja,
                    COALESCE(SUM(pc.quantidade_atributo), p.quantidade) as estoque,
                    CASE 
                        WHEN p.preco_promocional > 0 THEN 
                            ROUND(((COALESCE(MIN(pc.venda_atributo), p.valor_de_venda) - p.preco_promocional) / COALESCE(MIN(pc.venda_atributo), p.valor_de_venda)) * 100)
                        ELSE 0
                    END as desconto,
                    COUNT(pc.id_produto_combinado) > 0 as tem_variacoes
                FROM 
                    produtos p
                LEFT JOIN
                    produtos_combinados pc ON p.id_produto = pc.id_produto AND pc.ativo = 1
                WHERE 
                    (p.nome LIKE :termo OR p.descricao LIKE :termo OR p.descricao_completa LIKE :termo)
                    AND p.mostrar_na_loja = 1
                    AND p.ativo = 1
                    AND p.id_empresa = :id_empresa
                GROUP BY
                    p.id_produto
                ORDER BY 
                    p.ordem_na_loja ASC,
                    p.nome ASC
                LIMIT :limit OFFSET :offset
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':termo', $termoLike, PDO::PARAM_STR);
            $stmt->bindParam(':id_empresa', $this->idEmpresa, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            $produtos = $stmt->fetchAll();
            
            // Garantir caminho de imagem ou padrão
            foreach ($produtos as &$produto) {
                if (empty($produto['imagem'])) {
                    $produto['imagem'] = 'produto-default.jpg';
                }
            }
            
            return $produtos;
        } catch (Exception $e) {
            error_log("Erro ao buscar produtos por termo: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Conta o total de produtos
     *
     * @return int Total de produtos
     */
    public function contarProdutos(): int
    {
        try {
            $query = "
                SELECT COUNT(*) as total
                FROM produtos
                WHERE mostrar_na_loja = 1
                AND ativo = 1
                AND id_empresa = :id_empresa
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_empresa', $this->idEmpresa, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetch();
            
            return (int) $resultado['total'];
        } catch (Exception $e) {
            error_log("Erro ao contar produtos: " . $e->getMessage());
            return 0;
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
            $query = "
                SELECT 
                    p.id_produto,
                    p.nome,
                    p.descricao,
                    COALESCE(MIN(pc.venda_atributo), p.valor_de_venda) as preco,
                    p.arquivo as imagem,
                    p.mostrar_na_loja,
                    p.destaque_na_loja,
                    p.ordem_na_loja,
                    COALESCE(SUM(pc.quantidade_atributo), p.quantidade) as estoque,
                    CASE 
                        WHEN p.preco_promocional > 0 THEN 
                            ROUND(((COALESCE(MIN(pc.venda_atributo), p.valor_de_venda) - p.preco_promocional) / COALESCE(MIN(pc.venda_atributo), p.valor_de_venda)) * 100)
                        ELSE 0
                    END as desconto,
                    COUNT(pc.id_produto_combinado) > 0 as tem_variacoes,
                    COUNT(pi.id_item) as vendas
                FROM 
                    produtos p
                LEFT JOIN
                    produtos_combinados pc ON p.id_produto = pc.id_produto AND pc.ativo = 1
                LEFT JOIN
                    pedidos_itens pi ON p.id_produto = pi.id_produto
                LEFT JOIN
                    pedidos pe ON pi.id_pedido = pe.id_pedido AND pe.status IN (2, 3, 4, 5) -- Pedidos pagos ou concluídos
                WHERE 
                    p.mostrar_na_loja = 1
                    AND p.ativo = 1
                    AND p.id_empresa = :id_empresa
                GROUP BY
                    p.id_produto
                ORDER BY 
                    vendas DESC,
                    p.nome ASC
                LIMIT :limit
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_empresa', $this->idEmpresa, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            $produtos = $stmt->fetchAll();
            
            // Garantir caminho de imagem ou padrão
            foreach ($produtos as &$produto) {
                if (empty($produto['imagem'])) {
                    $produto['imagem'] = 'produto-default.jpg';
                }
            }
            
            return $produtos;
        } catch (Exception $e) {
            error_log("Erro ao buscar produtos mais vendidos: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtém os produtos novos
     *
     * @param int $limit Limite de produtos a retornar
     * @return array Lista de produtos novos
     */
    public function getProdutosNovos(int $limit = 4): array
    {
        try {
            $query = "
                SELECT 
                    p.id_produto,
                    p.nome,
                    p.descricao,
                    COALESCE(MIN(pc.venda_atributo), p.valor_de_venda) as preco,
                    p.arquivo as imagem,
                    p.mostrar_na_loja,
                    p.destaque_na_loja,
                    p.ordem_na_loja,
                    COALESCE(SUM(pc.quantidade_atributo), p.quantidade) as estoque,
                    CASE 
                        WHEN p.preco_promocional > 0 THEN 
                            ROUND(((COALESCE(MIN(pc.venda_atributo), p.valor_de_venda) - p.preco_promocional) / COALESCE(MIN(pc.venda_atributo), p.valor_de_venda)) * 100)
                        ELSE 0
                    END as desconto,
                    COUNT(pc.id_produto_combinado) > 0 as tem_variacoes
                FROM 
                    produtos p
                LEFT JOIN
                    produtos_combinados pc ON p.id_produto = pc.id_produto AND pc.ativo = 1
                WHERE 
                    p.mostrar_na_loja = 1
                    AND p.ativo = 1
                    AND p.id_empresa = :id_empresa
                GROUP BY
                    p.id_produto
                ORDER BY 
                    p.created_at DESC,
                    p.nome ASC
                LIMIT :limit
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_empresa', $this->idEmpresa, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            $produtos = $stmt->fetchAll();
            
            // Garantir caminho de imagem ou padrão
            foreach ($produtos as &$produto) {
                if (empty($produto['imagem'])) {
                    $produto['imagem'] = 'produto-default.jpg';
                }
            }
            
            return $produtos;
        } catch (Exception $e) {
            error_log("Erro ao buscar produtos novos: " . $e->getMessage());
            return [];
        }
    }
} 