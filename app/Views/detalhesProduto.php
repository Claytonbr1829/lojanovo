<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-md-5">
            <div class="card" style="width: 25rem;">
                <img src="<?= base_url('uploads/produtos/' . $produto['arquivo']) ?>" alt="" class="img-fluid">
            </div>
        </div>
        <div class="col-md-7">
            <div class="mt-3">
                <h5 class="negrito"><?= esc($produto['nome']) ?></h5>
            </div>
            <div class="text-success">
                <p><?= esc($categoria['nome']) ?></p>
            </div>
            <div class="text-success mb-3">
                <h4>R$: <?= esc($produto['valor_de_venda']) ?></h4>
            </div>
            <div>
                <?php if ($produto['quantidade'] > 0): ?>
                    <div class="d-flex">
                        <p class="me-2 text-bg-success">em estoque</p>
                        <p><?= esc($produto['quantidade']) ?> unidades disponível</p>
                    </div>
                <?php else: ?>
                    <p>Produto não tem no estoque</p>
                <?php endif; ?>
            </div>
            <div class="col-5 col-md-3 ">
                <div class="input-group input-group-sm mb-3 mt-3">
                    <!-- Botão de diminuir quantidade -->
                    <form action="<?= site_url('carrinho/adicionar/' . $produto['id_produto']) ?>" method="post">
                        <div class="input-group input-group-sm mb-2" style="max-width: 150px;">
                            <button type="button" class="btn btn-outline-secondary btn-quantity"
                                data-action="decrease">-</button>
                            <input type="number" name="quantidade" value="1" min="1" max="<?= $produto['quantidade'] ?>"
                                class="form-control text-center" id="quantidadeInput">
                            <button type="button" class="btn btn-outline-secondary btn-quantity"
                                data-action="increase">+</button>
                        </div>

                        <button type="submit" class="btn btn-success btn-sm text-bg-danger mt-3">
                            Adicionar ao Carrinho
                        </button>
                    </form>
                    <div class='d-flex mt-4'>
                        <p>
                            <strong>Dimensões:</strong>&nbsp;
                            <span class="me-2"><?= esc($produto['comprimento']) ?> x <?= esc($produto['largura']) ?> x
                                <?= esc($produto['altura']) ?></span>
                            <strong>Peso:</strong> <?= esc($produto['peso_liquido']) ?> kg
                        </p>

                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

</div>

<script>
    document.querySelectorAll('.btn-quantity').forEach(btn => {
        btn.addEventListener('click', function () {
            const input = document.getElementById('quantidadeInput');
            let value = parseInt(input.value);
            const min = parseInt(input.min);
            const max = parseInt(input.max);

            if (this.dataset.action === 'increase' && value < max) {
                input.value = value + 1;
            }

            if (this.dataset.action === 'decrease' && value > min) {
                input.value = value - 1;
            }
        });
    });
</script>