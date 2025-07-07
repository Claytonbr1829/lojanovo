<?php
namespace App\Models;

use Exception;
use App\Models\ProdutoModel;

class CategoriaModelDetalhe extends CategoriaModelBase
{
    /**
     * Obtém detalhes de uma categoria específica
     *
     * @param int $id ID da categoria
     * @return array|null Detalhes da categoria ou null se não encontrada
     */
    public function getCategoria(int $id): ?array
    {
        try {
            $builder = $this->db->table($this->table . ' c');
            $builder->select('c.*, COUNT(p.id_produto) as total_produtos');
            $builder->join('produtos p', 'p.id_categoria = c.id_categoria AND p.mostrar_na_loja = 1 AND p.ativo = 1', 'left');
            $builder->where('c.id_categoria', $id);
            $builder->where('c.exibir_na_loja', 1);
            $builder->where('c.ativo', 1);
            $builder->groupBy('c.id_categoria');
            
            $query = $builder->get();
            
            if ($query->getNumRows() === 0) {
                return null;
            }
            
            return $query->getRowArray();
        } catch (Exception $e) {
            log_message('error', "Erro ao buscar categoria $id: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Obtém uma categoria pelo slug
     *
     * @param string $slug Slug da categoria
     * @return array|null Detalhes da categoria ou null se não encontrada
     */
    public function getCategoriaBySlug(string $slug): ?array
    {
        try {
            $builder = $this->db->table($this->table . ' c');
            $builder->select('c.*, COUNT(p.id_produto) as total_produtos');
            $builder->join('produtos p', 'p.id_categoria = c.id_categoria AND p.mostrar_na_loja = 1 AND p.ativo = 1', 'left');
            $builder->where('c.slug', $slug);
            $builder->where('c.exibir_na_loja', 1);
            $builder->where('c.ativo', 1);
            $builder->groupBy('c.id_categoria');
            
            $query = $builder->get();
            
            if ($query->getNumRows() === 0) {
                return null;
            }
            
            return $query->getRowArray();
        } catch (Exception $e) {
            log_message('error', "Erro ao buscar categoria pelo slug $slug: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Obtém as subcategorias de uma categoria específica
     *
     * @param int $idCategoriaPai ID da categoria pai
     * @return array Lista de subcategorias
     */
    public function getSubcategorias(int $idCategoriaPai): array
    {
        try {
            $builder = $this->builder('c');
            $builder->select('c.id_categoria, c.nome, c.descricao, c.imagem, c.exibir_na_loja, c.destaque_na_loja, c.ordem_na_loja, c.slug');
            $builder->select('(SELECT COUNT(*) FROM produtos p WHERE p.id_categoria = c.id_categoria AND p.mostrar_na_loja = 1 AND p.ativo = 1) as total_produtos');
            $builder->where('c.id_categoria_pai', $idCategoriaPai);
            $builder->where('c.exibir_na_loja', 1);
            $builder->where('c.ativo', 1);
            $builder->orderBy('c.ordem_na_loja', 'ASC');
            $builder->orderBy('c.nome', 'ASC');
            
            $query = $builder->get();
            $subcategorias = $query->getResultArray();
            
            // Adicionar ícones às subcategorias
            foreach ($subcategorias as &$subcategoria) {
                $subcategoria['icone'] = $this->obterIconeCategoria($subcategoria['nome']);
                
                // Garantir caminho de imagem ou padrão
                if (empty($subcategoria['imagem'])) {
                    $subcategoria['imagem'] = 'categoria-default.jpg';
                }
            }
            
            return $subcategorias;
        } catch (Exception $e) {
            log_message('error', "Erro ao buscar subcategorias: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Conta o número de produtos em uma categoria específica
     *
     * @param int $idCategoria ID da categoria
     * @return int Número de produtos
     */
    public function contarProdutos(int $idCategoria): int
    {
        try {
            $builder = $this->db->table('produtos p');
            $builder->where('p.id_categoria', $idCategoria);
            $builder->where('p.mostrar_na_loja', 1);
            $builder->where('p.ativo', 1);
            
            return $builder->countAllResults();
        } catch (Exception $e) {
            log_message('error', "Erro ao contar produtos da categoria: " . $e->getMessage());
            
            // Em caso de erro, procura nas categorias básicas
            foreach ($this->getCategoriasBasicas() as $categoria) {
                if ($categoria['id_categoria'] == $idCategoria) {
                    return $categoria['total_produtos'];
                }
            }
            
            return 0;
        }
    }

    public function getCategoriaComProdutos(string $slug, int $limit = 12, int $offset = 0): ?array
    {
        try {
            // Busca a categoria pelo slug
            $categoria = $this->getCategoriaBySlug($slug);
            
            if (!$categoria) {
                return null;
            }
            
            // Busca os produtos da categoria
            $produtoModel = new ProdutoModel();
            $produtos = $produtoModel->getProdutosByCategoria(
                $categoria['id_categoria'],
                $limit,
                $offset
            );
            
            // Total de produtos na categoria
            $totalProdutos = $produtoModel->countProdutosByCategoria($categoria['id_categoria']);
            
            // Adiciona produtos à categoria
            $categoria['produtos'] = $produtos;
            $categoria['total_produtos'] = $totalProdutos;
            
            return $categoria;
        } catch (Exception $e) {
            log_message('error', "Erro ao buscar categoria com produtos: " . $e->getMessage());
            return null;
        }
    }
} 