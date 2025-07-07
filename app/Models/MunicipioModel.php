<?php

namespace App\Models;

use PDO;
use Exception;

class MunicipioModel extends MunicipioModelBase
{
    private $modelConsulta;
    
    /**
     * Construtor
     */
    public function __construct()
    {
        parent::__construct();
        $this->modelConsulta = new MunicipioModelConsulta();
    }
    
    /**
     * Retorna todos os municípios
     */
    public function getAll(): array
    {
        return $this->modelConsulta->getAll();
    }
    
    /**
     * Busca um município pelo ID
     */
    public function getById(int $id):array
    {
        return $this->modelConsulta->getById($id);
    }
    
    /**
     * Busca municípios por UF
     */
    public function getByUf(int $idUf): array
    {
        return $this->modelConsulta->getByUf($idUf);
    }
    
    /**
     * Busca um município pelo nome e UF
     */
    public function getByNomeUf(string $nome, int $idUf): ?array
    {
        return $this->modelConsulta->getByNomeUf($nome, $idUf);
    }
} 