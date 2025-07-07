<?php

namespace App\Models;

use PDO;
use Exception;

class MarcaParceiraModelConsulta extends MarcaParceiraModelBase
{
    /**
     * Obtém as marcas parceiras ativas para a loja virtual
     *
     * @param int $limit Limite de marcas a retornar
     * @return array Lista de marcas parceiras
     */
    public function getMarcasParceiras(int $limit = 6): array
    {
        try {
            $builder = $this->db->table('loja_marcas_parceiras m');
            $builder->select('
                m.id,
                m.nome,
                m.logo,
                m.link,
                m.ordem
            ');
            $builder->where('m.ativo', 1);
            $builder->where('m.id_empresa', $this->idEmpresa);
            $builder->orderBy('m.ordem', 'ASC');
            $builder->orderBy('m.id', 'DESC');
            $builder->limit($limit);
            
            $query = $builder->get();
            $marcas = $query->getResultArray();
            
            // Garantir caminho de imagem ou padrão
            foreach ($marcas as &$marca) {
                if (empty($marca['logo'])) {
                    $marca['logo'] = 'marca-default.png';
                }
            }
            
            return $marcas;
        } catch (\Exception $e) {
            log_message('error', "Erro ao buscar marcas parceiras: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtém uma marca parceira específica pelo ID
     *
     * @param int $id ID da marca parceira
     * @return array|null Dados da marca parceira ou null se não encontrada
     */
    public function getMarcaParceira(int $id): ?array
    {
        try {
            $builder = $this->db->table('loja_marcas_parceiras m');
            $builder->select('
                m.id,
                m.nome,
                m.logo,
                m.link,
                m.ativo,
                m.ordem
            ');
            $builder->where('m.id', $id);
            $builder->where('m.id_empresa', $this->idEmpresa);
            
            $query = $builder->get();
            $marca = $query->getRowArray();
            
            return $marca ?: null;
        } catch (\Exception $e) {
            log_message('error', "Erro ao buscar marca parceira: " . $e->getMessage());
            return null;
        }
    }
} 