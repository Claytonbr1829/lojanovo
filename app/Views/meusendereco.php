<div class="container my-4">
    <h4 class="mb-3">Por favor configure seu endereço padrão para faturamento e para entrega.</h4>
    <p class="text-muted">Você pode cadastrar vários endereços usando cada um para uma situação específica.</p>

    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#faturamento">Endereço de Faturamento</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#entrega">Endereço de Entrega</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#cobranca">Endereço da Cobrança</a>
        </li>
    </ul>

    <div class="tab-content">
        <?php foreach (['faturamento', 'entrega'] as $tipo): ?>
            <div class="tab-pane fade <?= $tipo === 'faturamento' ? 'show active' : '' ?>" id="<?= $tipo ?>">
                <?php $end = $enderecos[$tipo] ?? null; ?>
                <?php if ($end): ?>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="card border <?= $tipo === 'faturamento' ? 'border-dark' : 'border-secondary' ?>">
                                <div class="card-header <?= $tipo === 'faturamento' ? 'bg-dark' : 'bg-secondary' ?> text-white">
                                    <?= $tipo === 'faturamento' ? 'Casa (Padrão)' : 'Endereço de Entrega' ?>
                                </div>
                                <div class="card-body">
                                    <p class="mb-1"><strong><?= esc($end['logradouro']) ?><?= esc($end['numero']) ?></strong>
                                    </p>
                                    <?php if (!empty($end['complemento'])): ?>
                                        <p class="mb-1"><?= esc($end['complemento']) ?></p>
                                    <?php endif; ?>
                                    <p class="mb-1"><?= esc($end['bairro']) ?> - <?= esc($end['municipio']) ?> /
                                        <?= esc($end['estado']) ?>
                                    </p>
                                    <p class="mb-1">CEP: <?= esc($end['cep']) ?></p>
                                    <p class="text-muted"><small><?= esc($end['fonte']) ?></small></p>

                                    <div class="d-flex justify-content-between mt-3">
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#modalEndereco" data-tipo="<?= $tipo ?>"
                                            data-endereco='<?= json_encode($end) ?>'>✏️ Editar</button>

                                        <button class="btn btn-sm btn-outline-danger">🗑 Excluir</button>
                                    </div>
                                </div>
                            </div>
                            <!-- Modal de Edição de Endereço -->
                            <div class="modal fade" id="modalEndereco" tabindex="-1" aria-labelledby="modalEnderecoLabel"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <form id="formEndereco">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalEnderecoLabel">Editar Endereço</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="tipo" id="tipo">

                                                <div class="mb-3">
                                                    <label class="form-label">Logradouro</label>
                                                    <input type="text" name="logradouro" id="logradouro" class="form-control"
                                                        required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Número</label>
                                                    <input type="text" name="numero" id="numero" class="form-control">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Complemento</label>
                                                    <input type="text" name="complemento" id="complemento" class="form-control">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Bairro</label>
                                                    <input type="text" name="bairro" id="bairro" class="form-control" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Município</label>
                                                    <input type="text" name="municipio" id="municipio" class="form-control"
                                                        required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Estado</label>
                                                    <input type="text" name="estado" id="estado" class="form-control" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">CEP</label>
                                                    <input type="text" name="cep" id="cep" class="form-control" required>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-dark">Salvar</button>
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancelar</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                <?php else: ?>
                    <p class="text-muted">Nenhum endereço de <?= $tipo ?> cadastrado.</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="mt-4">
        <button class="btn btn-outline-dark">➕ Adicionar Endereço</button>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('modalEndereco');
        modal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const tipo = button.getAttribute('data-tipo');
            const endereco = JSON.parse(button.getAttribute('data-endereco'));

            document.getElementById('tipo').value = tipo;
            document.getElementById('logradouro').value = endereco.logradouro ?? '';
            document.getElementById('numero').value = endereco.numero ?? '';
            document.getElementById('complemento').value = endereco.complemento ?? '';
            document.getElementById('bairro').value = endereco.bairro ?? '';
            document.getElementById('municipio').value = endereco.municipio ?? '';
            document.getElementById('estado').value = endereco.estado ?? '';
            document.getElementById('cep').value = endereco.cep ?? '';
        });

        document.getElementById('formEndereco').addEventListener('submit', function (e) {
            e.preventDefault();

            const form = new FormData(this);

            fetch('/meusendereco/salvar', {
                method: 'POST',
                body: form
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'ok') {
                        location.reload(); // ou atualizar apenas o card via JS
                    } else {
                        alert('Erro ao salvar endereço');
                    }
                });
        });
    });
</script>