<?php
// View da página de categoria
?>

<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url() ?>">Início</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= esc($categoria['nome']) ?></li>
        </ol>
    </nav>

    <div class="row">
        <!-- Sidebar com informações da categoria -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <?php if (!empty($categoria['imagem'])): ?>
                    <img src="<?= base_url('uploads/categorias/' . $categoria['imagem']) ?>" class="card-img-top" alt="<?= esc($categoria['nome']) ?>">
                <?php endif; ?>
                <div class="card-body">
                    <h1 class="h4 card-title"><?= esc($categoria['nome']) ?></h1>
                    <?php if (!empty($categoria['descricao'])): ?>
                        <p class="card-text"><?= esc($categoria['descricao']) ?></p>
                    <?php endif; ?>
                    <p class="text-muted mb-0">
                        <strong><?= $totalProdutos ?></strong> produtos encontrados
                    </p>
                </div>
            </div>

            <?php if (!empty($categoria['subcategorias'])): ?>
                <div class="card mt-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Subcategorias</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <?php foreach ($categoria['subcategorias'] as $subcategoria): ?>
                                <li class="list-group-item">
                                    <a href="<?= site_url('categoria/' . $subcategoria['slug']) ?>" class="text-decoration-none">
                                        <?php if (!empty($subcategoria['icone'])): ?>
                                            <i class="fas <?= $subcategoria['icone'] ?> me-2"></i>
                                        <?php endif; ?>
                                        <?= esc($subcategoria['nome']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Produtos da categoria -->
        <div class="col-lg-9">
            <!-- Cabeçalho com ordenação -->
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <h2 class="h4 mb-0"><?= esc($categoria['nome']) ?></h2>
                <div class="d-flex">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            Ordenar por
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item <?= $sortBy == 'nome' && $sortOrder == 'asc' ? 'active' : '' ?>" 
                                   href="?sort=nome&order=asc">
                                    Nome (A-Z)
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?= $sortBy == 'nome' && $sortOrder == 'desc' ? 'active' : '' ?>" 
                                   href="?sort=nome&order=desc">
                                    Nome (Z-A)
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?= $sortBy == 'preco' && $sortOrder == 'asc' ? 'active' : '' ?>" 
                                   href="?sort=preco&order=asc">
                                    Preço (menor primeiro)
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?= $sortBy == 'preco' && $sortOrder == 'desc' ? 'active' : '' ?>" 
                                   href="?sort=preco&order=desc">
                                    Preço (maior primeiro)
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?= $sortBy == 'mais_vendidos' ? 'active' : '' ?>" 
                                   href="?sort=mais_vendidos">
                                    Mais vendidos
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?= $sortBy == 'mais_recentes' ? 'active' : '' ?>" 
                                   href="?sort=mais_recentes">
                                    Lançamentos
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="btn-group">
                        <a href="#" class="btn btn-sm btn-outline-secondary d-none d-md-inline" title="Visualização em grade">
                            <i class="fas fa-th"></i>
                        </a>
                        <a href="#" class="btn btn-sm btn-outline-secondary d-none d-md-inline" title="Visualização em lista">
                            <i class="fas fa-list"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <?php if (empty($produtos)): ?>
                <div class="alert alert-info">
                    Nenhum produto encontrado para esta categoria.
                </div>
            <?php else: ?>
                <!-- Lista de produtos -->
                <div class="row g-4">
                    <?php foreach ($produtos as $produto): ?>
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="card h-100 shadow-sm product-card border-0 position-relative">
                                <!-- Badge de promoção -->
                                <?php if (isset($produto['em_promocao']) && $produto['em_promocao'] && $produto['preco_promocional'] < $produto['preco']): ?>
                                    <div class="position-absolute top-0 end-0 p-2">
                                        <span class="badge bg-danger">
                                            <?= '-' . round((1 - $produto['preco_promocional'] / $produto['preco']) * 100) . '%'; ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Imagem do produto -->
                                <a href="<?= site_url('produto/' . ($produto['slug'] ?? $produto['id_produto'])); ?>" class="text-decoration-none">
                                    <img src="<?= base_url('uploads/produtos/' . ($produto['imagem'] ?? 'produto-default.jpg')); ?>" 
                                         class="card-img-top product-img" 
                                         alt="<?= esc($produto['nome']); ?>">
                                </a>
                                
                                <div class="card-body">
                                    <!-- Categoria -->
                                    <p class="card-text text-muted small mb-1">
                                        <?= esc($produto['categoria_nome'] ?? ''); ?>
                                    </p>
                                    
                                    <!-- Nome -->
                                    <h5 class="card-title product-title">
                                        <a href="<?= site_url('produto/' . ($produto['slug'] ?? $produto['id_produto'])); ?>" class="text-decoration-none text-dark">
                                            <?= esc($produto['nome']); ?>
                                        </a>
                                    </h5>
                                    
                                    <!-- Preços -->
                                    <?php if(!isset($config['mostrar_precos']) || $config['mostrar_precos'] == 1): ?>
                                    <div class="mb-2 product-price">
                                        <?php if (isset($produto['em_promocao']) && $produto['em_promocao'] && $produto['preco_promocional'] < $produto['preco']): ?>
                                            <span class="text-decoration-line-through text-muted me-1">
                                                <?= 'R$ ' . number_format($produto['preco'], 2, ',', '.'); ?>
                                            </span>
                                            <span class="fw-bold text-danger">
                                                <?= 'R$ ' . number_format($produto['preco_promocional'], 2, ',', '.'); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="fw-bold">
                                                <?= 'R$ ' . number_format($produto['preco'], 2, ',', '.'); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <!-- Botões -->
                                    <?php if (isset($config['mostrar_precos']) && $config['mostrar_precos']): ?>
                                        <div class="mt-auto d-flex">
                                            <a href="<?= site_url('produto/' . ($produto['slug'] ?? $produto['id_produto'])) ?>" class="btn btn-sm btn-outline-secondary me-1 flex-grow-1">Ver detalhes</a>
                                            
                                            <?php if (isset($produto['quantidade']) && $produto['quantidade'] > 0): ?>
                                                <a href="<?= site_url('carrinho/adicionar/' . $produto['id_produto']) ?>" 
                                                    class="btn btn-sm btn-danger add-to-cart flex-grow-1"
                                                    data-product-id="<?= $produto['id_produto'] ?>">
                                                    <i class="fas fa-shopping-cart"></i>
                                                </a>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-sm btn-secondary flex-grow-1" disabled>
                                                    Indisponível
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <a href="<?= site_url('produto/' . ($produto['slug'] ?? $produto['id_produto'])) ?>" class="btn btn-sm btn-primary mt-auto">Ver detalhes</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Paginação -->
                <div class="d-flex justify-content-center mt-5">
                    <?= $pager->links() ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div> 