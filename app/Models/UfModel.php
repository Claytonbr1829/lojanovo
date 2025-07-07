<?php

namespace App\Models;

use PDO;
use Exception;

class UfModel extends UfModelBase
{
    private $modelConsulta;
    
    /**
     * Construtor
     */
    public function __construct()
    {
        parent::__construct();
        $this->modelConsulta = new UfModelConsulta();
    }
    
    /**
     * Retorna todos os estados
     */
    public function getAll():array
    {
        return $this->modelConsulta->getAll();
    }
    
    /**
     * Busca um estado pelo ID
     */
    public function getById(int $id): ?array
    {
        return $this->modelConsulta->getById($id);
    }
    
    /**
     * Busca um estado pela sigla
     */
    public function getBySigla(string $sigla): ?array
    {
        return $this->modelConsulta->getBySigla($sigla);
    }
} 