<?php

return [
    // Geral
    'order'             => 'Pedido',
    'orders'            => 'Pedidos',
    'my_orders'         => 'Meus pedidos',
    'order_number'      => 'Número do pedido',
    'order_date'        => 'Data do pedido',
    'order_status'      => 'Status do pedido',
    'order_total'       => 'Total do pedido',
    'order_details'     => 'Detalhes do pedido',
    
    // Status do pedido
    'status'            => [
        'pending'       => 'Pendente',
        'processing'    => 'Processando',
        'confirmed'     => 'Confirmado',
        'paid'          => 'Pago',
        'shipped'       => 'Enviado',
        'delivered'     => 'Entregue',
        'cancelled'     => 'Cancelado',
        'refunded'      => 'Reembolsado',
        'partially_refunded' => 'Parcialmente reembolsado',
        'on_hold'       => 'Em espera',
        'backordered'   => 'Pedido atrasado',
    ],
    
    // Informações do pedido
    'info'              => [
        'customer'      => 'Cliente',
        'email'         => 'E-mail',
        'phone'         => 'Telefone',
        'shipping_address' => 'Endereço de entrega',
        'billing_address'  => 'Endereço de cobrança',
        'payment_method'   => 'Forma de pagamento',
        'shipping_method'  => 'Forma de envio',
        'tracking_number'  => 'Número de rastreamento',
        'estimated_delivery' => 'Previsão de entrega',
    ],
    
    // Itens do pedido
    'items'             => [
        'product'       => 'Produto',
        'quantity'      => 'Quantidade',
        'price'         => 'Preço',
        'subtotal'      => 'Subtotal',
        'shipping'      => 'Frete',
        'discount'      => 'Desconto',
        'tax'           => 'Impostos',
        'total'         => 'Total',
    ],
    
    // Ações
    'actions'           => [
        'view'          => 'Visualizar',
        'track'         => 'Rastrear',
        'cancel'        => 'Cancelar',
        'reorder'       => 'Refazer pedido',
        'download_invoice' => 'Baixar fatura',
        'print'         => 'Imprimir',
    ],
    
    // Mensagens
    'messages'          => [
        'created'       => 'Pedido criado com sucesso',
        'confirmed'     => 'Pedido confirmado',
        'paid'          => 'Pedido pago',
        'shipped'       => 'Pedido enviado',
        'delivered'     => 'Pedido entregue',
        'cancelled'     => 'Pedido cancelado',
        'refunded'      => 'Pedido reembolsado',
        'not_found'     => 'Pedido não encontrado',
        'tracking_sent' => 'Número de rastreamento enviado',
    ],
    
    // Erros
    'errors'            => [
        'create_failed' => 'Erro ao criar pedido',
        'cancel_failed' => 'Erro ao cancelar pedido',
        'not_found'     => 'Pedido não encontrado',
        'invalid_status'=> 'Status do pedido inválido',
        'payment_failed'=> 'Erro no pagamento',
        'shipping_failed'=> 'Erro no envio',
    ],
    
    // Notificações
    'notifications'     => [
        'new_order'     => 'Novo pedido recebido',
        'status_change' => 'Status do pedido alterado',
        'payment_received'=> 'Pagamento recebido',
        'shipping_update'=> 'Atualização do envio',
        'delivery_confirmation'=> 'Confirmação de entrega',
    ],
    
    // Histórico
    'history'           => [
        'title'         => 'Histórico do pedido',
        'date'          => 'Data',
        'status'        => 'Status',
        'comment'       => 'Comentário',
        'system'        => 'Sistema',
        'customer'      => 'Cliente',
    ],
    
    // Fatura
    'invoice'           => [
        'title'         => 'Fatura',
        'number'        => 'Número da fatura',
        'date'          => 'Data da fatura',
        'due_date'      => 'Data de vencimento',
        'download'      => 'Baixar fatura',
        'print'         => 'Imprimir fatura',
    ],
    
    // Rastreamento
    'tracking'          => [
        'title'         => 'Rastreamento do pedido',
        'carrier'       => 'Transportadora',
        'tracking_number'=> 'Número de rastreamento',
        'estimated_delivery'=> 'Previsão de entrega',
        'status'        => 'Status do envio',
        'history'       => 'Histórico do envio',
    ],
    
    // Outros
    'notes'             => 'Observações',
    'special_instructions'=> 'Instruções especiais',
    'gift_wrap'         => 'Embrulho para presente',
    'gift_message'      => 'Mensagem do presente',
    'coupon_used'       => 'Cupom utilizado',
    'loyalty_points'    => 'Pontos de fidelidade',
]; 