<?php
$title = 'Pagamento - ' . ($config['nome_loja'] ?? 'SwapShop');
ob_start();
?>

<body>
    <div class="container">
        <div class="row justify-content-center mt-4">
            <div class="col-md-10">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Dados do Pedido</h4>
                    </div>
                    <div class="card-body">
                        <p><strong>Número da Fatura:</strong> <?= $pedido['id_pedido'] ?></p>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Produto</th>
                                        <th class="text-center">Qtd</th>
                                        <th class="text-end">Preço Unitário</th>
                                        <th class="text-end">Subtotal</th>
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
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Frete:</td>
                                        <td class="text-end">R$
                                            <?= number_format($pedido['valor_frete'], 2, ',', '.') ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Total:</td>
                                        <td class="text-end">R$
                                            <?= number_format($pedido['preco_total'] + $pedido['valor_frete'], 2, ',', '.') ?>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <p><strong>Data:</strong> <?= date('d/m/Y', strtotime($pedido['data'])) ?></p>
                                <p><strong>Hora:</strong> <?= date('H:i', strtotime($pedido['hora'])) ?></p>
                                <p><strong>Status:</strong>
                                    <span
                                        class="badge bg-<?= $pedido['status_pedido'] === 'pago' ? 'success' : 'warning' ?>">
                                        <?= ucfirst($pedido['status_pedido']) ?>
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                </p>
                                <p><strong>Data de Vencimento:</strong>
                                    <?= date('d/m/y', strtotime($pedido['vencimento_pagamento'])) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center mt-4">
            <div class="col-md-10">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Dados do Comprador</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Nome:</strong> <?= $pedido['nome'] ?></p>
                                <p><strong>E-mail:</strong> <?= $pedido['email'] ?></p>
                                <p><strong>Documento:</strong> <?= $pedido['cpf'] ?></p>
                            </div>
                            <div class="col-md-6">
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
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($pedido['status_pedido'] !== 'pago'): ?>
            <div class="row justify-content-center mt-4">
                <div class="col-md-10">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">Dados do Pagamento</h4>
                        </div>
                        <div class="card-body">
                            <form id="formPagamento">
                                <div class="mb-3">
                                    <label class="form-label">Forma de Pagamento:</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="formaPagamento" id="pix"
                                            value="pix" checked>
                                        <label class="form-check-label" for="pix">PIX</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="formaPagamento" id="boleto"
                                            value="boleto">
                                        <label class="form-check-label" for="boleto">Boleto</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="formaPagamento" id="credit_card"
                                            value="credit_card">
                                        <label class="form-check-label" for="credit_card">Cartão de Crédito</label>
                                    </div>
                                </div>

                                <div class="checkout-container" style="display:none;">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="numero_cartao" class="form-label">Número do Cartão:</label>
                                            <input type="text" class="form-control" id="numero_cartao" name="numero_cartao"
                                                placeholder="Digite o número do cartão" value="1234567890123456" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="cpf" class="form-label">CPF:</label>
                                            <input type="text" class="form-control" id="cpf" name="cpf"
                                                placeholder="Digite seu CPF" required value="12345678912">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="validade_mes" class="form-label">Validade (MM):</label>
                                            <input type="text" class="form-control" id="validade_mes" name="validade_mes"
                                                placeholder="MM" required maxlength="2" value="03">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="validade_ano" class="form-label">Validade (AAAA):</label>
                                            <input type="text" class="form-control" id="validade_ano" name="validade_ano"
                                                placeholder="AA" required maxlength="4" value="2025">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="cvv" class="form-label">CVV:</label>
                                            <input type="text" class="form-control" id="cvv" name="cvv"
                                                placeholder="Código de segurança (CVV)" value="123" required>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="nome_titular" class="form-label">Nome do Titular:</label>
                                            <input type="text" class="form-control" id="nome_titular" name="nome_titular"
                                                placeholder="Nome do titular do cartão" value="John Doe" required>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" id="btnAvancar" class="btn btn-primary">Fazer pagamento</button>
                            </form>

                            <div id="qrCodeContainer" class="mt-3 text-center"></div>
                            <div id="boletoContainer" class="mt-3"></div>
                            <div id="boletoCodigoContainer" class="mt-3 text-center"></div>
                            <div id="boletoBotao" class="mt-3 text-center"></div>
                            <div id="cartaoContainer" class="mt-3 text-center"></div>
                            <div id="cartaoBotao" class="mt-3 text-center"></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <input type="hidden" name="token" id="token" value="<?= $pedido['token'] ?>">
    <input type="hidden" name="preco_total" id="preco_total" value="<?= $pedido['preco_total'] ?>">

    <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.0/dist/JsBarcode.all.min.js"></script>

    <script>
        // Código para comunicação com a extensão
        chrome.runtime.onMessage.addListener((request, sender, sendResponse) => {
            if (request.action === 'processPayment') {
                // Simula o processamento do pagamento
                processarPagamento(request.dados).then(resultado => {
                    sendResponse({ success: true, data: resultado });
                }).catch(erro => {
                    sendResponse({ success: false, error: erro.message });
                });

                // Indica que a resposta será assíncrona
                return true;
            }
        });

        async function processarPagamento(dados) {
            // Aqui você integraria com sua API de pagamento
            return new Promise((resolve) => {
                setTimeout(() => {
                    resolve({
                        transactionId: 'tx_' + Math.random().toString(36).substr(2, 9),
                        status: 'completed'
                    });
                }, 1500);
            });
        }

        // Opcional: Enviar mensagem para a extensão quando a página carregar
        if (typeof chrome !== 'undefined' && chrome.runtime) {
            chrome.runtime.sendMessage({
                type: 'paymentPageLoaded',
                orderId: '<?= $pedido['id_pedido'] ?>',
                amount: '<?= $pedido['preco_total'] ?>'
            });
        }
    </script>

    <script>
        $(document).ready(function () {
            // Mostrar ou ocultar campos para cartão de crédito
            $('input[name="formaPagamento"]').on('change', function () {
                $('.checkout-container').toggle($(this).val() === 'credit_card');
            });

            // Função para processar o pagamento via AJAX
            $('#btnAvancar').on('click', function () {
                if (!$('input[name="formaPagamento"]:checked').length) {
                    alert('Por favor, selecione uma forma de pagamento.');
                    return;
                }

                if ($('input[name="formaPagamento"]:checked').val() === 'credit_card') {
                    if (!$('#numero_cartao').val() || !$('#cvv').val() || !$('#nome_titular').val()) {
                        alert('Por favor, preencha todos os campos do cartão.');
                        return;
                    }
                }

                let token = $('#token').val();
                let valor = $('#preco_total').val();
                let formaPagamento = $('input[name="formaPagamento"]:checked').val();
                let numero_cartao = $('#numero_cartao').val();
                let cpf = $('#cpf').val();
                let validade_mes = $('#validade_mes').val();
                let validade_ano = $('#validade_ano').val();
                let cvv = $('#cvv').val();
                let nome_titular = $('#nome_titular').val();

                $(this).prop('disabled', true).text('Aguarde...');

                $.ajax({
                    url: '/pedido/pagar/<?= $pedido['token'] ?>',
                    method: 'POST',
                    data: {
                        valor: valor,
                        formaPagamento: formaPagamento,
                        token: token,
                        numero_cartao: numero_cartao,
                        cpf: cpf,
                        validade_mes: validade_mes,
                        validade_ano: validade_ano,
                        cvv: cvv,
                        nome_titular: nome_titular,
                    },
                    success: function (response) {
                        try {
                            let data = response;

                            console.log(data)

                            if (data.status === 0) {
                                $('#qrCodeContainer, #boletoContainer, #boletoCodigoContainer, #boletoBotao, #cartaoContainer, #cartaoBotao').empty();

                                if (formaPagamento === 'pix' && data.data.pix_qr_code) {
                                    $('#qrCodeContainer').html(`
                                        <p>Pagamento via PIX. Use o QR Code abaixo para pagar.</p>
                                        <img src="data:image/png;base64,${data.data.pix_qr_code}" alt="QR Code PIX" style="width:200px;height:200px;">
                                        <div class="mt-2">
                                            <button class="btn btn-primary" onclick="window.location.href='/'">Já paguei</button>
                                        </div>
                                    `);
                                }
                                else if (formaPagamento === 'boleto' && data.data.boleto_linha_digitavel) {
                                    $('#boletoContainer').html(`<p class="text-center">Linha Digitável: ${data.data.boleto_linha_digitavel}</p>`);

                                    let barcodeImage = document.createElement("img");
                                    JsBarcode(barcodeImage, data.data.boleto_linha_digitavel, {
                                        format: "CODE128",
                                        displayValue: false,
                                        height: 30,
                                        margin: 10,
                                    });
                                    $('#boletoCodigoContainer').append(barcodeImage);

                                    $('#boletoBotao').html(`
                                        <div class="d-flex justify-content-center gap-2">
                                            <button class="btn btn-primary" onclick="window.open('${data.data.link_pagamento}', '_blank')">Download do Boleto</button>
                                            <button class="btn btn-primary" onclick="window.location.href='/cobranca'">Já paguei</button>
                                        </div>
                                    `);
                                }
                                else if (formaPagamento === 'credit_card') {
                                    $('#cartaoContainer').html(`
                                        <p>Pagamento via cartão de crédito processado com sucesso.</p>
                                        <button class="btn btn-primary" onclick="window.open('${data.data.comprovante}', '_blank')">Ver comprovante</button>
                                    `);
                                    $('#cartaoBotao').html(`
                                        <button class="btn btn-primary mt-2" onclick="window.location.href='/'">Já paguei</button>
                                    `);
                                }
                            } else {
                                alert('Erro: ' + data.mensagem);
                            }
                        } catch (e) {
                            alert('Erro ao processar a resposta do servidor.');
                        }
                    },
                    error: function () {
                        alert('Erro ao processar pagamento. Tente novamente.');
                    },
                    complete: function () {
                        $('#btnAvancar').prop('disabled', false).text('Fazer pagamento');
                    }
                });
            });
            chrome.runtime.sendMessage({
                action: 'initiatePayment',
                orderData: {
                    amount: valor,
                    paymentMethod: formaPagamento,
                    orderId: '<?= $pedido['id_pedido'] ?>'
                }
            }, function (response) {
                if (response.success) {
                    console.log('Extensão processou o pagamento', response);
                }
            });
        });
    </script>

    <style>
        .checkout-container {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .badge {
            font-size: 0.9em;
            padding: 0.5em 0.75em;
        }

        .card {
            margin-bottom: 20px;
        }

        .form-check {
            margin-bottom: 10px;
        }
    </style>
</body>