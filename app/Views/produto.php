<?php
// View da página de produto
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url() ?>">Início</a></li>
            <?php if (isset($categoria) && !empty($categoria)): ?>
                <li class="breadcrumb-item">
                    <a href="<?= site_url('categoria/' . $categoria['slug']) ?>"><?= esc($categoria['nome']) ?></a>
                </li>
            <?php endif; ?>
            <li class="breadcrumb-item active" aria-current="page"><?= esc($produto['nome']) ?></li>
        </ol>
    </nav>

    <!-- Produto -->
    <div class="row">
        <!-- Galeria de imagens -->
        <div class="col-md-6 mb-4">
            <div class="product-gallery">
                <!-- Imagem principal -->
                <div class="main-image mb-3">
                    <img src="<?= base_url('uploads/produtos/' . $produto['imagem']) ?>" class="img-fluid rounded" alt="<?= esc($produto['nome']) ?>" id="mainProductImage">
                </div>
                
                <!-- Miniaturas de imagens adicionais -->
                <?php if (!empty($produto['imagens_array'])): ?>
                    <div class="thumbnails row g-2">
                        <div class="col-3">
                            <img src="<?= base_url('uploads/produtos/' . $produto['imagem']) ?>" class="img-thumbnail thumb-active" alt="Principal" 
                                onclick="changeMainImage(this)" data-image="<?= $produto['imagem'] ?>">
                        </div>
                        <?php foreach ($produto['imagens_array'] as $imagem): ?>
                            <div class="col-3">
                                <img src="<?= base_url('uploads/produtos/' . $imagem) ?>" class="img-thumbnail" alt="<?= esc($produto['nome']) ?>" 
                                    onclick="changeMainImage(this)" data-image="<?= $imagem ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Informações do produto -->
        <div class="col-md-6">
            <h1 class="h3 mb-2"><?= esc($produto['nome']) ?></h1>
            
            <?php if (isset($categoria) && !empty($categoria)): ?>
                <div class="mb-3">
                    <span class="badge bg-secondary">
                        <i class="fas <?= $categoria['icone'] ?? 'fa-tag' ?> me-1"></i>
                        <?= esc($categoria['nome']) ?>
                    </span>
                </div>
            <?php endif; ?>
            
            <!-- Preços -->
            <?php if (!isset($config['mostrar_precos']) || $config['mostrar_precos'] == 1): ?>
            <div class="product-price-large mb-3">
                <?php if ($produto['preco_promocional'] > 0): ?>
                    <div class="old-price fs-5">De: <span class="text-decoration-line-through"><?= $produto['preco_antigo_formatado'] ?></span></div>
                    <div class="new-price fs-3 fw-bold text-success">Por: <?= $produto['preco_formatado'] ?></div>
                    <div class="discount badge bg-danger mb-2">-<?= $produto['desconto'] ?>% de desconto</div>
                <?php else: ?>
                    <div class="current-price fs-3 fw-bold text-success"><?= $produto['preco_formatado'] ?></div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <!-- Estoque -->
            <div class="mb-3">
                <span class="badge <?= $produto['quantidade'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                    <?= $produto['quantidade'] > 0 ? 'Em estoque' : 'Fora de estoque' ?>
                </span>
                <?php if ($produto['quantidade'] > 0): ?>
                    <span class="text-muted ms-2"><?= $produto['quantidade'] ?> unidades disponíveis</span>
                <?php endif; ?>
            </div>
            
            <!-- Descrição curta -->
            <?php if (!empty($produto['descricao'])): ?>
                <div class="product-short-description mb-4">
                    <p><?= esc($produto['descricao']) ?></p>
                </div>
            <?php endif; ?>
            
            <!-- Formulário de compra -->
            <?php if (!isset($config['mostrar_precos']) || $config['mostrar_precos'] == 1): ?>
            <form action="<?= site_url('carrinho/adicionar/' . $produto['id_produto']) ?>" method="post" class="mb-4">
                <div class="row g-3 align-items-center">
                    <?php if ($produto['quantidade'] > 0): ?>
                        <div class="col-auto">
                            <div class="input-group quantity-control">
                                <button type="button" class="btn btn-secondary btn-quantity" data-action="decrease">-</button>
                                <input type="number" name="quantidade" id="quantity_<?= $produto['id_produto'] ?>" class="form-control text-center" 
                                    value="1" min="1" max="<?= $produto['quantidade'] ?>" readonly>
                                <button type="button" class="btn btn-secondary btn-quantity" data-action="increase">+</button>
                            </div>
                        </div>
                        <div class="col">
                            <button type="submit" class="btn btn-primary btn-lg w-100 add-to-cart" data-product-id="<?= $produto['id_produto'] ?>">
                                <i class="fas fa-shopping-cart me-2"></i> Adicionar ao Carrinho
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="col">
                            <button type="button" class="btn btn-secondary btn-lg w-100" disabled>
                                <i class="fas fa-ban me-2"></i> Produto Indisponível
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </form>
            <?php endif; ?>
            
            <!-- Informações adicionais -->
            <div class="product-meta">
                <?php if (!empty($produto['sku'])): ?>
                    <p class="mb-1"><strong>SKU:</strong> <?= esc($produto['sku']) ?></p>
                <?php endif; ?>
                
                <?php if (!empty($produto['peso_bruto']) || !empty($produto['comprimento']) || !empty($produto['largura']) || !empty($produto['altura'])): ?>
                    <p class="mb-1"><strong>Dimensões:</strong> 
                        <?= (!empty($produto['comprimento']) ? $produto['comprimento'] . 'cm' : '') ?>
                        <?= (!empty($produto['largura']) ? ' x ' . $produto['largura'] . 'cm' : '') ?>
                        <?= (!empty($produto['altura']) ? ' x ' . $produto['altura'] . 'cm' : '') ?>
                        <?= (!empty($produto['peso_bruto']) ? ' - ' . $produto['peso_bruto'] . 'kg' : '') ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Abas de informações -->
    <div class="product-tabs mt-5">
        <ul class="nav nav-tabs" id="productTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab" aria-controls="description" aria-selected="true">
                    Descrição Completa
                </button>
            </li>
            <?php if (!empty($produto['especificacoes_array'])): ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="specifications-tab" data-bs-toggle="tab" data-bs-target="#specifications" type="button" role="tab" aria-controls="specifications" aria-selected="false">
                        Especificações
                    </button>
                </li>
            <?php endif; ?>
            <?php if (!empty($produto['informacoes_adicionais'])): ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="additional-tab" data-bs-toggle="tab" data-bs-target="#additional" type="button" role="tab" aria-controls="additional" aria-selected="false">
                        Informações Adicionais
                    </button>
                </li>
            <?php endif; ?>
        </ul>
        <div class="tab-content p-4 border border-top-0 rounded-bottom" id="productTabsContent">
            <div class="tab-pane fade show active" id="description" role="tabpanel" aria-labelledby="description-tab">
                <?php if (!empty($produto['descricao_completa'])): ?>
                    <?= $produto['descricao_completa'] ?>
                <?php else: ?>
                    <p class="text-muted">Nenhuma descrição completa disponível para este produto.</p>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($produto['especificacoes_array'])): ?>
                <div class="tab-pane fade" id="specifications" role="tabpanel" aria-labelledby="specifications-tab">
                    <table class="table table-bordered">
                        <tbody>
                            <?php foreach ($produto['especificacoes_array'] as $especificacao): ?>
                                <tr>
                                    <th style="width: 30%"><?= esc($especificacao['nome'] ?? '') ?></th>
                                    <td><?= esc($especificacao['valor'] ?? '') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($produto['informacoes_adicionais'])): ?>
                <div class="tab-pane fade" id="additional" role="tabpanel" aria-labelledby="additional-tab">
                    <?= $produto['informacoes_adicionais'] ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Produtos relacionados -->
    <?php if (!empty($produtosRelacionados)): ?>
        <section class="related-products mt-5">
            <h2 class="mb-4">Produtos Relacionados</h2>
            <div class="row g-4">
                <?php foreach ($produtosRelacionados as $produtoRel): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="card h-100 shadow-sm product-card border-0 position-relative">
                            <!-- Badge de promoção -->
                            <?php if (isset($produtoRel['em_promocao']) && $produtoRel['em_promocao'] && $produtoRel['preco_promocional'] < $produtoRel['preco']): ?>
                                <div class="position-absolute top-0 end-0 p-2">
                                    <span class="badge bg-danger">
                                        <?= '-' . round((1 - $produtoRel['preco_promocional'] / $produtoRel['preco']) * 100) . '%'; ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Imagem do produto -->
                            <a href="<?= site_url('produto/' . ($produtoRel['slug'] ?? $produtoRel['id_produto'])); ?>" class="text-decoration-none">
                                <img src="<?= base_url('uploads/produtos/' . ($produtoRel['imagem'] ?? 'produto-default.jpg')); ?>" 
                                     class="card-img-top product-img" 
                                     alt="<?= esc($produtoRel['nome']); ?>">
                            </a>
                            
                            <div class="card-body">
                                <!-- Categoria -->
                                <p class="card-text text-muted small mb-1">
                                    <?= esc($produtoRel['categoria_nome'] ?? ''); ?>
                                </p>
                                
                                <!-- Nome -->
                                <h5 class="card-title product-title">
                                    <a href="<?= site_url('produto/' . ($produtoRel['slug'] ?? $produtoRel['id_produto'])); ?>" class="text-decoration-none text-dark">
                                        <?= esc($produtoRel['nome']); ?>
                                    </a>
                                </h5>
                                
                                <!-- Preços -->
                                <?php if(!isset($config['mostrar_precos']) || $config['mostrar_precos'] == 1): ?>
                                <div class="mb-2 product-price">
                                    <?php if (isset($produtoRel['em_promocao']) && $produtoRel['em_promocao'] && $produtoRel['preco_promocional'] < $produtoRel['preco']): ?>
                                        <span class="text-decoration-line-through text-muted me-1">
                                            <?= 'R$ ' . number_format($produtoRel['preco'], 2, ',', '.'); ?>
                                        </span>
                                        <span class="fw-bold text-danger">
                                            <?= 'R$ ' . number_format($produtoRel['preco_promocional'], 2, ',', '.'); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="fw-bold">
                                            <?= 'R$ ' . number_format($produtoRel['preco'], 2, ',', '.'); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                                
                                <!-- Botões -->
                                <?php if (isset($produtoRel['quantidade']) && $produtoRel['quantidade'] > 0): ?>
                                    <div class="row g-2 mt-auto">
                                        <div class="col-6">
                                            <a href="<?= site_url('produto/' . ($produtoRel['slug'] ?? $produtoRel['id_produto'])); ?>" class="btn btn-outline-primary w-100">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                        <?php if(!isset($config['mostrar_precos']) || $config['mostrar_precos'] == 1): ?>
                                        <div class="col-6">
                                            <a href="<?= site_url('carrinho/adicionar/' . $produtoRel['id_produto']); ?>" class="btn btn-danger w-100 add-to-cart" data-product-id="<?= $produtoRel['id_produto']; ?>">
                                                <i class="fas fa-shopping-cart"></i>
                                            </a>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <button class="btn btn-secondary w-100" disabled>
                                        <i class="fas fa-ban"></i> Indisponível
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</div>

<script>
    function changeMainImage(element) {
        // Remover a classe ativa de todas as miniaturas
        document.querySelectorAll('.thumbnails .img-thumbnail').forEach(thumb => {
            thumb.classList.remove('thumb-active');
        });
        
        // Adicionar a classe ativa na miniatura clicada
        element.classList.add('thumb-active');
        
        // Atualizar a imagem principal
        const mainImage = document.getElementById('mainProductImage');
        const newImagePath = '<?= base_url('uploads/produtos/') ?>' + element.getAttribute('data-image');
        mainImage.src = newImagePath;
    }
</script> 