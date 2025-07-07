<?php

namespace App\Models;

use CodeIgniter\Model;

class PedidoItemModel extends Model
{
    protected $table = 'pedidos_itens';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id', 'id_produto', 'quantidade', 'preco_unitario', 
        'preco_total', 'id_pedido','numero_pedido', 'nome'

    ];
} 