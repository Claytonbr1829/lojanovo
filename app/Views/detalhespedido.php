<div class="container my-5">
    <h2 class="mb-4">DETALHES DO PEDIDO</h2>

    <div class="bg-white p-4 rounded shadow-sm">
        <div class="mb-3">
            <strong>Número do Pedido:</strong> <?= esc($pedido['id_pedido']) ?><br>
            <strong>Data:</strong> <?= date('d/m/Y \à\s H:i:s', strtotime($pedido['data'])) ?><br>
            <strong>Forma de Pagamento:</strong> <?= esc($pedido['forma_de_pagamento']) ?><br>
            <strong>Valor:</strong> R$ <?= number_format($pedido['preco_total'], 2, ',', '.') ?><br>
        </div>

        <?php foreach ($itens as $item): ?>
            <div class="border-top pt-3 mt-3">
                <div class="row">
                    <div class="col-md-2">
                        <img src="<?= base_url( 'uploads/produtos/'.($item['arquivo'] != '' ? $item['arquivo'] : 'produto-default.jpg'))?>" alt="Produto" class="img-fluid rounded">
                    </div>
                    <div class="col-md-10">
                        <p class="mb-1"><strong><?= esc($item['nome']) ?></strong></p>
                        
                        <p class="mb-1">Quantidade: <?= esc($item['quantidade']) ?> | Preço Unitário: R$
                            <?= number_format($item['preco_unitario'], 2, ',', '.') ?></p>
                        <p class="mb-0"><strong>Total: R$ <?= number_format($item['preco_total'], 2, ',', '.') ?></strong>
                        </p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="bg-light p-4 mt-4 rounded shadow-sm">
        <h5 class="mb-3">Endereço de Entrega</h5>
        <p><?= esc($pedido['endereco']) ?></p>
        <p>Peso Total: <?= esc($pedido['preco_total']) ?></p>
        <p>Prazo de Produção: <?= esc($pedido['previsao_entrega']) ?></p>
        <p>Prazo Entrega: <?= esc($pedido['prazo_entrega']) ?></p>
        <p>Valor do Frete: R$ <?= number_format($pedido['valor_frete'], 2, ',', '.') ?></p>
        
        <p>Número de rastreio: <?= esc($pedido['codigo_rastreio'] ?? '-') ?></p>
    </div>

    
</div>
