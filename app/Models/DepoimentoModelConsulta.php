<?php

namespace App\Models;

use PDO;
use Exception;

class DepoimentoModelConsulta extends DepoimentoModelBase
{
    /**
     * Obtém os depoimentos ativos para a loja virtual
     *
     * @param int $limit Limite de depoimentos a retornar
     * @return array Lista de depoimentos
     */
    public function getDepoimentos(int $limit = 3): array
    {
        try {
            $builder = $this->db->table('loja_depoimentos d');
            $builder->select('
                d.id,
                d.nome_cliente as nome,
                d.cargo_empresa as cargo,
                d.depoimento,
                d.avaliacao,
                d.foto,
                d.ordem
            ');
            $builder->where('d.ativo', 1);
            $builder->where('d.id_empresa', $this->idEmpresa);
            $builder->orderBy('d.ordem', 'ASC');
            $builder->orderBy('d.id', 'DESC');
            $builder->limit($limit);
            
            $query = $builder->get();
            $depoimentos = $query->getResultArray();
            
            // Garantir caminho de imagem ou padrão
            foreach ($depoimentos as &$depoimento) {
                if (empty($depoimento['foto'])) {
                    $depoimento['foto'] = 'avatar-default.jpg';
                }
            }
            
            return $depoimentos;
        } catch (\Exception $e) {
            log_message('error', "Erro ao buscar depoimentos: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtém um depoimento específico pelo ID
     *
     * @param int $id ID do depoimento
     * @return array|null Dados do depoimento ou null se não encontrado
     */
    public function getDepoimento(int $id): ?array
    {
        try {
            $builder = $this->db->table('loja_depoimentos d');
            $builder->select('
                d.id,
                d.nome_cliente as nome,
                d.cargo_empresa as cargo,
                d.depoimento,
                d.avaliacao,
                d.foto,
                d.ordem,
                d.ativo
            ');
            $builder->where('d.id', $id);
            $builder->where('d.id_empresa', $this->idEmpresa);
            
            $query = $builder->get();
            $depoimento = $query->getRowArray();
            
            if ($depoimento) {
                // Garantir caminho de imagem ou padrão
                if (empty($depoimento['foto'])) {
                    $depoimento['foto'] = 'avatar-default.jpg';
                }
            }
            
            return $depoimento ?: null;
        } catch (\Exception $e) {
            log_message('error', "Erro ao buscar depoimento: " . $e->getMessage());
            return null;
        }
    }
} 