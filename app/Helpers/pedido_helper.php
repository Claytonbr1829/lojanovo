<?php

if (!function_exists('getStatusPedido')) {
    function getStatusPedido($status) {
        $statusList = [
            1 => 'Pendente',
            2 => 'Aprovado',
            3 => 'Em transporte',
            4 => 'Entregue',
            5 => 'Cancelado'
        ];
        return $statusList[$status] ?? 'Desconhecido';
    }
}

if (!function_exists('getStatusPagamento')) {
    function getStatusPagamento($status) {
        $statusList = [
            1 => 'Aguardando pagamento',
            2 => 'Pagamento confirmado',
            3 => 'Pagamento recusado',
            4 => 'Reembolsado'
        ];
        return $statusList[$status] ?? 'Desconhecido';
    }
} 