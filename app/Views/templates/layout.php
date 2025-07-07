<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="<?= base_url() ?>">
    <title><?= $title ?? ($aparencia['titulo_site'] ?? ($config['nome_loja'] ?? 'SwapShop')) ?></title>

    <!-- Meta tags para SEO -->
    <meta name="description"
        content="<?= isset($aparencia['descricao_site']) ? $aparencia['descricao_site'] : (isset($config['meta_descricao']) ? $config['meta_descricao'] : 'SwapShop - Sua loja virtual completa') ?>">
    <meta name="keywords" content="<?= isset($aparencia['palavras_chave']) ? $aparencia['palavras_chave'] : '' ?>">
    <meta property="og:title"
        content="<?= isset($aparencia['titulo_site']) ? $aparencia['titulo_site'] : (isset($config['og_title']) ? $config['og_title'] : ($title ?? 'SwapShop')) ?>">
    <meta property="og:description"
        content="<?= isset($aparencia['descricao_site']) ? $aparencia['descricao_site'] : (isset($config['og_description']) ? $config['og_description'] : 'SwapShop - Sua loja virtual completa') ?>">
    <meta property="og:image"
        content="/uploads/seo/<?= isset($config['og_image']) ? $config['og_image'] : 'og-image-default.jpg' ?>">

    <!-- Bootstrap CSS -->
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=<?= str_replace(' ', '+', $aparencia['fonte'] ?? 'Roboto') ?>:wght@300;400;500;700&display=swap"
        rel="stylesheet">

    <!-- CSS principais (ordem importante) -->
    <link rel="stylesheet" href="<?= base_url('css/loja.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/dinamico.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/custom.css') ?>">




    <!-- Modo escuro (se ativado) -->
    <?php if (isset($modoCss) && $modoCss): ?>
        <style>
            <?= $modoCss ?>
        </style>
    <?php endif; ?>

    <!-- Estilos customizados baseados na aparência -->
    <style>
        body {
            font-family:
                <?= isset($aparencia['fonte']) ? $aparencia['fonte'] : (isset($config['fonte_principal']) ? $config['fonte_principal'] : 'Roboto, sans-serif') ?>
            ;
        }

        <?php if (isset($aparencia['estilo_cabecalho'])): ?>
            /* Estilos customizados para o cabeçalho */
            .main-header {
                <?= $aparencia['estilo_cabecalho'] ?>
            }

        <?php endif; ?>

        <?php if (isset($aparencia['estilo_rodape'])): ?>
            /* Estilos customizados para o rodapé */
            .main-footer {
                <?= $aparencia['estilo_rodape'] ?>
            }

        <?php endif; ?>

        <?php if (isset($config['mostrar_precos']) && $config['mostrar_precos'] == 0): ?>
            /* Ocultar preços quando a configuração estiver desativada */
            .product-price,
            .price-container {
                display: none !important;
            }

        <?php endif; ?>
    </style>
</head>

<body>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
    <!-- Barra Superior -->
    <div class="top-bar">
        <div class="container">
            <div class="row">
                <div class="col d-flex justify-content-between">
                    <div>
                        <a href="<?= session()->has('cliente') ? site_url('minha-conta') : site_url('login') ?>">
                            <i class="fas fa-user-circle me-1"></i> Minha Conta
                        </a>
                        <a href="<?= session()->has('cliente') ? site_url('meuspedidos') : site_url('login') ?>" class="ms-3">
                            <i class="fas fa-clipboard-list me-1"></i> Meus Pedidos
                        </a>
                    </div>
                    <div>
                        <a href="<?= site_url('localizar') ?>" class="me-3">
                            <i class="fas fa-map-marker-alt me-1"></i>
                        </a>
                        <a href="<?= session()->has('cliente') ? site_url('minha-conta') : site_url('login') ?>"
                            class="me-3">
                            <i class="fas fa-user me-1"></i>
                        </a>
                        <a href="<?= site_url('carrinho') ?>" class="position-relative">
                            <i class="fas fa-shopping-cart me-1"></i>
                            <?php if (isset($carrinho['quantidade']) && $carrinho['quantidade'] > 0): ?>
                                <span
                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?= $carrinho['quantidade'] ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cabeçalho -->
    <?= view('templates/header') ?>

    <!-- Conteúdo principal -->
    <main class="main-content">
        <?= $content ?? '' ?>
    </main>

    <!-- Rodapé -->
    <?= view('templates/footer') ?>

    <!-- Scripts -->

    <script src="<?= base_url('js/loja.js') ?>"></script>
    <script src="<?= base_url('assets/js/main.js') ?>"></script>

    <!-- Scripts dinâmicos baseados nas configurações -->
    <?php if (isset($scripts)): ?>
        <?= $scripts ?>
    <?php endif; ?>

    <!-- Script para o carrinho -->
    <script>
        $(document).ready(function () {
            // Atualizar carrinho via AJAX
            $('.add-to-cart').click(function (e) {
                e.preventDefault();
                var btn = $(this);
                var productId = btn.data('product-id');
                var quantidade = 1;

                // Se estiver na página do produto, pegar a quantidade do input
                var quantityInput = $('#quantity_' + productId);
                if (quantityInput.length) {
                    quantidade = parseInt(quantityInput.val());
                }

                $.ajax({
                    url: '<?= site_url('carrinho/adicionar') ?>/' + productId,
                    method: 'POST',
                    data: { quantidade: quantidade },
                    dataType: 'json',
                    beforeSend: function () {
                        btn.prop('disabled', true);
                        if (btn.find('.fa-shopping-cart').length) {
                            btn.find('.fa-shopping-cart').removeClass('fa-shopping-cart').addClass('fa-spinner fa-spin');
                        }
                    },
                    success: function (response) {
                        if (response.success) {
                            // Atualiza o contador do carrinho
                            $('.cart-count').text(response.total_itens);

                            // Mostra mensagem de sucesso
                            alert('Produto adicionado ao carrinho!');
                        } else {
                            alert(response.message || 'Erro ao adicionar produto ao carrinho');
                        }
                    },
                    error: function () {
                        alert('Erro ao adicionar produto ao carrinho');
                    },
                    complete: function () {
                        btn.prop('disabled', false);
                        if (btn.find('.fa-spinner').length) {
                            btn.find('.fa-spinner').removeClass('fa-spinner fa-spin').addClass('fa-shopping-cart');
                        }
                    }
                });
            });

            // Controles de quantidade
            $('.btn-quantity').click(function () {
                var action = $(this).data('action');
                var input = $(this).closest('.quantity-control').find('input');
                var currentVal = parseInt(input.val());
                var min = parseInt(input.attr('min'));
                var max = parseInt(input.attr('max'));

                if (action === 'decrease' && currentVal > min) {
                    input.val(currentVal - 1);
                } else if (action === 'increase' && currentVal < max) {
                    input.val(currentVal + 1);
                }
            });
        });
    </script>
</body>

</html>