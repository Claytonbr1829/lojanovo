<?php
namespace App\Models;

use PDO;
use Exception;
use CodeIgniter\Model;

class PedidoModelBase extends Model
{
    protected $table = 'pedidos';
    protected $primaryKey = 'id_pedido';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        'id_pedido', 'valor_a_pagar', 'desconto', 'valor_recebido', 'troco', 'forma_de_pagamento','data',
        'hora', 'situacao', 'prazo_de_entrega', 'id_cliente', 'id_vendedor', 'id_caixa', 'id_empresa',
        'created_at', 'updated_at', 'deleted_at', 'status_pedido', 'endereco_diferente', 'endereco',
        'cep', 'estado', 'cidade', 'bairro', 'endereco_rua', 'numero_rua', 'complemento', 'transportadora',
        'tipo_frete', 'valor_frete', 'previsao_entrega', 'codigo_rastreio', 'url_rastreamento','observacoes_finais',
        'data_entrega', 'porcentagem_cliente', 'porcentagem_service','porcentagem_premium', 'token','preco_total',
        'valor_frete', 'subtotal',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = '';
    
    /**
     * Constantes para status padrão
     */
    const STATUS_PENDENTE = 1;
    const STATUS_PAGO = 2;
    const STATUS_EM_SEPARACAO = 3;
    const STATUS_EM_TRANSPORTE = 4;
    const STATUS_ENTREGUE = 5;
    const STATUS_CANCELADO = 6;
    
    // Array de status com descrições para exibição
    protected $statusDescricoes = [
        self::STATUS_PENDENTE => 'Pendente',
        self::STATUS_PAGO => 'Pago',
        self::STATUS_EM_SEPARACAO => 'Em Separação',
        self::STATUS_EM_TRANSPORTE => 'Em Transporte',
        self::STATUS_ENTREGUE => 'Entregue',
        self::STATUS_CANCELADO => 'Cancelado'
    ];
    
    /**
     * Retorna a descrição de um status baseado em seu código
     *
     * @param int $status Código do status
     * @return string Descrição do status
     */
    protected function getStatusDescricao(int $status): string
    {
        return $this->statusDescricoes[$status] ?? 'Status Desconhecido';
    }
    
    /**
     * Adiciona informações complementares aos dados do pedido
     *
     * @param array $pedido Dados do pedido
     * @return array Pedido com informações adicionais
     */
    protected function formatarPedido(array $pedido): array
    {
        // Adiciona a descrição do status
        $pedido['status_descricao'] = $this->getStatusDescricao($pedido['status']);
        
        // Formata valores monetários
        $pedido['valor_total_formatado'] = 'R$ ' . number_format($pedido['valor_total'], 2, ',', '.');
        $pedido['valor_frete_formatado'] = 'R$ ' . number_format($pedido['valor_frete'], 2, ',', '.');
        $pedido['valor_desconto_formatado'] = 'R$ ' . number_format($pedido['valor_desconto'], 2, ',', '.');
        
        // Formata datas
        $pedido['data_pedido'] = date('d/m/Y H:i', strtotime($pedido['created_at']));
        $pedido['data_atualizacao'] = date('d/m/Y H:i', strtotime($pedido['updated_at']));
        
        return $pedido;
    }
} 