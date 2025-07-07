<?php
// Verifica se é apenas edição de endereço
$isEdicaoEndereco = $edicaoEndereco ?? false;
$cliente = $cliente ?? null;
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">

                <div class="card-body p-0">
                    <?php if (session('errors')): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php foreach (session('errors') as $erro): ?>
                                    <li><?= esc($erro) ?></li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                    <?php endif ?>

                    <form id="mainForm" action="<?= site_url('cliente/salvar-endereco') ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="card-header bg-primary text-white">
                            <h3 class="card-title mb-0">Dados Pessoais</h3>
                        </div>
                        <div class="m-3">
                            <div class="row mb-3 mt-4">
                                <div class="col-md-6">
                                    <label for="nome" class="form-label">Nome Completo*</label>
                                    <input type="text" class="form-control" id="nome" name="nome"
                                        value="<?= old('nome', $cliente['nome'] ?? '') ?>" required>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="tipo" class="form-label">Tipo de Pessoa*</label>
                                        <select class="form-control" id="tipo" name="tipo" required>
                                            <option value="1" <?= (old('tipo', $cliente['tipo'] ?? '1') == '1' ? 'selected' : '') ?>>Pessoa Física</option>
                                            <option value="2" <?= (old('tipo', $cliente['tipo'] ?? '1') == '2' ? 'selected' : '') ?>>Pessoa Jurídica</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="cpf_cnpj" class="form-label" id="label-documento">
                                            <?= (old('tipo', $cliente['tipo'] ?? '1') == '1') ? 'CPF*' : 'CNPJ*' ?>
                                        </label>
                                        <input type="text" class="form-control" id="cpf_cnpj" name="cpf_cnpj"
                                            value="<?= old('cpf_cnpj', $cliente['cpf_cnpj'] ?? '') ?>" required
                                            placeholder="<?= (old('tipo', $cliente['tipo'] ?? '1') == '1') ? '000.000.000-00' : '00.000.000/0000-00' ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email*</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="<?= old('email', $cliente['email'] ?? '') ?>" required>
                                </div>

                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="fixo" class="form-label">Telefone Fixo</label>
                                    <input type="text" class="form-control" id="fixo" name="fixo"
                                        value="<?= old('fixo', $cliente['fixo'] ?? '') ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="celular_1" class="form-label">Celular_1*</label>
                                    <input type="text" class="form-control" id="celular_1" name="celular_1"
                                        value="<?= old('celular_1', $cliente['celular_1'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="celular_2" class="form-label">Celular_2</label>
                                    <input type="text" class="form-control" id="celular_2" name="celular_2"
                                        value="<?= old('celular_2', $cliente['celular_2'] ?? '') ?>">
                                </div>

                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="data_de_nascimento" class="form-label">Data de Nascimento*</label>
                                    <input type="date" class="form-control" id="data_de_nascimento"
                                        name="data_de_nascimento"
                                        value="<?= old('data_de_nascimento', $cliente['data_de_nascimento'] ?? '') ?>"
                                        required>
                                </div>

                            </div>
                        </div>

                        <!-- <hr class="my-4"> -->
                        <div class="card-header bg-primary text-white">
                            <h3 class="card-title mb-0">Dados Endereço</h3>
                        </div>
                        <div class="m-3">
                            <div class="row mb-3 mt-4">
                                <div class="col-md-4">
                                    <label for="cep" class="form-label">CEP*</label>
                                    <input type="text" class="form-control" id="cep" name="cep"
                                        value="<?= old('cep', $cliente['cep'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-8">
                                    <label for="logradouro" class="form-label">Logradouro*</label>
                                    <input type="text" class="form-control" id="logradouro" name="logradouro"
                                        value="<?= old('logradouro', $cliente['logradouro'] ?? '') ?>" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="numero" class="form-label">Número*</label>
                                    <input type="text" class="form-control" id="numero" name="numero"
                                        value="<?= old('numero', $cliente['numero'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="bairro" class="form-label">Bairro*</label>
                                    <input type="text" class="form-control" id="bairro" name="bairro"
                                        value="<?= old('bairro', $cliente['bairro'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="complemento" class="form-label">Complemento</label>
                                    <input type="text" class="form-control" id="complemento" name="complemento"
                                        value="<?= old('complemento', $cliente['complemento'] ?? '') ?>">
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="municipio" class="form-label">Cidade*</label>
                                    <input type="text" class="form-control" id="municipio" name="municipio"
                                        value="<?= old('municipio', $cliente['municipio'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="id_uf" class="form-label">UF*</label>
                                    <select class="form-control" id="id_uf" name="id_uf" required>
                                        <option value="">Selecione um estado</option>
                                        <?php foreach ($ufs as $estado): ?>
                                            <option value="<?= $estado['id_uf'] ?>" <?= (old('id_uf', $cliente['id_uf'] ?? '') == $estado['id_uf'] ? 'selected' : '') ?>>
                                                <?= $estado['estado'] ?>
                                            </option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary w-100"
                                onclick="onSubmitForm(event);">Salvar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Máscara para telefone fixo
    function aplicarMascaraTelefoneFixo(input) {
        input?.addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 6) {
                value = value.replace(/^(\d{2})(\d{4})(\d{0,4})$/, '$1 $2-$3');
            } else if (value.length > 2) {
                value = value.replace(/^(\d{2})(\d{0,4})$/, '$1 $2');
            }
            e.target.value = value.substring(0, 12);
        });
    }

    // Máscara para celular
    function aplicarMascaraTelefoneCelular(input) {
        input?.addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 7) {
                value = value.replace(/^(\d{2})(\d{5})(\d{0,4})$/, '$1 $2-$3');
            } else if (value.length > 2) {
                value = value.replace(/^(\d{2})(\d{0,5})$/, '$1 $2');
            }
            e.target.value = value.substring(0, 13);
        });
    }

    // Função para preencher o formulário com os dados do cliente
    function preencherFormulario() {
        const cliente = <?= json_encode($cliente ?? []) ?>;

        if (!cliente || Object.keys(cliente).length === 0) return;

        document.getElementById('nome').value = cliente.nome || '';
        document.getElementById('tipo').value = cliente.tipo || '1';
        document.getElementById('cpf_cnpj').value = cliente.tipo == '1' ? (cliente.cpf || '') : (cliente.cnpj || '');
        document.getElementById('email').value = cliente.email || '';
        document.getElementById('fixo').value = cliente.fixo || '';
        document.getElementById('celular_1').value = cliente.celular_1 || '';
        document.getElementById('celular_2').value = cliente.celular_2 || '';
        document.getElementById('data_de_nascimento').value = cliente.data_de_nascimento || '';
        document.getElementById('cep').value = cliente.cep || '';
        document.getElementById('logradouro').value = cliente.logradouro || '';
        document.getElementById('numero').value = cliente.numero || '';
        document.getElementById('bairro').value = cliente.bairro || '';
        document.getElementById('complemento').value = cliente.complemento || '';
        document.getElementById('municipio').value = cliente.municipio || '';

        const ufSelect = document.getElementById('id_uf');
        if (ufSelect && cliente.id_uf) {
            ufSelect.value = cliente.id_uf;
        }

        atualizarMascaraDocumento(cliente.tipo || '1');
    }


    async function onSubmitForm(e) {
        e.preventDefault();

        // Coletar dados do formulário normalmente (não como JSON)
        const form = document.querySelector('#mainForm');
        const formData = new FormData(form);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest' // Importante para o CI
                },
                body: formData // Envia como FormData, não como JSON
            });

            // Verificar se a resposta é JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                throw new Error(`Resposta inesperada: ${text.substring(0, 100)}`);
            }

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error || 'Erro ao processar requisição');
            }

            if (!data.success) {
                throw new Error(data.message || 'Operação não foi bem-sucedida');
            }

            // Sucesso
            showMessage(data.message || 'Dados salvos com sucesso!', 'success');

            // Redirecionar se houver URL
            if (data.redirect) {
                window.location.href = data.redirect;
            }

        } catch (error) {
            const responseText = await error?.response?.text?.(); // caso tenha HTML
            console.error('Erro detalhado:', responseText || error);
            showMessage(error.message || 'Erro ao processar', 'danger');
        };

    }

    document.addEventListener('DOMContentLoaded', function () {
        // Preenche o formulário com os dados do cliente
        preencherFormulario();

        const tipoPessoa = document.getElementById('tipo');
        const docField = document.getElementById('cpf_cnpj');
        const labelDoc = document.getElementById('label-documento');

        aplicarMascaraTelefoneFixo(document.getElementById('fixo'));
        aplicarMascaraTelefoneCelular(document.getElementById('celular_1'));
        aplicarMascaraTelefoneCelular(document.getElementById('celular_2'));

        // Atualiza máscara inicial
        if (tipoPessoa && docField && labelDoc) {
            atualizarMascaraDocumento(tipoPessoa.value);

            tipoPessoa.addEventListener('change', function () {
                docField.value = '';
                atualizarMascaraDocumento(this.value);
            });

            docField.addEventListener('input', function (e) {
                aplicarMascaraDinamica(e.target.value, tipoPessoa.value);
            });
        }

        // Máscara para CEP
        document.getElementById('cep')?.addEventListener('blur', function () {
            const cep = this.value.replace(/\D/g, '');
            if (cep.length !== 8) return;

            // Só busca se campos estiverem vazios
            const logradouro = document.getElementById('logradouro');
            const bairro = document.getElementById('bairro');
            const municipio = document.getElementById('municipio');

            if (logradouro.value || bairro.value || municipio.value) return;

            logradouro.value = 'Buscando...';
            bairro.value = 'Buscando...';
            municipio.value = 'Buscando...';

            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(response => response.json())
                .then(data => {
                    if (data.erro) {
                        alert('CEP não encontrado!');
                        logradouro.value = '';
                        bairro.value = '';
                        municipio.value = '';
                        return;
                    }

                    logradouro.value = data.logradouro || '';
                    bairro.value = data.bairro || '';
                    municipio.value = data.localidade || '';

                    const ufSelect = document.getElementById('id_uf');
                    if (ufSelect && data.uf) {
                        for (let i = 0; i < ufSelect.options.length; i++) {
                            if (
                                ufSelect.options[i].text === data.uf ||
                                ufSelect.options[i].label === data.uf
                            ) {
                                ufSelect.selectedIndex = i;
                                break;
                            }
                        }
                    }

                    document.getElementById('numero').focus();
                })
                .catch(() => alert('Erro ao buscar CEP. Tente novamente.'));
        });


        // Validação de idade mínima
        document.getElementById('data_de_nascimento')?.addEventListener('change', function () {
            const dataNasc = new Date(this.value);
            const hoje = new Date();
            let idade = hoje.getFullYear() - dataNasc.getFullYear();
            const mes = hoje.getMonth() - dataNasc.getMonth();

            if (mes < 0 || (mes === 0 && hoje.getDate() < dataNasc.getDate())) {
                idade--;
            }

            if (idade < 18) {
                alert('Você deve ter pelo menos 18 anos para se cadastrar.');
                this.value = '';
            }
        });

        function showMessage(message, type = 'danger') {
            const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>`;

            let container = document.getElementById('messages-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'messages-container';
                container.className = 'fixed-top mt-5 mx-auto w-75';
                document.body.prepend(container);
            }

            container.innerHTML = '';
            container.insertAdjacentHTML('afterbegin', alertHtml);

            setTimeout(() => {
                const alert = container.querySelector('.alert');
                if (alert) {
                    alert.classList.remove('show');
                    setTimeout(() => alert.remove(), 150);
                }
            }, 5000);
        }

        window.showMessage = showMessage;
    });

    function atualizarMascaraDocumento(tipo) {
        const docField = document.getElementById('cpf_cnpj');
        const labelDoc = document.getElementById('label-documento');

        if (!docField || !labelDoc) return;

        if (tipo === '1') {
            docField.placeholder = '000.000.000-00';
            labelDoc.textContent = 'CPF*';
            docField.maxLength = 14;
        } else {
            docField.placeholder = '00.000.000/0000-00';
            labelDoc.textContent = 'CNPJ*';
            docField.maxLength = 18;
        }
    }

    function aplicarMascaraDinamica(valor, tipo) {
        const docField = document.getElementById('cpf_cnpj');
        let value = valor.replace(/\D/g, '');

        if (tipo === '1') {
            if (value.length > 11) value = value.substring(0, 11);
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        } else {
            if (value.length > 14) value = value.substring(0, 14);
            value = value.replace(/(\d{2})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1/$2');
            value = value.replace(/(\d{4})(\d)/, '$1-$2');
        }

        docField.value = value;
    }
</script>