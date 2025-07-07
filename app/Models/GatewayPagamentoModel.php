<?php

namespace App\Models;

use CodeIgniter\Model;

class GatewayPagamentoModel extends Model
{
	protected $table = 'gateway_pagamento';
	protected $primaryKey = 'id';
	protected $allowedFields = [
		'id',
		'integracao',
		'pix',
		'boleto',
		'cartao',
		'url',
		'token',
		'secret_key',
		'id_empresa',
	];
	protected $useTimestamps = false;
	protected $createdField  = null;
	protected $updatedField  = null;
	protected $deletedField  = null;
}
