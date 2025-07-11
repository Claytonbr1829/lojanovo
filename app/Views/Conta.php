<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">Minha Conta</h3>
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

                    <form action="<?= site_url('minha-conta') ?>" method="post">
                        <div class="row align-items-center mb-3">
                            <!-- buscar email cadastrado -->
                             <div-col-md-4>
                                <p><strong>Email:</strong> <?= esc($cliente['email']) ?></p>
                             </div-col-md-4>
                            <!-- Campo para escolha Pessoa Fisica ou Jurídico -->
                            <div class="col-md-3">
                                <label for="tipo" class="form-label">Tipo:</label>
                                <select class="form-select <?= session('errors.tipo') ? 'is-invalid' : '' ?>" id="tipo"
                                    name="tipo" required>
                                    <option value="" selected disabled>Selecione...</option>
                                    <option value="1" <?= old('tipo') == '1' ? 'selected' : '' ?>>Pessoa Física</option>
                                    <option value="2" <?= old('tipo') == '2' ? 'selected' : '' ?>>Pessoa Jurídica</option>
                                </select>
                                <?php if (session('errors.tipo')): ?>
                                    <div class="invalid-feedback"><?= session('errors.tipo') ?></div>
                                <?php endif ?>
                            </div>

                            <!-- Campo Nome/Empresa -->
                            <div class="col-md-9">
                                <label for="text_name" class="form-label">Nome/Razão Social:</label>
                                <input type="text"
                                    class="form-control <?= session('errors.nome') ? 'is-invalid' : '' ?>"
                                    id="text_name" name="nome" value="<?= old('nome') ?>" required>
                                <?php if (session('errors.nome')): ?>
                                    <div class="invalid-feedback"><?= session('errors.nome') ?></div>
                                <?php endif ?>
                            </div>
                        </div>
                        <!-- campo para escolha do cpf ou cnpj -->
                        <div class="row align-items-center mb-3">
                            <div class="col-md-5">
                                <label for="numero_documento" class="form-label">CPF/CNPJ:</label>
                                <input type="text"
                                    class="form-control <?= session('errors.numero_documento') ? 'is-invalid' : '' ?>"
                                    id="numero_documento" name="numero_documento"
                                    placeholder="000.000.000-00 ou 00.000.000/0000-00"
                                    value="<?= old('numero_documento') ?>" required>
                                <div id="doc-error" class="invalid-feedback" style="display: none;">
                                    <?= session('errors.numero_documento') ?? '' ?>
                                </div>
                            </div>

                            <!-- campo para o rg -->
                            <!-- RG -->
                            <div class="col-5">
                                <label for="numero_identidade" class="form-label">RG:</label>
                                <input type="text"
                                    class="form-control <?= session('errors.numero_identidade') ? 'is-invalid' : '' ?>"
                                    id="numero_identidade" name="numero_identidade"
                                    value="<?= old('numero_identidade') ?>" required>
                                <?php if (session('errors.numero_identidade')): ?>
                                    <div class="invalid-feedback"><?= session('errors.numero_identidade') ?></div>
                                <?php endif ?>
                            </div>

                            <!-- CEP -->
                            <div class="col-md-3">
                                <label for="cep" class="form-label">CEP:</label>
                                <input type="text" class="form-control <?= session('errors.cep') ? 'is-invalid' : '' ?>"
                                    id="cep" name="cep" placeholder="00000-000" value="<?= old('cep') ?>" required>
                                <?php if (session('errors.cep')): ?>
                                    <div class="invalid-feedback"><?= session('errors.cep') ?></div>
                                <?php endif ?>
                            </div>

                            <!-- Campos do logradouro -->
                            <div class="row align-items-center mb-3">
                                <div class="col-md-8">
                                    <label for="logradouro" class="form-label">Logradouro (Rua/Av.):</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="logradouro" name="logradouro"
                                            readonly required>
                                    </div>
                                </div>
                                <!-- campo do numero da casa -->
                                <div class="col-md-3">
                                    <label for="numero" class="form-label">Nº:</label>
                                    <input type="text" class="form-control" id="numero" name="numero" required>
                                </div>
                            </div>

                            <!-- compo para bairro -->
                            <div class="row align-items-center mb-3">
                                <div class="col-md-4">
                                    <label for="bairro" class="form-label">Bairro:</label>
                                    <input type="text" class="form-control" id="bairro" name="bairro" readonly required>
                                </div>
                                <!-- compo para cidade -->
                                <div class="col-md-6">
                                    <label for="cidade" class="form-label">Cidade:</label>
                                    <input type="text" class="form-control" id="cidade" name="cidade" readonly required>
                                </div>
                                <!-- compo para estado -->
                                <div class="col-md-2">
                                    <label for="uf" class="form-label">UF:</label>
                                    <input type="text" class="form-control" id="uf" name="uf" readonly required>
                                </div>
                            </div>

                            <div class="row align-items-center mb-3">
                                <!-- Telefone Celular -->
                                <div class="col-md-3">
                                    <label for="text_cel" class="form-label">Celular</label>
                                    <input type="text"
                                        class="form-control <?= session('errors.text_cel') ? 'is-invalid' : '' ?>"
                                        id="text_cel" name="text_cel" value="<?= old('text_cel') ?>" required>
                                    <?php if (session('errors.text_cel')): ?>
                                        <div class="invalid-feedback"><?= session('errors.text_cel') ?></div>
                                    <?php endif ?>
                                </div>
                                <!-- Telefone Fixo -->
                                <div class="col-md-3">
                                    <label for="text_fixo" class="form-label">Telefone Fixo</label>
                                    <input type="text"
                                        class="form-control <?= session('errors.text_fixo') ? 'is-invalid' : '' ?>"
                                        id="text_fixo" name="text_fixo" value="<?= old('text_fixo') ?>" required>
                                    <?php if (session('errors.text_fixo')): ?>
                                        <div class="invalid-feedback"><?= session('errors.text_fixo') ?></div>
                                    <?php endif ?>
                                </div>
                            </div>

                            <!-- Campo Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">E-mail</label>
                                <input type="email"
                                    class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>" id="email"
                                    name="email" value="<?= old('email') ?>" required>
                                <?php if (session('errors.email')): ?>
                                    <div class="invalid-feedback"><?= session('errors.email') ?></div>
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
<script>
    // Máscara para CPF/CNPJ
    document.getElementById('numero_documento').addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, '');

        if (value.length <= 11) {
            // Formata CPF: 000.000.000-00
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            value = value.substring(0, 14);
        } else {
            // Formata CNPJ: 00.000.000/0000-00
            value = value.replace(/(\d{2})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1/$2');
            value = value.replace(/(\d{4})(\d)/, '$1-$2');
            value = value.substring(0, 18);
        }

        e.target.value = value;
    });

    // Validação ao enviar o formulário
    document.querySelector('form').addEventListener('submit', function (e) {
        const docField = document.getElementById('numero_documento');
        const docValue = docField.value.replace(/\D/g, '');
        const docError = document.getElementById('doc-error');

        // Validação básica
        if (docValue.length === 0) {
            e.preventDefault();
            docError.textContent = 'CPF ou CNPJ é obrigatório';
            docError.style.display = 'block';
            return;
        }

        if (docValue.length === 11) {
            // Valida CPF básico (não permite todos dígitos iguais)
            if (/^(\d)\1{10}$/.test(docValue)) {
                e.preventDefault();
                docError.textContent = 'CPF inválido (não pode ter todos dígitos iguais)';
                docError.style.display = 'block';
            }
        } else if (docValue.length === 14) {
            // Valida CNPJ básico (não permite todos dígitos iguais)
            if (/^(\d)\1{13}$/.test(docValue)) {
                e.preventDefault();
                docError.textContent = 'CNPJ inválido (não pode ter todos dígitos iguais)';
                docError.style.display = 'block';
            }
        } else {
            e.preventDefault();
            docError.textContent = 'Documento invfálido (deve ter 11 ou 14 dígitos)';
            docError.style.display = 'block';
        }
    });


    document.getElementById('cep').addEventListener('blur', function (e) {
        const cep = this.value.replace(/\D/g, '');

        // Verifica se o CEP tem 8 dígitos
        if (cep.length !== 8) {
            alert('CEP inválido! Deve conter 8 dígitos.');
            return;
        }

        // Mostra loading (opcional)
        document.getElementById('logradouro').value = 'Buscando...';

        // Faz a requisição para a API ViaCEP
        fetch(`https://viacep.com.br/ws/${cep}/json/`)
            .then(response => response.json())
            .then(data => {
                if (data.erro) {
                    alert('CEP não encontrado!');
                    return;
                }

                // Preenche os campos
                document.getElementById('logradouro').value = data.logradouro;
                document.getElementById('bairro').value = data.bairro;
                document.getElementById('cidade').value = data.localidade;
                document.getElementById('uf').value = data.uf;

                // Foca no campo número automaticamente
                document.getElementById('numero').focus();
            })
            .catch(error => {
                console.error('Erro ao buscar CEP:', error);
                alert('Erro ao buscar CEP. Tente novamente.');
            });
    });

    // Máscara para o CEP
    document.getElementById('cep').addEventListener('input', function (e) {
        let value = this.value.replace(/\D/g, '');

        if (value.length > 5) {
            value = value.substring(0, 5) + '-' + value.substring(5, 8);
        }

        this.value = value;
    });

    document.getElementById('numero_identidade').addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, ''); // Remove tudo que não for dígito

        // Formatação do RG (padrão: 00.000.000-0)
        if (value.length > 2) value = value.replace(/^(\d{2})/, '$1.');
        if (value.length > 6) value = value.replace(/^(\d{2}\.\d{3})/, '$1.');
        if (value.length > 9) value = value.replace(/^(\d{2}\.\d{3})/, '$1-');

        // Limita o tamanho (RG geralmente tem 9 dígitos + pontos e traço = 12 caracteres)
        value = value.substring(0, 12);

        e.target.value = value;
    });

    // Formata Telefone Fixo: (00) 0000-0000
    document.getElementById('text_fixo').addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 0) {
            value = value.replace(/^(\d{2})/, '($1) ');
            if (value.length > 9) {
                value = value.replace(/(\d{4})(\d)/, '$1-$2');
            }
            e.target.value = value.substring(0, 14); // Limita ao formato completo
        }
    });

    // Formata Celular: (00) 00000-0000
    document.getElementById('text_cel').addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 0) {
            value = value.replace(/^(\d{2})/, '($1) ');
            if (value.length > 10) {
                value = value.replace(/(\d{5})(\d)/, '$1-$2');
            }
            e.target.value = value.substring(0, 15); // Limita ao formato completo
        }
    });

</script>