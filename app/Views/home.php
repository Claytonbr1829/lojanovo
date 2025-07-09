<!-- Seção de Banner Principal (Carrossel) -->
<div id="mainCarousel" class="carousel slide hero-banner mb-5" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <?php
        // Verificar se há banners disponíveis
        $banners = [];
        if (isset($aparencia['banners']) && !empty($aparencia['banners'])) {
            $banners = is_array($aparencia['banners']) ? $aparencia['banners'] : json_decode($aparencia['banners'], true);
        }

        // Se não houver banners, usar o banner_principal como único banner
        if (empty($banners) && isset($aparencia['banner_principal']) && !empty($aparencia['banner_principal'])) {
            $banners = [['imagem' => $aparencia['banner_principal'], 'titulo' => '', 'subtitulo' => '', 'link' => site_url('produtos/destaque')]];
        }

        // Se ainda estiver vazio, usar banners padrão
        if (empty($banners)) {
            $banners = [
                ['imagem' => 'banner1.jpg', 'titulo' => 'Produtos em Promoção', 'subtitulo' => 'Confira nossas ofertas por tempo limitado com descontos especiais.', 'link' => site_url('produtos/destaque')],
                //['imagem' => 'banner2.jpg', 'titulo' => 'Os Mais Vendidos', 'subtitulo' => 'Descubra os produtos favoritos entre nossos clientes.', 'link' => site_url('produtos/mais-vendidos')],
                ['imagem' => 'banner3.jpg', 'titulo' => 'Novidades', 'subtitulo' => 'Fique por dentro dos lançamentos e novidades da nossa loja.', 'link' => site_url('produtos/novidades')]
            ];
        }

        // Criar os indicadores
        foreach ($banners as $index => $banner):
            ?>
            <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="<?= $index ?>"
                class="<?= $index === 0 ? 'active' : '' ?>" aria-current="<?= $index === 0 ? 'true' : 'false' ?>"
                aria-label="Slide <?= $index + 1 ?>"></button>
        <?php endforeach; ?>
    </div>
    <div class="carousel-inner">
        <?php foreach ($banners as $index => $banner): ?>
            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                <img src="<?= base_url('uploads/banners/' . $banner['imagem']); ?>" class="d-block w-100"
                    alt="<?= $banner['titulo'] ?? 'Banner promocional' ?>">
                <!-- <div class="carousel-caption"> -->
                <div class="container">
                    <div class="row">
                        <div class="col-md-7">
                            <h1 class="display-4 fw-bold"><?= $banner['titulo'] ?? '' ?></h1>
                            <p class="lead"><?= $banner['subtitulo'] ?? '' ?></p>
                            <?php if (isset($banner['link']) && !empty($banner['link'])): ?>
                                <a href="<?= $banner['link'] ?>"
                                    class="btn btn-primary btn-lg"><?= $banner['texto_botao'] ?? 'Ver mais' ?></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <!-- //</div> -->
            </div>
        <?php endforeach; ?>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
        <span class="fas fa-chevron-left fa-2x text-white"></span>
        <span class="visually-hidden">Anterior</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
        <span class="fas fa-chevron-right fa-2x text-white"></span>
        <span class="visually-hidden">Próximo</span>
    </button>

</div>

<div class="container">
    <!-- Vantagens da Loja -->

    <!-- Categorias em Destaque
    <section class="categories-section mb-5">
        <div class="section-header d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title">Categorias</h2>
            <a href="<?= site_url('produtos'); ?>" class="view-all-link text-decoration-none">Ver todas <i class="fas fa-chevron-right ms-1"></i></a>
        </div>
        <div class="row g-4">
            <?php if (!empty($categorias)): ?>
                <?php foreach ($categorias as $categoria): ?>
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="<?= site_url('categoria/' . ($categoria['slug'] ?? $categoria['id_categoria'])); ?>" class="text-decoration-none">
                            <div class="category-card position-relative rounded shadow-sm overflow-hidden">
                                <img src="<?= base_url('uploads/categorias/' . ($categoria['imagem'] ?? 'categoria-default.jpg')); ?>" 
                                     alt="<?= esc($categoria['nome']); ?>" class="img-fluid w-100">
                                <div class="category-overlay position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-end p-3">
                                    <div class="category-name h6 text-white mb-0"><?= esc($categoria['nome']); ?></div>
                                    <?php if (isset($categoria['total_produtos'])): ?>
                                        <div class="product-count small text-white-50"><?= $categoria['total_produtos']; ?> produtos</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info">Nenhuma categoria encontrada.</div>
                </div>
            <?php endif; ?>
        </div>
    </section> -->

    <!-- Banner Promocional -->
    <!-- <section class="promo-banner mb-5">
        <div class="row">
            <div class="col-12">
                <div class="card bg-dark text-white border-0 rounded-3 overflow-hidden">
                    <img src="<?= base_url('uploads/banners/promocao-especial.jpg'); ?>" class="card-img" alt="Promoção Especial">
                    <div class="card-img-overlay d-flex flex-column justify-content-center">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-6">
                                    <h3 class="card-title">Promoção Especial</h3>
                                    <p class="card-text mb-4">Desconto de 15% em toda a loja usando o cupom BEMVINDO</p>
                                    <a href="<?= site_url('produtos'); ?>" class="btn btn-primary">Aproveitar Agora</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> -->

    <!-- Produtos em Destaque -->
    <?php if (!empty($produtosDestaque)): ?>
        <section class="featured-products mb-5">
            <div class="section-header d-flex justify-content-between align-items-center mb-4">
                <h2 class="section-title">Produtos em Destaque</h2>
                <a href="<?= site_url('produtos/destaque') ?>" class="view-all-link text-decoration-none">Ver todos <i
                        class="fas fa-chevron-right ms-1"></i></a>
            </div>
            <div class="row g-4">
                <?php foreach ($produtosDestaque as $produto): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="product-card card h-100 align-items-center">
                            <a href="<?= site_url('produto/' . ($produto['slug'] ?? $produto['id_produto'])) ?>"
                                class="text-decoration-none">
                                <img src="<?= base_url('uploads/produtos/' . ($produto['imagem'] ?? 'produto-default.jpg')) ?>"
                                    class="card-img-top" alt="<?= esc($produto['nome']) ?>">
                            </a>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">
                                    <a href="<?= site_url('produto/' . ($produto['slug'] ?? $produto['id_produto'])) ?>"
                                        class="text-decoration-none text-dark">
                                        <?= esc($produto['nome']) ?>
                                    </a>
                                </h5>
                                <?php if (!isset($config['mostrar_precos']) || $config['mostrar_precos'] == 1): ?>
                                    <div class="product-price mt-auto">
                                        <?php if (!empty($produto['preco_promocional'])): ?>
                                            <span class="old-price text-muted text-decoration-line-through">
                                                <?= $produto['preco_antigo_formatado'] ?>
                                            </span>
                                            <span class="new-price text-primary fw-bold">
                                                <?= $produto['preco_formatado'] ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="price text-primary fw-bold">
                                                <?= $produto['preco_formatado'] ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                <div class="d-flex gap-2 mt-3">
                                    <a href="<?= site_url('produto/' . ($produto['slug'] ?? $produto['id_produto'])) ?>"
                                        class="btn btn-outline-primary flex-grow-0">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?= site_url('carrinho/adicionar/' . $produto['id_produto']) ?>"
                                        class="btn btn-primary flex-grow-1">
                                        <i class="fas fa-shopping-cart me-2"></i>Adicionar ao Carrinho
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <hr>

    <!-- Banners Duplos
    <section class="dual-banners mb-5">
        <div class="row g-4">
            <div class="col-12 col-md-6">
                <div class="card bg-light text-dark border-0 rounded-3 overflow-hidden h-100">
                    <img src="<?= base_url('uploads/banners/banner-small-1.jpg'); ?>" class="card-img" alt="Coleção Nova">
                    <div class="card-img-overlay d-flex flex-column justify-content-center">
                        <h4 class="card-title">Coleção Nova</h4>
                        <p class="card-text mb-3">As últimas tendências para você</p>
                        <a href="<?= site_url('produtos/novidades'); ?>" class="btn btn-outline-dark w-auto align-self-start">Ver coleção</a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card bg-light text-dark border-0 rounded-3 overflow-hidden h-100">
                    <img src="<?= base_url('uploads/banners/banner-small-2.jpg'); ?>" class="card-img" alt="Ofertas Especiais">
                    <div class="card-img-overlay d-flex flex-column justify-content-center">
                        <h4 class="card-title">Ofertas Especiais</h4>
                        <p class="card-text mb-3">Economize até 50% em produtos selecionados</p>
                        <a href="<?= site_url('produtos/promocao'); ?>" class="btn btn-outline-dark w-auto align-self-start">Ver ofertas</a>
                    </div>
                </div>
            </div>
        </div>
    </section> -->

    <!-- Produtos Mais Vendidos -->
    <?php if (!isset($config['mostrar_mais_vendidos']) || $config['mostrar_mais_vendidos'] == 1): ?>
        <section class="best-sellers mb-5">
            <div class="section-header d-flex justify-content-between align-items-center mb-4">
                <h2 class="section-title">Mais Vendidos</h2>
                <a href="<?= site_url('produtos/mais-vendidos') ?>" class="view-all-link text-decoration-none">Ver todos <i
                        class="fas fa-chevron-right ms-1"></i></a>
            </div>
            <div class="row g-4">
                <?php foreach ($produtosMaisVendidos as $produto): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="product-card card h-100">
                            <a href="<?= site_url('produto/' . ($produto['slug'] ?? $produto['id_produto'])) ?>"
                                class="text-decoration-none">
                                <img src="<?= base_url('uploads/produtos/' . ($produto['imagem'] ?? 'produto-default.jpg')) ?>"
                                    class="card-img-top" alt="<?= esc($produto['nome']) ?>">
                            </a>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">
                                    <a href="<?= site_url('produto/' . ($produto['slug'] ?? $produto['id_produto'])) ?>"
                                        class="text-decoration-none text-dark">
                                        <?= esc($produto['nome']) ?>
                                    </a>
                                </h5>
                                <?php if (!isset($config['mostrar_precos']) || $config['mostrar_precos'] == 1): ?>
                                    <div class="product-price mt-auto">
                                        <?php if (!empty($produto['preco_promocional'])): ?>
                                            <span class="old-price text-muted text-decoration-line-through">
                                                <?= $produto['preco_antigo_formatado'] ?>
                                            </span>
                                            <span class="new-price text-primary fw-bold">
                                                <?= $produto['preco_formatado'] ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="price text-primary fw-bold">
                                                <?= $produto['preco_formatado'] ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                <a href="<?= site_url('carrinho/adicionar/' . $produto['id_produto']) ?>"
                                    class="btn btn-primary mt-3">
                                    <i class="fas fa-shopping-cart me-2"></i>Adicionar ao Carrinho
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <!-- Produtos Novos -->
    <?php if (!empty($produtosNovos)): ?>
        <section class="new-products mb-5">
            <div class="section-header d-flex justify-content-between align-items-center mb-4">
                <h2 class="section-title">Novidades</h2>
                <a href="<?= site_url('produtos/novidades') ?>" class="view-all-link text-decoration-none">Ver todos <i
                        class="fas fa-chevron-right ms-1"></i></a>
            </div>
            <div class="row g-4 h-100">
                <?php foreach ($produtosNovos as $produto): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="product-card card h-100 align-items-center">
                            <a href="<?= site_url('produto/' . ($produto['slug'] ?? $produto['id_produto'])) ?>"
                                class="text-decoration-none">
                                <img src="<?= base_url('uploads/produtos/' . ($produto['imagem'] ?? 'produto-default.jpg')) ?>"
                                    class="card-img-top w-auto" alt="<?= esc($produto['nome']) ?>">
                            </a>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">
                                    <a href="<?= site_url('produto/' . ($produto['slug'] ?? $produto['id_produto'])) ?>"
                                        class="text-decoration-none text-dark">
                                        <?= esc($produto['nome']) ?>
                                    </a>
                                </h5>
                                <?php if (!isset($config['mostrar_precos']) || $config['mostrar_precos'] == 1): ?>
                                    <div class="product-price mt-auto">
                                        <?php if (!empty($produto['preco_promocional'])): ?>
                                            <span class="old-price text-muted text-decoration-line-through">
                                                <?= $produto['preco_antigo_formatado'] ?>
                                            </span>
                                            <span class="new-price text-primary fw-bold">
                                                <?= $produto['preco_formatado'] ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="price text-primary fw-bold">
                                                <?= $produto['preco_formatado'] ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                <a href="<?= site_url('carrinho/adicionar/' . $produto['id_produto']) ?>"
                                    class="btn btn-primary mt-3">
                                    <i class="fas fa-shopping-cart me-2"></i>Adicionar ao Carrinho
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <!-- Marcas Parceiras -->
    <?php if (!isset($config['mostrar_marcas_parceiras']) || $config['mostrar_marcas_parceiras'] == 1): ?>
        <section class="partner-brands mb-5">
            <div class="section-header d-flex justify-content-between align-items-center mb-4">
                <h2 class="section-title">Marcas Parceiras</h2>
                <a href="<?= site_url('marcas-parceiras') ?>" class="view-all-link text-decoration-none">Ver todas <i
                        class="fas fa-chevron-right ms-1"></i></a>
            </div>
            <div class="row g-4">
                <?php foreach ($marcasParceiras as $marca): ?>
                    <div class="col-6 col-md-3 col-lg-2">
                    <a href="<?= esc($marca['link']) ?>" target="_blank" class="text-decoration-none"
                            class="text-decoration-none">
                            <div class="brand-card card h-100">
                                <img src="<?= base_url('uploads/marcas/' . ($marca['logo'] ?? 'marca-default.jpg')) ?>"
                                    class="card-img-top" alt="<?= esc($marca['nome']) ?>">
                                <div class="card-body text-center">
                                    <h5 class="card-title"><?= esc($marca['nome']) ?></h5>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <!-- Depoimentos -->
    <?php if (!isset($config['mostrar_depoimentos']) || $config['mostrar_depoimentos'] == 1): ?>
        <section class="testimonials mb-5">
            <div class="section-header text-center mb-4">
                <h2 class="section-title">O que nossos clientes dizem</h2>
            </div>
            <div class="row g-4">
                <?php foreach ($depoimentos as $depoimento): ?>
                    <div class="col-md-4">
                        <div class="testimonial-card card h-100">
                            <div class="card-body">
                                <div class="testimonial-rating mb-3">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i
                                            class="fas fa-star <?= $i <= ($depoimento['avaliacao'] ?? 0) ? 'text-warning' : 'text-muted' ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <p class="testimonial-text"><?= esc($depoimento['comentario'] ?? 'Sem comentário') ?></p>
                                <div class="testimonial-author">
                                    <strong><?= esc($depoimento['nome'] ?? 'Anônimo') ?></strong>
                                    <small class="text-muted d-block">
                                        <?= !empty($depoimento['data']) ? date('d/m/Y', strtotime($depoimento['data'])) : 'Data não informada' ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <section class="benefits-section py-4 mb-5">
        <div class="row g-4 text-center">
            <div class="col-6 col-md-3">
                <div class="benefit-item p-3 h-100 rounded shadow-sm">
                    <i class="fas fa-truck fa-2x mb-3 text-primary"></i>
                    <h5>Entrega Rápida</h5>
                    <p class="mb-0 small">Entregamos em todo o Brasil</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="benefit-item p-3 h-100 rounded shadow-sm">
                    <i class="fas fa-credit-card fa-2x mb-3 text-primary"></i>
                    <h5>Pagamento Seguro</h5>
                    <p class="mb-0 small">Diversas formas de pagamento</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="benefit-item p-3 h-100 rounded shadow-sm">
                    <i class="fas fa-sync-alt fa-2x mb-3 text-primary"></i>
                    <h5>Troca Garantida</h5>
                    <p class="mb-0 small">7 dias para troca ou devolução</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="benefit-item p-3 h-100 rounded shadow-sm">
                    <i class="fas fa-headset fa-2x mb-3 text-primary"></i>
                    <h5>Suporte ao Cliente</h5>
                    <p class="mb-0 small">Atendimento de segunda a sexta</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter -->
    <?php if (!isset($config['mostrar_assine_Newletter']) || $config['mostrar_assine_Newletter'] == 1): ?>
        <section class="newsletter-section mb-5 py-5 bg-primary text-white rounded-3">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 text-center">
                        <h3 class="mb-3">Assine nossa Newsletter</h3>
                        <p class="mb-4">Fique por dentro das novidades, promoções exclusivas e lançamentos.</p>
                        <form class="newsletter-form">
                            <div class="input-group">
                                <input type="email" class="form-control form-control-lg" placeholder="Seu email"
                                    aria-label="Email">
                                <button class="btn btn-light" type="submit">Assinar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>
</div>

<!-- CSS adicional para o tema Slin -->
<style>
    /* Hero banner */
    .hero-banner {
        margin-top: 0;
    }

    .hero-banner .carousel-item {
        height: 500px;
    }

    .hero-banner .carousel-item img {
        object-fit: cover;
        height: 100%;
    }

    .hero-banner .carousel-caption {
        background: rgba(0, 0, 0, 0.3);
        left: 0;
        right: 0;
        bottom: 0;
        top: 0;
        text-align: left;
        padding: 0;
    }

    .hero-banner .carousel-caption .container {
        height: 100%;
        display: flex;
        align-items: center;
    }

    /* Categorias */
    .category-card {
        transition: all 0.3s ease;
        height: 180px;
    }

    .category-card img {
        height: 100%;
        object-fit: cover;
    }

    .category-overlay {
        background: linear-gradient(to top, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0) 70%);
    }

    .category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }

    /* Produtos */
    .product-image-container {
        position: relative;
        overflow: hidden;
    }

    .product-img {
        height: 200px;
        object-fit: contain;
        transition: transform 0.5s ease;
    }

    .product-card:hover .product-img {
        transform: scale(1.05);
    }

    .product-actions {
        top: 10px;
        right: 10px;
        display: flex;
        flex-direction: column;
        gap: 5px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .product-card:hover .product-actions {
        opacity: 1;
    }

    .product-title {
        font-size: 1rem;
        height: 2.5rem;
        overflow: hidden;
    }

    /* Seções */
    .section-title {
        font-size: 1.75rem;
        font-weight: 600;
        position: relative;
        padding-bottom: 0.5rem;
    }

    .section-title:after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 50px;
        height: 3px;
        background-color: var(--bs-primary);
    }

    .section-header .section-title:after {
        display: none;
    }

    /* Benefícios */
    .benefit-item {
        transition: all 0.3s ease;
        border-bottom: 3px solid transparent;
    }

    .benefit-item:hover {
        border-color: var(--bs-primary);
        transform: translateY(-5px);
    }

    /* Depoimentos */
    .testimonials-section {
        background-color: #f8f9fa;
    }

    .testimonials-section .section-title {
        display: inline-block;
        margin: 0 auto;
    }

    .testimonials-section .section-title:after {
        left: 50%;
        transform: translateX(-50%);
    }

    /* Marcas */
    .brand-item {
        opacity: 0.7;
        transition: all 0.3s ease;
    }

    .brand-item:hover {
        opacity: 1;
    }

    /* Newsletter */
    .newsletter-section {
        background-color: var(--bs-primary);
    }

    .newsletter-form .input-group {
        max-width: 500px;
        margin: 0 auto;
    }

    /* Estilos responsivos */
    @media (max-width: 767.98px) {
        .hero-banner .carousel-item {
            height: 300px;
        }

        .section-title {
            font-size: 1.5rem;
        }

        .category-card {
            height: 120px;
        }
    }

    /* Estilos específicos para a página inicial */
    .btn-product {
        transition: all 0.3s ease;
    }

    .btn-product:hover {
        transform: scale(1.05);
    }

    .product-card .btn {
        opacity: 1 !important;
    }
</style>