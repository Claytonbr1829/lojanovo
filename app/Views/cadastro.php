<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">Cadastrar</h3>
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

                    <form action="<?= site_url('cadastrar') ?>" method="post">
                        <div class="row align-items-center mb-3">
                            <!-- Campo Email -->
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-10">
                                        <label for="email" class="form-label">E-mail</label>
                                        <input type="email"
                                            class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>"
                                            id="email" name="email" value="<?= old('email') ?>" required>
                                    </div>
                                    <div class="col-2">
                                        <label for="id_empresa" class="form-label">Id-Empresa</label>
                                        <input type="number"
                                            class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>"
                                            id="id_empresa" name="id_empresa" value="<?= old('id_empresa') ?>" required>
                                    </div>
                                </div>
                                <?php if (session('errors.email')): ?>
                                    <div class="invalid-feedback"><?= session('errors.email') ?></div>
                                <?php endif ?>
                            </div>

                            <!-- Campo Senha -->
                            <div class="mb-3">
                                <label for="senha" class="form-label">Password</label>
                                <input type="password"
                                    class="form-control <?= session('errors.senha') ? 'is-invalid' : '' ?>" id="senha"
                                    name="senha" required>
                                <div class="form-text">A senha deve conter pelo menos 8 caracteres, incluindo letras
                                    maiúsculas, minúsculas, números e símbolos</div>
                                <?php if (session('errors.senha')): ?>
                                    <div class="invalid-feedback"><?= session('errors.senha') ?></div>
                                <?php endif ?>
                            </div>

                            <!-- Campo Confirma Senha -->
                            <div class="mb-3">
                                <label for="confirma_senha"
                                    class="form-label">Confirmar Senha</label>
                                <input type="password"
                                    class="form-control <?= session('errors.confirma_senha') ? 'is-invalid' : '' ?>"
                                    id="confirma_senha" name="confirma_senha" required>
                                <?php if (session('errors.confirma_senha')): ?>
                                    <div class="invalid-feedback"><?= session('errors.confirma_senha') ?></div>
                                <?php endif ?>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Cadastrar</button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>