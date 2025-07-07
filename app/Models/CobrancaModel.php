<?php 

namespace App\Models;

use CodeIgniter\Model;

class CobrancaModel extends Model
{

    protected $table = 'tblcobranca';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $allowedFields = [
        'id',
        'id_transacao',
        'data_criacao',
        'valor',
        'forma_pagamento',
        'status_pagamento',
        'id_empresa',
        'id_usuario',
        'link_pagamento',
        'pix_qr_code',
        'boleto_linha_digitavel',
        'id_asaas'
    ];

    public function getListFields ()
    {
        return $this->allowedFields;
    }
}