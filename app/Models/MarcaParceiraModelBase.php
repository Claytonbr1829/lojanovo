<?php

namespace App\Models;

use PDO;
use Exception;

class MarcaParceiraModelBase extends BaseModel
{
    protected $table = 'loja_marcas_parceiras';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        'id_empresa', 'nome', 'logo', 'link', 'ativo', 'ordem', 'created_at', 'updated_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = '';
    
    /**
     * Constantes para status padrão
     */
    const STATUS_ATIVO = 1;
    const STATUS_INATIVO = 0;
} 