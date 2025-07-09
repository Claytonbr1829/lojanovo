<?php
namespace App\Models;

use Exception;

class CategoriaModelLista extends CategoriaModelBase
{
    /**
     * Obtém todas as categorias ativas para a loja virtual
     *
     * @param int $limit Limite de registros (0 = sem limite)
     * @param bool $apenasDestaque Buscar apenas categorias em destaque
     * @param int|null $idEmpresa ID da empresa (null = usa o ID atual)
     * @return array Lista de categorias
     */
    public function getCategorias(int $limit = 0, bool $apenasDestaque = false, ?int $idEmpresa = null): array
    {
        try {
            // Se não foi informado, usa o ID da empresa atual
            if ($idEmpresa === null) {
                $idEmpresa = $this->idEmpresa;
            }

            $builder = $this->db->table($this->table . ' c');
            $builder->select('c.id_categoria, c.nome, c.descricao, c.imagem, c.exibir_na_loja, c.destaque_na_loja, c.ordem_na_loja, c.slug');
            $builder->select('(SELECT COUNT(*) FROM produtos p WHERE p.id_categoria = c.id_categoria AND p.mostrar_na_loja = 1 AND p.ativo = 1 AND p.id_empresa = c.id_empresa) as total_produtos');
            $builder->where('c.exibir_na_loja', 1);
            $builder->where('c.ativo', 1);
            $builder->where('c.id_empresa', $idEmpresa);
            
            if ($apenasDestaque) {
                $builder->where('c.destaque_na_loja', 1);
            }
            
            $builder->orderBy('c.ordem_na_loja', 'ASC');
            $builder->orderBy('c.nome', 'ASC');
            
            if ($limit > 0) {
                $builder->limit($limit);
            }
            
            $query = $builder->get();
            $categorias = $query->getResultArray();
            
            // Adicionar ícones às categorias e verificar imagens
            foreach ($categorias as &$categoria) {
                $categoria['icone'] = $this->obterIconeCategoria($categoria['nome']);
                
                // Garantir caminho de imagem ou padrão
                if (empty($categoria['imagem'])) {
                    $categoria['imagem'] = 'categoria-default.jpg';
                }
            }
            
            return $categorias;
        } catch (Exception $e) {
            log_message('error', "Erro ao buscar categorias: " . $e->getMessage());
            return $this->getCategoriasBasicas($idEmpresa);
        }
    }
    
    /**
     * Obtém as categorias para exibição no menu
     *
     * @param int $limit Limite de categorias (0 = sem limite)
     * @param int|null $idEmpresa ID da empresa (null = usa o ID atual)
     * @return array Lista de categorias para o menu
     */
    public function getCategoriasMenu(int $limit = 0, ?int $idEmpresa = null): array
    {
        try {
            // Se não foi informado, usa o ID da empresa atual
            if ($idEmpresa === null) {
                $idEmpresa = $this->idEmpresa;
            }
            
            $builder = $this->db->table($this->table . ' c');
            $builder->select('c.id_categoria, c.nome, c.slug, c.exibir_na_loja, c.ordem_na_loja');
            $builder->select('(SELECT COUNT(*) FROM produtos p WHERE p.id_categoria = c.id_categoria 
                              AND p.mostrar_na_loja = 1 AND p.ativo = 1 AND p.id_empresa = c.id_empresa) as total_produtos');
            $builder->where('c.exibir_na_loja', 1);
            $builder->where('c.ativo', 1);
            $builder->where('c.id_empresa', $idEmpresa);
            $builder->orderBy('c.ordem_na_loja', 'ASC');
            $builder->orderBy('c.nome', 'ASC');
            
            if ($limit > 0) {
                $builder->limit($limit);
            }
            
            $query = $builder->get();
            $categorias = $query->getResultArray();
            
            // Adicionar ícones às categorias
            foreach ($categorias as &$categoria) {
                $categoria['icone'] = $this->obterIconeCategoria($categoria['nome']);
            }
            
            return $categorias;
        } catch (Exception $e) {
            log_message('error', "Erro ao buscar categorias para menu: " . $e->getMessage());
            
            // Em caso de erro, retorna as categorias básicas
            $categorias = $this->getCategoriasBasicas($idEmpresa);
            
            if ($limit > 0) {
                return array_slice($categorias, 0, $limit);
            }
            
            return $categorias;
        }
    }
    
    /**
     * Obtém categorias em destaque para a loja virtual
     *
     * @param int $limit Limite de registros
     * @param int|null $idEmpresa ID da empresa (null = usa o ID atual)
     * @return array Lista de categorias em destaque
     */
    public function getCategoriasDestaque(int $limit = 4, ?int $idEmpresa = null): array
    {
        // Se não foi informado, usa o ID da empresa atual
        if ($idEmpresa === null) {
            $idEmpresa = $this->idEmpresa;
        }
        
        // Primeiro tenta buscar categorias marcadas como destaque
        $categorias = $this->getCategorias($limit, true, $idEmpresa);
        
        // Se não tiver categorias em destaque suficientes, complementar com categorias normais
        if (count($categorias) < $limit) {
            try {
                $idsExistentes = array_column($categorias, 'id_categoria');
                
                $builder = $this->db->table($this->table . ' c');
                $builder->select('c.id_categoria, c.nome, c.destaque_na_loja, c.slug, c.imagem');
                $builder->where('c.exibir_na_loja', 1);
                $builder->where('c.ativo', 1);
                $builder->where('c.id_empresa', $idEmpresa);
                
                if (!empty($idsExistentes)) {
                    $builder->whereNotIn('c.id_categoria', $idsExistentes);
                }
                
                $builder->orderBy('c.destaque_na_loja', 'DESC');
                $builder->orderBy('c.ordem_na_loja', 'ASC');
                $builder->orderBy('c.nome', 'ASC');
                $builder->limit($limit - count($categorias));
                
                $query = $builder->get();
                $categoriasComplementares = $query->getResultArray();
                
                // Adicionar ícones às categorias complementares
                foreach ($categoriasComplementares as &$categoria) {
                    $categoria['icone'] = $this->obterIconeCategoria($categoria['nome']);
                    
                    // Garantir caminho de imagem ou padrão
                    if (empty($categoria['imagem'])) {
                        $categoria['imagem'] = 'categoria-default.jpg';
                    }
                }
                
                $categorias = array_merge($categorias, $categoriasComplementares);
            } catch (Exception $e) {
                log_message('error', "Erro ao buscar categorias complementares: " . $e->getMessage());
                
                // Em caso de erro ao buscar complementares, retornar somente as que já temos
                if (empty($categorias)) {
                    $categoriasBasicas = $this->getCategoriasBasicas($idEmpresa);
                    
                    // Ordenar pelo total de produtos para simular destaque
                    usort($categoriasBasicas, function($a, $b) {
                        return $b['total_produtos'] <=> $a['total_produtos'];
                    });
                    
                    return array_slice($categoriasBasicas, 0, $limit);
                }
            }
        }
        
        return $categorias;
    }
    
    /**
     * Conta o número de produtos em cada categoria
     *
     * @return array Array associativo com id_categoria => quantidade
     */
    public function contarProdutosPorCategoria(): array
    {
        try {
            $builder = $this->db->table('produtos p');
            $builder->select('p.id_categoria, COUNT(*) as total');
            $builder->where('p.mostrar_na_loja', 1);
            $builder->where('p.ativo', 1);
            $builder->groupBy('p.id_categoria');
            
            $query = $builder->get();
            $resultado = $query->getResultArray();
            
            // Formatar o resultado como array associativo
            $contagem = [];
            foreach ($resultado as $row) {
                $contagem[$row['id_categoria']] = $row['total'];
            }
            
            return $contagem;
        } catch (Exception $e) {
            log_message('error', "Erro ao contar produtos por categoria: " . $e->getMessage());
            
            // Em caso de erro, retorna contagem simulada
            $resultado = [];
            foreach ($this->getCategoriasBasicas($this->idEmpresa) as $categoria) {
                $resultado[$categoria['id_categoria']] = $categoria['total_produtos'];
            }
            
            return $resultado;
        }
    }
} 