<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">Alterar Senha</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger">
                            <?= $error ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($success)): ?>
                        <div class="alert alert-success">
                            <?= $success ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= site_url('cliente/alterar-senha') ?>" method="post">
                        <!-- Campo Email (somente leitura) -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= esc($email) ?>"
                                readonly>
                        </div>

                        <!-- Senha atual -->
                        <div class="mb-3">
                            <label for="senha_atual" class="form-label">Senha Atual</label>
                            <input type="password"
                                class="form-control <?= session('errors.senha_atual') ? 'is-invalid' : '' ?>"
                                id="senha_atual" name="senha_atual" required>
                            <?php if (session('errors.senha_atual')): ?>
                                <div class="invalid-feedback"><?= session('errors.senha_atual') ?></div>
                            <?php endif ?>
                        </div>

                        <!-- Nova senha -->
                        <div class="mb-3">
                            <label for="nova_senha" class="form-label">Nova Senha</label>
                            <input type="password"
                                class="form-control <?= session('errors.nova_senha') ? 'is-invalid' : '' ?>"
                                id="nova_senha" name="nova_senha" required>
                            <?php if (session('errors.nova_senha')): ?>
                                <div class="invalid-feedback"><?= session('errors.nova_senha') ?></div>
                            <?php endif ?>
                        </div>

                        <!-- Confirmar nova senha -->
                        <div class="mb-3">
                            <label for="confirmar_senha" class="form-label">Confirmar Nova Senha</label>
                            <input type="password"
                                class="form-control <?= session('errors.confirmar_senha') ? 'is-invalid' : '' ?>"
                                id="confirmar_senha" name="confirmar_senha" required>
                            <?php if (session('errors.confirmar_senha')): ?>
                                <div class="invalid-feedback"><?= session('errors.confirmar_senha') ?></div>
                            <?php endif ?>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Alterar Senha</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>