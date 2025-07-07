<?php
namespace App\Views\meusPedidos; ?>

<div class="container">
    <h1 class="mt-4 mb-3"><?= esc($title ?? 'Meus Pedidos') ?></h1>

    <div class="card mb-4">
        <div class="card-body">
            <p class="card-text">Abaixo estão todos os pedidos e status de cada um.</p>
            <p class="card-text">Clique sobre o pedido desejado para obter mais informações.</p>
        </div>
    </div>

    <!-- Formulário de Pesquisa -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Pesquisar Pedidos</h5>
        </div>
        <div class="card-body">
            <form action="<?= site_url('meuspedidos/pesquisa') ?>" method="post">
                <div class="row g-3">
                    <div class="col-md-3">
                        <select class="form-select" name="tipo_pesquisa" id="tipo_pesquisa">
                            <option value="1" <?= old('tipo_pesquisa') == '1' ? 'selected' : '' ?>>Por Período</option>
                            <option value="2" <?= old('tipo_pesquisa') == '2' ? 'selected' : '' ?>>Nº Pedido</option>
                        </select>
                    </div>
                    <div class="col-md-3" id="campo_data_inicio">
                        <input type="date" class="form-control" name="data_inicio" value="<?= old('data_inicio') ?>">
                    </div>
                    <div class="col-md-3" id="campo_data_fim">
                        <input type="date" class="form-control" name="data_fim" value="<?= old('data_fim') ?>">
                    </div>
                    <div class="col-md-3 d-none" id="campo_numero_pedido">
                        <input type="text" class="form-control" name="numero_pedido" placeholder="Nº do Pedido"
                            value="<?= old('numero_pedido') ?>">
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabela de Pedidos -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nº PEDIDO</th>
                            <th>DATA</th>
                            <th>PRODUTO</th>
                            <th>VALOR (R$)</th>
                            <th>STATUS</th>
                            <th>ARQUIVOS</th>
                            <th>NF</th>
                            <th>AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pedidos) && is_array($pedidos)): ?>
                            <?php foreach ($pedidos as $pedido): ?>
                                <tr>
                                    <td><?= esc($pedido['numero_formatado']) ?></td>
                                    <td><?= esc(date('d/m/Y H:i:s', strtotime($pedido['data_pedido']))) ?></td>
                                    <td>
                                        <?= esc($pedido['nome_produto']) ?>
                                        <?php if ($pedido['quantidade_itens'] > 1): ?>
                                            <br><small class="text-muted">+ <?= $pedido['quantidade_itens'] - 1 ?> outros
                                                itens</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>R$ <?= esc(number_format($pedido['valor_total'], 2, ',', '.')) ?></td>
                                    <td>
                                        <span class="badge" style="background-color: <?= esc($pedido['cor_status']) ?>">
                                            <?= esc($pedido['status']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($pedido['permite_upload']): ?>
                                            <a href="<?= site_url('carrinho/envio-arquivos/' . $pedido['id_pedido']) ?>"
                                                class="text-primary">
                                                <i class="fas fa-upload"></i>
                                            </a>
                                        <?php else: ?>
                                            <i class="fas fa-ban text-muted"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if (!empty($pedido['nota_fiscal'])): ?>
                                            <a href="javascript:void(0)"
                                                onclick="downloadNota(<?= esc($pedido['id_pedido'], 'js') ?>)" class="text-primary">
                                                <i class="fas fa-file-download"></i><br>
                                                <small>NF <?= esc($pedido['nota_fiscal']) ?></small>
                                            </a>
                                        <?php elseif ($pedido['status'] === 'Pagamento Pendente'): ?>
                                            <small>Aguarde</small>
                                        <?php elseif ($pedido['status'] === 'Processando'): ?>
                                            <small>Processando</small>
                                        <?php else: ?>
                                            <small>-</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= site_url('detalhespedido/' . $pedido['id_pedido']) ?>"
                                            class="btn btn-sm btn-primary">
                                            Detalhes
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">Nenhum pedido encontrado</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <?php if (isset($pager)): ?>
                <div class="row mt-4">
                    <div class="col-md-9 d-flex justify-content-center">
                    <?= $pager->links('default', 'bootstrap_full') ?>
                    </div>
                    <div class="col-md-3">
                        <form method="get" id="perPageForm" class="d-flex">
                            <select class="form-select" id="per_page" name="per_page" onchange="this.form.submit()">
                                <option value="5" <?= ($per_page ?? 10) == 5 ? 'selected' : '' ?>>5 itens</option>
                                <option value="10" <?= ($per_page ?? 10) == 10 ? 'selected' : '' ?>>10 itens</option>
                                <option value="20" <?= ($per_page ?? 10) == 20 ? 'selected' : '' ?>>20 itens</option>
                                <option value="50" <?= ($per_page ?? 10) == 50 ? 'selected' : '' ?>>50 itens</option>
                            </select>
                            <!-- Manter outros parâmetros GET -->
                            <?php foreach ($_GET as $key => $value): ?>
                                <?php if ($key !== 'per_page' && $key !== 'page'): ?>
                                    <input type="hidden" name="<?= esc($key) ?>" value="<?= esc($value) ?>">
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // Alternar entre campos de pesquisa
    document.getElementById('tipo_pesquisa')?.addEventListener('change', function () {
        const tipo = this.value;
        const dataInicio = document.getElementById('campo_data_inicio');
        const dataFim = document.getElementById('campo_data_fim');
        const numeroPedido = document.getElementById('campo_numero_pedido');

        if (tipo === '1') {
            dataInicio.classList.remove('d-none');
            dataFim.classList.remove('d-none');
            numeroPedido.classList.add('d-none');
        } else {
            dataInicio.classList.add('d-none');
            dataFim.classList.add('d-none');
            numeroPedido.classList.remove('d-none');
        }
    });

    // Função para download de nota fiscal
    function downloadNota(pedidoId) {
        window.location.href = '<?= site_url("meus-pedidos/download-nota/") ?>' + pedidoId;
    }

    // Atualizar quantidade de itens por página
    document.getElementById('per_page')?.addEventListener('change', function () {
        const perPage = this.value;
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', perPage);
        window.location.href = url.toString();
    });

    // Inicializar campos de pesquisa
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('tipo_pesquisa').dispatchEvent(new Event('change'));
    });
</script>
<script>
    // Adiciona o valor de per_page nos links de paginação
    document.addEventListener('DOMContentLoaded', function () {
        const perPage = document.getElementById('per_page')?.value;
        document.querySelectorAll('.pagination a').forEach(link => {
            const url = new URL(link.href);
            url.searchParams.set('per_page', perPage);
            link.href = url.toString();
        });
    });
</script>