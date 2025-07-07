<?php
namespace App\Models;
use CodeIgniter\Model;

class PedidosPagamentoModel extends Model
{
    // Configurações básicas do modelo
    protected $table = 'pedidos_pagamento';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    // Campos permitidos para inserção/atualização
    protected $allowedFields = [
        'id_pedido',
        'data_inicial_pagamento',
        'vencimento_pagamento',
        'descricao_pagamento',
        'valor_pagamento',
        'juros_pagamento',
        'multa_pagamento',
        'quitado_pagamento',
        'recebimento_pagamento',
        'condicoes_pagamento',
        'status'
    ];

    // Busca pagamento por ID do pedido
    public function getPagamentoByPedidoId($id_pedido)
    {
        return $this->where('id_pedido', $id_pedido)->first();
    }

    // Salva dados de pagamento
    public function savePayment($data)
    {
        return $this->save($data);
    }
}