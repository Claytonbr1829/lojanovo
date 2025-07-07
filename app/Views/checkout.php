<div class="container mt-4 mb-5">
    <h1 class="mb-4"><?= lang('Checkout.checkout') ?></h1>

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

    <div class="row">
        <!-- Informações do Cliente e Endereço -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><?= lang('Checkout.steps.login') ?></h4>
                </div>
                <div class="card-body">
                    <?php if (isset($cliente)): ?>

                        <!-- Cliente logado -->
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <!-- Onde você acessa o nome do cliente -->
                                <p class="mb-0">
                                    <strong><?= $cliente['nome'] ?? $cliente['razao_social'] ?? 'Nome não disponível' ?></strong>
                                </p>
                                <p class="mb-0"><?= $cliente['email'] ?></p>
                                <p class="mb-0"><?= $cliente['celular_1'] ?? '' ?></p>
                            </div>
                            <a href="<?= site_url('cliente/logout') ?>"
                                class="btn btn-outline-primary"><?= lang('Users.auth.logout') ?></a>
                        </div>
                    <?php else: ?>
                        <!-- Cliente não logado -->
                        <div class="row">
                            <div class="col-md-6">
                                <h5><?= lang('Users.auth.login') ?></h5>
                                <p><?= lang('Checkout.login.guest') ?></p>
                                <p><a href="<?= site_url('login') ?>"
                                        class="btn btn-primary"><?= lang('Users.auth.login') ?></a></p>
                            </div>
                            <div class="col-md-6">
                                <h5><?= lang('Users.auth.register') ?></h5>
                                <p><?= lang('Users.messages.new_account') ?></p>
                                <p><a href="<?= site_url('cliente/cadastro') ?>"
                                        class="btn btn-outline-primary"><?= lang('Users.auth.register') ?></a></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <form action="<?= site_url('checkout/processar') ?>" method="post">
                <?php if (!isset($cliente)): ?>
                    <!-- Formulário para não logados -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0"><?= lang('User.personal_info') ?></h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nome" class="form-label"><?= lang('Users.fields.name') ?></label>
                                    <input type="text" class="form-control" id="nome" name="nome" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label"><?= lang('Users.fields.email') ?></label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="telefone" class="form-label"><?= lang('Users.fields.phone') ?></label>
                                    <input type="tel" class="form-control" id="telefone" name="telefone">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="cpf" class="form-label"><?= lang('Contact.cpf') ?></label>
                                    <input type="text" class="form-control" id="cpf" name="cpf">
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Endereço de Entrega -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><?= lang('Checkout.shipping.address') ?></h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="cep" class="form-label"><?= lang('Checkout.address.zip_code') ?></label>
                                <input type="text" class="form-control" id="cep" name="cep" required
                                    value="<?= $endereco_preenchido['cep'] ?? '' ?>">
                            </div>
                            <div class="col-md-8 mb-3">
                                <label for="logradouro"
                                    class="form-label"><?= lang('Checkout.address.street') ?></label>
                                <input type="text" class="form-control" id="logradouro" name="logradouro" required
                                    value="<?= $endereco_preenchido['logradouro'] ?? '' ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="numero" class="form-label"><?= lang('Checkout.address.number') ?></label>
                                <input type="text" class="form-control" id="numero" name="numero" required
                                    value="<?= $endereco_preenchido['numero'] ?? '' ?>">
                            </div>
                            <div class="col-md-8 mb-3">
                                <label for="complemento"
                                    class="form-label"><?= lang('Checkout.address.complement') ?></label>
                                <input type="text" class="form-control" id="complemento" name="complemento"
                                    value="<?= $endereco_preenchido['complemento'] ?? '' ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="bairro"
                                    class="form-label"><?= lang('Checkout.address.neighborhood') ?></label>
                                <input type="text" class="form-control" id="bairro" name="bairro" required
                                    value="<?= $endereco_preenchido['bairro'] ?? '' ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="id_municipio"
                                    class="form-label"><?= lang('Checkout.address.city') ?></label>
                                <input type="text" class="form-control" id="id_municipio" name="id_municipio" required
                                    value="<?= $endereco_preenchido['municipio'] ?? '' ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="id_uf" class="form-label"><?= lang('Checkout.address.state') ?></label>
                                <select class="form-select" id="id_uf" name="id_uf" required>
                                    <option value=""><?= lang('System/system_info/select_state') ?></option>
                                    <?php foreach ($estados as $estado): ?>
                                        <option value="<?= $estado['id_uf'] ?>" <?= (isset($cliente['id_uf']) && $cliente['id_uf'] == $estado['id_uf']) ||
                                              (isset($endereco_preenchido['id_uf']) && $endereco_preenchido['id_uf'] == $estado['id_uf']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($estado['estado'], ENT_QUOTES, 'UTF-8') ?>
                                        </option>

                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- calcular frete -->
                <div class="card mb-4">
                    <div id="divEstimaFrete">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0"><?= lang('Checkout.review.calcular') ?></h4>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-center flex-column align-items-center">
                                <!-- Loader -->
                                <div id="loadprocessarNovo" style="display:none" class="text-center">
                                    <div class="mb-2">Aguarde, calculando frete!</div>
                                    <img id="imgLoadX" src="<?= base_url('assets/img/load2.gif') ?>"
                                        style="width: 50px; height: 50px;">
                                </div>

                                <!-- Retorno do CEP -->
                                <div id="retornoCep" class="w-100 mt-3"></div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="frete_valor" value="">
                    <input type="hidden" name="frete_prazo" value="">
                    <input type="hidden" name="frete_servico" value="">
                    <input type="hidden" name="valor_total" id="valor_total"
                        value="<?= number_format($total, 2, '.', '') ?>">
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-success btn-lg"><?= lang('Checkout.review.confirm') ?></button>
                </div>
            </form>
        </div>

        <!-- Resumo do Pedido -->
        <div class="col-md-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><?= lang('Checkout.checkout_summary') ?></h4>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h5><?= lang('Checkout.review.items') ?></h5>
                        <ul class="list-group mb-3">
                            <?php foreach ($itens as $item): ?>
                                <?php
                                $preco = $item['preco'] ?? $item['preco_unitario'] ?? 0;
                                $subtotal = $item['subtotal'] ?? ($preco * $item['quantidade']);
                                $nome = $item['nome'] ?? $item['produto']['nome'] ?? 'Produto sem nome';
                                ?>
                                <li>
                                    <div
                                        class="list-group-item d-flex justify-content-between align-items-center  border border-0">
                                        <div class="d-flex align-items-center">
                                            <!-- Detalhes do produto -->
                                            <div>
                                                <h6 class="my-0"><?= $nome ?? 'Produto sem nome' ?></h6>
                                                <small class="text-muted">
                                                    <?php
                                                    $preco = $item['preco'] ?? $item['preco_unitario'] ?? 0;
                                                    ?>
                                                    <?= $item['quantidade'] ?> x R$
                                                    <?= number_format($preco, 2, ',', '.') ?>

                                                </small>
                                            </div>
                                        </div>
                                        <span class="text-nowrap">R$
                                            <?= number_format($item['subtotal'] ?? ($preco * $item['quantidade']), 2, ',', '.') ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="mb-3">
                        <h5><?= lang('Checkout.review.total') ?></h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span><?= lang('Checkout.review.subtotal') ?></span>
                            <span id="resumo-subtotal">R$ <?= number_format($total, 2, ',', '.') ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span><?= lang('Checkout.review.shipping_cost') ?></span>
                            <span id="resumo-frete-valor">R$ 0,00</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold"><?= lang('Checkout.review.final_total') ?></span>
                            <span class="fw-bold" id="resumo-total">R$ <?= number_format($total, 2, ',', '.') ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Função para formatar CEP -->
<script>
    const clienteTemCEP = <?= (isset($cliente['cep']) && !empty($cliente['cep'])) ? 'true' : 'false' ?>;

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

    function formatarMoeda(valor) {
        return 'R$ ' + valor.toFixed(2).replace('.', ',').replace(/(\d)(?=(\d{3})+\,)/g, '$1.');
    }

    function parseMoeda(valor) {
        return parseFloat(valor.replace('R$ ', '').replace(/\./g, '').replace(',', '.')) || 0;
    }

    function atualizarResumoPedido(frete) {
        if (Array.isArray(frete)) frete = frete[0];
        if (!frete || isNaN(frete.valor)) return;

        const subtotal = parseMoeda($('#resumo-subtotal').text());
        const total = subtotal + parseFloat(frete.valor);

        $('#resumo-frete-valor').text(formatarMoeda(frete.valor));
        $('#resumo-total').text(formatarMoeda(total));

        $('input[name="frete_valor"]').val(frete.valor);
        $('input[name="frete_prazo"]').val(frete.prazo);
        $('input[name="frete_servico"]').val(frete.servico);
        $('#valor_total').val(total.toFixed(2));
    }

    function configurarEventosFrete() {
        $(document).off('change', '.opcao-frete').on('change', '.opcao-frete', function () {
            if (this.checked) {
                const frete = {
                    valor: parseFloat($(this).data('valor')) || 0,
                    prazo: $(this).data('prazo') || '',
                    servico: $(this).data('servico') || ''
                };
                atualizarResumoPedido(frete);
            }
        });
        const primeiraOpcao = $('.opcao-frete').first();
        if (primeiraOpcao.length) {
            primeiraOpcao.prop('checked', true).trigger('change');
        }
    }
    // Se já existir uma opção de frete marcada, aplica os valores no resumo
    const opcaoSelecionada = $('.opcao-frete:checked');
    if (opcaoSelecionada.length > 0) {
        const frete = {
            valor: parseFloat(opcaoSelecionada.data('valor')) || 0,
            prazo: opcaoSelecionada.data('prazo') || '',
            servico: opcaoSelecionada.data('servico') || ''
        };
        atualizarResumoPedido(frete);
    }

    const clienteTemCEPValido = <?= (isset($cliente['cep']) && !empty($cliente['cep']) && preg_match('/^[0-9]{8}$/', $cliente['cep'])) ? 'true' : 'false' ?>;

    // function calcularFrete() {
    //     const cep = $('#cep').val().replace(/\D/g, '');
    //     if (!/^[0-9]{8}$/.test(cep)) {
    //         showMessage('Por favor, informe um CEP válido com exatamente 8 dígitos', 'warning');
    //         return;
    //     }

    //     $('#loadprocessarNovo').show();
    //     $('#retornoCep').html('');

    //     $.ajax({
    //         url: '<?= base_url('calcular-frete') ?>',
    //         type: 'POST',
    //         dataType: 'json',
    //         data: { cep: cep },
    //         success: function (response) {
    //             $('#loadprocessarNovo').hide();

    //             if (response.success) {
    //                 $('#retornoCep').html(response.data.html);
    //                 const primeiraOpcao = $('.opcao-frete').first();
    //                 if (primeiraOpcao.length) {
    //                     primeiraOpcao.prop('checked', true).trigger('change');
    //                 }
    //                 configurarEventosFrete();

    //                 // Verificar se já existe endereço de entrega antes de salvar
    //                 const isClienteLogado = <?= session()->has('cliente') ? 'true' : 'false' ?>;
    //                 const enderecoJaCadastrado = <?= isset($enderecoEntrega) && !empty($enderecoEntrega) ? 'true' : 'false' ?>;

    //                 if (isClienteLogado && !enderecoJaCadastrado) {
    //                     const logradouro = $('#logradouro').val();
    //                     const numero = $('#numero').val();
    //                     const bairro = $('#bairro').val();
    //                     const municipio = $('#id_municipio').val();
    //                     const estado = $('#id_uf option:selected').text(); // Pegar o texto do estado selecionado

    //                     if (cep && logradouro && numero && bairro && municipio && estado) {
    //                         $.ajax({
    //                             url: '<?= site_url('checkout/salvarendereco') ?>',
    //                             method: 'POST',
    //                             contentType: 'application/json',
    //                             data: JSON.stringify({
    //                                 cep: cep,
    //                                 logradouro: logradouro,
    //                                 numero: numero,
    //                                 bairro: bairro,
    //                                 complemento: $('#complemento').val() || null,
    //                                 municipio: municipio,
    //                                 estado: estado // Usar o valor correto
    //                             }),
    //                             success: function (res) {
    //                                 console.log('Resposta do servidor:', res);
    //                                 if (res.success) {
    //                                     showMessage('Endereço salvo com sucesso!', 'success');
    //                                 } else {
    //                                     showMessage('Erro: ' + (res.error || 'Erro desconhecido'), 'danger');
    //                                 }
    //                             },
    //                             error: function (err) {
    //                                 console.error('Erro na requisição:', err);
    //                                 let msg = err.responseJSON?.error || 'Erro de comunicação com o servidor';
    //                                 showMessage('Erro ao salvar endereço: ' + msg, 'danger');
    //                             }
    //                         });
    //                     }
    //                 }
    //             } else {
    //                 showMessage(response.message || 'Erro ao calcular frete');
    //             }
    //         },
    //         error: function (xhr) {
    //             $('#loadprocessarNovo').hide();
    //             let msg = 'Erro na comunicação com o servidor';
    //             if (xhr.responseJSON && xhr.responseJSON.error) {
    //                 msg = xhr.responseJSON.error;
    //             }
    //             showMessage(`Erro: ${msg} (Código: ${xhr.status})`, 'danger');
    //         }
    //     });
    // }

    $(document).ready(function () {
        // Verifica se há endereço preenchido e calcula frete
        if ($('#cep').val().replace(/\D/g, '').length === 8) {
            setTimeout(() => {
                calcularFrete();
            }, 500);
        }

        // Formatação do CEP
        $('#cep').on('input', function () {
            let value = $(this).val().replace(/\D/g, '');
            if (value.length > 5) {
                value = value.substring(0, 5) + '-' + value.substring(5, 8);
            }
            $(this).val(value);

            // Calcula frete quando CEP estiver completo
            if (value.length === 9) {
                calcularFrete();
            }
        });

        // Busca ViaCEP quando perde o foco
        $('#cep').on('blur', async function () {
            const cep = $(this).val().replace(/\D/g, '');
            if (cep.length !== 8) return;

            try {
                $(this).prop('disabled', true);
                const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
                const data = await response.json();

                if (data.erro) throw new Error('CEP não encontrado');

                // Preenche os campos automaticamente
                $('#logradouro').val(data.logradouro || '');
                $('#bairro').val(data.bairro || '');
                $('#id_municipio').val(data.localidade);

                // Seleciona o estado correspondente
                const ufOption = $('#id_uf option').filter(function () {
                    return $(this).text().trim() === data.uf;
                }).first();

                if (ufOption.length) {
                    ufOption.prop('selected', true);
                }

                // Calcula o frete automaticamente
                calcularFrete();

            } catch (error) {
                console.error('Erro ao buscar CEP:', error);
                showMessage('CEP não encontrado. Por favor, verifique.', 'danger');
            } finally {
                $(this).prop('disabled', false);
            }
        });
    });

    function calcularFrete() {
        const cep = $('#cep').val().replace(/\D/g, '');
        if (!/^[0-9]{8}$/.test(cep)) {
            showMessage('Por favor, informe um CEP válido com exatamente 8 dígitos', 'warning');
            return;
        }

        $('#loadprocessarNovo').show();
        $('#retornoCep').html('');

        $.ajax({
            url: '<?= base_url('calcular-frete') ?>',
            type: 'POST',
            dataType: 'json',
            data: { cep: cep },
            success: function (response) {
                $('#loadprocessarNovo').hide();
                if (response.success) {
                    $('#retornoCep').html(response.data.html);
                    configurarEventosFrete();
                    $('.opcao-frete').first().prop('checked', true).trigger('change');
                } else {
                    showMessage(response.message || 'Erro ao calcular frete');
                }
            },
            error: function (xhr) {
                $('#loadprocessarNovo').hide();
                showMessage('Erro ao calcular frete. Tente novamente.', 'danger');
            }
        });
    }

</script>