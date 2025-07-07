<!-- Cabeçalho -->
<header class="main-header py-3">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-3 col-6">
                <a href="<?= base_url() ?>" class="logo">
                    <?php if (isset($logo) && !empty($logo)): ?>
                        <img src="<?= base_url('uploads/logos/' . $logo) ?>"
                            alt="<?= isset($config['nome_loja']) ? $config['nome_loja'] : 'SwapShop' ?>" class="img-fluid">
                    <?php else: ?>
                        <h1 class="m-0"><?= isset($config['nome_loja']) ? $config['nome_loja'] : 'SwapShop' ?></h1>
                    <?php endif; ?>
                </a>
            </div>
            <div class="col-md-6 col-6 d-md-block d-none">
                <form action="<?= site_url('produtos/buscar') ?>" method="get" class="search-form">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control" placeholder="O que você está procurando?"
                            aria-label="Buscar produtos" value="<?= isset($_GET['q']) ? esc($_GET['q']) : '' ?>">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                    </div>
                </form>
            </div>
            <div class="col-md-3 d-md-block d-none text-end">
                <div class="contact-info">
                    <?php if (isset($config['telefone']) && !empty($config['telefone'])): ?>
                        <p class="mb-0"><i class="fas fa-phone-alt me-2"></i><?= $config['telefone'] ?></p>
                    <?php endif; ?>
                    <?php if (isset($config['email_contato']) && !empty($config['email_contato'])): ?>
                        <p class="mb-0"><i class="fas fa-envelope me-2"></i><?= $config['email_contato'] ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Menu de Navegação -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain"
            aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= current_url() == base_url() ? 'active' : '' ?>"
                        href="<?= base_url() ?>">Início</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= current_url() == site_url('produtos') ? 'active' : '' ?>"
                        href="<?= site_url('produtos') ?>">Produtos</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownCategorias" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Categorias
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdownCategorias">
                        <?php if (isset($categorias) && is_array($categorias) && !empty($categorias)): ?>
                            <?php foreach ($categorias as $categoria): ?>
                                <li>
                                    <a class="dropdown-item"
                                        href="<?= site_url('categoria/' . (!empty($categoria['slug']) ? $categoria['slug'] : $categoria['id_categoria'])) ?>">
                                        <i class="fas <?= $categoria['icone'] ?? 'fa-tag' ?> me-1"></i>
                                        <?= esc($categoria['nome']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li><a class="dropdown-item">Nenhuma categoria encontrada</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= current_url() == site_url('marcas-parceiras') ? 'active' : '' ?>"
                        href="<?= site_url('marcas-parceiras') ?>">Marcas Parceiras</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= current_url() == site_url('sobre') ? 'active' : '' ?>"
                        href="<?= site_url('sobre') ?>">Sobre</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= current_url() == site_url('contato') ? 'active' : '' ?>"
                        href="<?= site_url('contato') ?>">Contato</a>
                </li>
            </ul>
            <div class="d-flex align-items-center">
                <a href="<?= site_url('carrinho') ?>" class="btn btn-outline-light me-3 position-relative">
                    <i class="fas fa-shopping-cart"></i>
                    <?php if (isset($total_itens_carrinho) && $total_itens_carrinho > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?= $total_itens_carrinho ?>
                        </span>
                    <?php endif; ?>
                </a>
                <?php
                $cliente = session()->get('cliente');
                if (!empty($cliente) && isset($cliente['logged_in']) && $cliente['logged_in']): ?>
                    <!-- Usuário logado -->
                    <div class="dropdown">
                        <button class="btn btn-outline-light dropdown-toggle" type="button" id="userDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user me-1"></i> <?= esc($cliente['nome']) ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="<?= site_url('minha-conta') ?>">
                                <i class="fas fa-user-circle me-2"></i>Minha Conta</a></li>
                            <li><a class="dropdown-item" href="<?= site_url('meus-pedidos') ?>">
                                <i class="fas fa-shopping-bag me-2"></i>Meus Pedidos</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= site_url('logout') ?>">
                                <i class="fas fa-sign-out-alt me-2"></i>Sair</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <!-- Usuário não logado -->
                    <a href="<?= site_url('login') ?>" class="btn btn-outline-light">
                        <i class="fas fa-user me-1"></i> Entrar
                    </a>
                <?php endif; ?>
            </div>
            <div class="d-lg-none d-block mt-3">
                <form action="<?= site_url('produtos/buscar') ?>" method="get" class="search-form">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control" placeholder="O que você está procurando?"
                            aria-label="Buscar produtos" value="<?= isset($_GET['q']) ? esc($_GET['q']) : '' ?>">
                        <button class="btn btn-secondary" type="submit"><i class="fas fa-search"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</nav>