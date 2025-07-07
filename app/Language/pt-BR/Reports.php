<?php

return [
    // Geral
    'reports'           => 'Relatórios',
    'report'            => 'Relatório',
    'generate'          => 'Gerar relatório',
    'export'            => 'Exportar',
    'print'             => 'Imprimir',
    'period'            => 'Período',
    
    // Tipos de relatório
    'types'             => [
        'sales'         => 'Vendas',
        'products'      => 'Produtos',
        'customers'     => 'Clientes',
        'inventory'     => 'Estoque',
        'taxes'         => 'Impostos',
        'shipping'      => 'Envios',
        'payments'      => 'Pagamentos',
        'refunds'       => 'Reembolsos',
        'analytics'     => 'Analytics',
    ],
    
    // Períodos
    'periods'           => [
        'today'         => 'Hoje',
        'yesterday'     => 'Ontem',
        'this_week'     => 'Esta semana',
        'last_week'     => 'Semana passada',
        'this_month'    => 'Este mês',
        'last_month'    => 'Mês passado',
        'this_quarter'  => 'Este trimestre',
        'last_quarter'  => 'Trimestre passado',
        'this_year'     => 'Este ano',
        'last_year'     => 'Ano passado',
        'custom'        => 'Personalizado',
    ],
    
    // Filtros
    'filters'           => [
        'date_range'    => 'Intervalo de datas',
        'start_date'    => 'Data inicial',
        'end_date'      => 'Data final',
        'category'      => 'Categoria',
        'status'        => 'Status',
        'payment_method'=> 'Forma de pagamento',
        'shipping_method' => 'Forma de envio',
    ],
    
    // Vendas
    'sales'             => [
        'total_sales'   => 'Total de vendas',
        'gross_sales'   => 'Vendas brutas',
        'net_sales'     => 'Vendas líquidas',
        'refunds'       => 'Reembolsos',
        'discounts'     => 'Descontos',
        'taxes'         => 'Impostos',
        'shipping'      => 'Frete',
        'average_order' => 'Média por pedido',
        'conversion_rate' => 'Taxa de conversão',
    ],
    
    // Produtos
    'products'          => [
        'best_sellers'  => 'Mais vendidos',
        'worst_sellers' => 'Menos vendidos',
        'most_viewed'   => 'Mais visualizados',
        'out_of_stock'  => 'Fora de estoque',
        'low_stock'     => 'Estoque baixo',
        'profit_margin' => 'Margem de lucro',
    ],
    
    // Clientes
    'customers'         => [
        'new_customers' => 'Novos clientes',
        'returning'     => 'Clientes recorrentes',
        'top_spenders'  => 'Maiores compradores',
        'most_orders'   => 'Mais pedidos',
        'average_spend' => 'Média de gastos',
        'lifetime_value'=> 'Valor vitalício',
    ],
    
    // Estoque
    'inventory'         => [
        'stock_value'   => 'Valor do estoque',
        'stock_levels'  => 'Níveis de estoque',
        'stock_movement'=> 'Movimentação de estoque',
        'dead_stock'    => 'Estoque parado',
        'reorder_points'=> 'Pontos de reposição',
    ],
    
    // Impostos
    'taxes'             => [
        'tax_collected' => 'Impostos coletados',
        'tax_rate'      => 'Alíquota',
        'tax_summary'   => 'Resumo de impostos',
        'tax_by_region' => 'Impostos por região',
    ],
    
    // Envios
    'shipping'          => [
        'shipping_cost' => 'Custo de envio',
        'shipping_time' => 'Tempo de envio',
        'delivery_rate' => 'Taxa de entrega',
        'by_carrier'    => 'Por transportadora',
        'by_region'     => 'Por região',
    ],
    
    // Pagamentos
    'payments'          => [
        'payment_methods' => 'Formas de pagamento',
        'successful'    => 'Pagamentos bem-sucedidos',
        'failed'        => 'Pagamentos falhos',
        'pending'       => 'Pagamentos pendentes',
        'refunded'      => 'Pagamentos reembolsados',
    ],
    
    // Analytics
    'analytics'         => [
        'visitors'      => 'Visitantes',
        'page_views'    => 'Visualizações de página',
        'bounce_rate'   => 'Taxa de rejeição',
        'avg_time'      => 'Tempo médio',
        'sources'       => 'Fontes de tráfego',
        'devices'       => 'Dispositivos',
    ],
    
    // Exportação
    'export_formats'    => [
        'pdf'           => 'PDF',
        'excel'         => 'Excel',
        'csv'           => 'CSV',
        'json'          => 'JSON',
    ],
    
    // Mensagens
    'messages'          => [
        'generating'    => 'Gerando relatório...',
        'no_data'       => 'Nenhum dado encontrado para o período selecionado',
        'export_success'=> 'Relatório exportado com sucesso',
        'export_error'  => 'Erro ao exportar relatório',
    ],
]; 