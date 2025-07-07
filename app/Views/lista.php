
<div class="container py-5">
    <h1 class="mb-4">Meus Pedidos</h1>

    <?php if (empty($pedidos)): ?>
        <div class="alert alert-info">
            Você ainda não fez nenhum pedido.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Número</th>
                        <th>Data</th>
                        <th>Itens</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pedidos as $pedido): ?>
                        <tr>
                            <td>#<?= $pedido['id_pedido'] ?? 'N/A' ?></td>
                            <td><?= isset($pedido['created_at']) ? date('d/m/Y', strtotime($pedido['created_at'])) : 'N/A' ?>
                            </td>
                            <td><?= $pedido['total_itens'] ?? 0 ?></td>
                            <td>R$
                                <?= isset($pedido['valor_total']) ? number_format($pedido['valor_total'], 2, ',', '.') : '0,00' ?>
                            </td>
                            <td>
                                <?php if (isset($pedido['status'])): ?>
                                    <span class="badge bg-<?=
                                        $pedido['status'] == 1 ? 'warning' :
                                        ($pedido['status'] == 2 ? 'success' :
                                            ($pedido['status'] == 5 ? 'danger' : 'secondary'))
                                        ?>">
                                        <?= getStatusPedido($pedido['status']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Desconhecido</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (isset($pedido['id_pedido'])): ?>
                                    <a href="<?= site_url("pedido/{$pedido['id_pedido']}") ?>" class="btn btn-sm btn-primary">
                                        Detalhes
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>


<?php
function getStatusPedido($status)
{
    $statusList = [
        1 => 'Pendente',
        2 => 'Aprovado',
        3 => 'Em transporte',
        4 => 'Entregue',
        5 => 'Cancelado'
    ];
    return $statusList[$status] ?? 'Desconhecido';
}
?>