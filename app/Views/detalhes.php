<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">Detalhes do Pedido</h1>

            <!-- Mensagens de feedback -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <div class="row">
                <!-- Card de informações - Ocupa 4 colunas em telas grandes -->
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Informações do Pedido</h5>
                        </div>
                        <div class="card-body">
                            <p><strong class="me-2">Número do Pedido</strong><?= $pedido['id_pedido'] ?></p>
                            <p><strong>Data:</strong> <?= date('d/m/Y', strtotime($pedido['data'])) ?></p>
                            <p><strong>Hora:</strong> <?= date('H:i', strtotime($pedido['hora'])) ?></p>
                            <p><strong>Status:</strong>
                                <span
                                    class="badge bg-<?= $pedido['status_pedido'] === 'pago' ? 'success' : 'warning' ?>">
                                    <?= ucfirst($pedido['status_pedido']) ?>
                                </span>
                            </p>
                            <p><strong>Forma de Pagamento:</strong> <?= strtoupper($pedido['forma_de_pagamento']) ?></p>
                            <p><strong>Valor Total:</strong> R$ <?= number_format($pedido['preco_total'], 2, ',', '.') ?></p>
                            <?php if ($pedido['valor_frete'] > 0): ?>
                                <p><strong>Frete:</strong> R$ <?= number_format($pedido['valor_frete'], 2, ',', '.') ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Card de endereço - Ocupa 4 colunas em telas grandes -->
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Endereço de Entrega</h5>
                        </div>
                        <div class="card-body">
                            <address class="mb-0">
                                <?= htmlspecialchars($pedido['endereco_rua'] ?? '') ?>,
                                <?= htmlspecialchars($pedido['numero_rua'] ?? '') ?><br>
                                <?php if (!empty($pedido['complemento'])): ?>
                                    <?= htmlspecialchars($pedido['complemento']) ?><br>
                                <?php endif; ?>
                                Bairro: <?= htmlspecialchars($pedido['bairro'] ?? '') ?><br>
                                CEP: <?= htmlspecialchars($pedido['cep'] ?? '') ?><br>
                                <?= htmlspecialchars($pedido['cidade'] ?? '') ?> -
                                <?= htmlspecialchars($pedido['estado'] ?? '') ?>
                            </address>

                        </div>
                        <div class="m-4">
                            <a href="<?= base_url("pedido/pagar/{$pedido['token']}") ?>" class="btn btn-primary w-100">
                                <i class="fas fa-credit-card me-1"></i> Finalizar Pagamento
                            </a>
                            <?php if ($mostrarBotaoVoltarCheckout): ?>
                                <a href="<?= site_url('checkout') ?>" class="btn btn-outline-secondary mt-2 w-100">
                                    ← Voltar para o Checkout
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Card de ações - Ocupa 4 colunas em telas grandes -->
                <!-- <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Ações</h5>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <?php if ($pedido['status_pedido'] === 'pendente'): ?>
                                <a href="<?= base_url("pedido/comprovante/{$pedido['id_pedido']}") ?>"
                                    class="btn btn-outline-secondary mb-2">
                                    <i class="fas fa-file-pdf me-1"></i> Gerar Comprovante
                                </a>

                            <?php else: ?>
                                <a href="<?= base_url("pedido/comprovante/{$pedido['id_pedido']}") ?>"
                                    class="btn btn-primary">
                                    <i class="fas fa-file-pdf me-1"></i> Ver Comprovante
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div> -->

                <!-- Card de itens (ocupa 12 colunas) -->
                <?php if (!empty($itensPedido)): ?>
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Itens do Pedido</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Produto</th>
                                            <th class="text-center">Quantidade</th>
                                            <th class="text-end">Preço Unitário</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($itensPedido as $item): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($item['nome'] ?? $item['id_produto']) ?></td>
                                                <td class="text-center"><?= $item['quantidade'] ?></td>
                                                <td class="text-end">R$
                                                    <?= number_format($item['preco_unitario'], 2, ',', '.') ?>
                                                </td>
                                                <td class="text-end">R$
                                                    <?= number_format($item['preco_unitario'] * $item['quantidade'], 2, ',', '.') ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot class="fw-bold">
                                        <tr>
                                            <td colspan="3" class="text-end">Total:</td>
                                            <td class="text-end">R$
                                                <?= number_format($pedido['preco_total'], 2, ',', '.') ?>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>