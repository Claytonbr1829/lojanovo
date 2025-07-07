<?php

namespace App\Models;

use PDO;
use Exception;

class MunicipioModelBase extends BaseModel
{
    protected $table = 'municipios';
    protected $primaryKey = 'id_municipio';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        'id_uf', 'municipio', 'codigo_ibge'
    ];
} 