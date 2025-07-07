<?php

namespace App\Models;

use PDO;
use Exception;

class UfModelBase extends BaseModel
{
    protected $table = 'ufs';
    protected $primaryKey = 'id_uf';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        'uf', 'estado', 'codigo_ibge'
    ];
} 