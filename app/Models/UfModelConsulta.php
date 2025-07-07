<?php

namespace App\Models;

use PDO;
use Exception;

class UfModelConsulta extends UfModelBase
{
    /**
     * Retorna todos os estados
     */
    public function getAll():array
    {
        try {
            $query = $this->db->query("SELECT * FROM ufs ORDER BY estado ASC");
            return $query->getResultArray();
        } catch (Exception $e) {
            error_log("Erro ao buscar estados: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Busca um estado pelo ID
     */
    public function getById(int $id): ?array
    {
        try {
            $query = $this->db->query("SELECT * FROM ufs WHERE id_uf = ?", [$id]);
            $result = $query->getRowArray();
            
            return $result ?: null;
        } catch (Exception $e) {
            error_log("Erro ao buscar estado por ID: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Busca um estado pela sigla
     */
    public function getBySigla(string $sigla): ?array
    {
        try {
            $query = $this->db->query("SELECT * FROM ufs WHERE uf = ?", [$sigla]);
            $result = $query->getRowArray();
            
            return $result ?: null;
        } catch (Exception $e) {
            error_log("Erro ao buscar estado por sigla: " . $e->getMessage());
            return null;
        }
    }
} 