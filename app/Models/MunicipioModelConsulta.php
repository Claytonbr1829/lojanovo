<?php

namespace App\Models;

use PDO;
use Exception;

class MunicipioModelConsulta extends MunicipioModelBase
{
    /**
     * Retorna todos os municípios
     */
    public function getAll(): array
    {
        try {
            $query = $this->db->query("SELECT m.*, u.estado, u.uf 
                    FROM municipios m
                    JOIN ufs u ON m.id_uf = u.id_uf
                    ORDER BY m.municipio ASC");
            
            return $query->getResultArray();
        } catch (Exception $e) {
            error_log("Erro ao buscar municípios: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Busca um município pelo ID
     */
    public function getById(int $id):?array
    {
        try {
            $query = $this->db->query("SELECT m.*, u.estado, u.uf 
                    FROM municipios m
                    JOIN ufs u ON m.id_uf = u.id_uf
                    WHERE m.id_municipio = ?", $id);
            

            $result = $query->getRowArray();

           // $objeto = (object)$result;

           return $result ?: null;
        } catch (Exception $e) {
            error_log("Erro ao buscar município por ID: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Busca municípios por UF
     */
    public function getByUf(int $idUf): array
    {
        try {
            $query = $this->db->query("SELECT * FROM municipios 
                    WHERE id_uf = ? 
                    ORDER BY municipio ASC", [$idUf]);
            
            return $query->getResultArray();
        } catch (Exception $e) {
            error_log("Erro ao buscar municípios por UF: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Busca um município pelo nome e UF
     */
    public function getByNomeUf(string $nome, int $idUf): ?array
    {
        try {
            $query = $this->db->query("SELECT * FROM municipios 
                    WHERE municipio = ? 
                    AND id_uf = ?", [$nome, $idUf]);
            
            $result = $query->getRowArray();
            
            return $result ?: null;
        } catch (Exception $e) {
            error_log("Erro ao buscar município por nome e UF: " . $e->getMessage());
            return null;
        }
    }
} 