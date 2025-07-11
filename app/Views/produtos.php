
<!-- Conteúdo da página de produtos -->
<div class="container py-4" id="produtos-container">
    <div class="row">
        <!-- Filtros -->
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Filtros</h5>
                </div>
                <div class="card-body">
                    <form action="<?= site_url('produtos'); ?>" method="get" id="filtro-form">
                        <!-- Busca -->
                        <div class="mb-3">
                            <label for="q" class="form-label">Buscar:</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="q" name="q" 
                                       value="<?= isset($filtros['busca']) ? esc($filtros['busca']) : ''; ?>"
                                       placeholder="Nome do produto">
                                <button class="btn btn-outline-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Categoria -->
                        <div class="mb-3">
                            <label for="categoria" class="form-label">Categoria:</label>
                            <select class="form-select" id="categoria" name="categoria">
                                <option value="">Todas as categorias</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= $cat['id_categoria']; ?>" 
                                            <?= (isset($filtros['categoria']) && $filtros['categoria'] == $cat['id_categoria']) ? 'selected' : ''; ?>>
                                        <?= esc($cat['nome']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Faixa de Preço -->
                        <div class="mb-3">
                            <label class="form-label">Faixa de preço:</label>
                            <div class="d-flex align-items-center">
                                <input type="number" class="form-control me-2" id="preco_min" name="preco_min" 
                                       value="<?= isset($filtros['preco_min']) ? esc($filtros['preco_min']) : ''; ?>"
                                       placeholder="Mín" min="0" step="0.01">
                                <span class="mx-1">a</span>
                                <input type="number" class="form-control ms-2" id="preco_max" name="preco_max" 
                                       value="<?= isset($filtros['preco_max']) ? esc($filtros['preco_max']) : ''; ?>"
                                       placeholder="Máx" min="0" step="0.01">
                            </div>
                        </div>
                        
                        <!-- Ordenação (campos ocultos para manter a ordenação atual) -->
                        <input type="hidden" name="sort" value="<?= $sortBy; ?>">
                        <input type="hidden" name="order" value="<?= $sortOrder; ?>">
                        
                        <!-- Botões -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Filtrar</button>
                            <a href="<?= site_url('produtos'); ?>" class="btn btn-outline-secondary">Limpar filtros</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Listagem de Produtos -->
        <div class="col-md-9">
            <!-- Cabeçalho com título e ordenação -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h4">
                    <?= $title; ?> 
                    <small class="text-muted fs-6">(<?= $totalProdutos; ?> produtos)</small>
                </h2>
                
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownOrdenacao" 
                            data-bs-toggle="dropdown" aria-expanded="false">
                        Ordenar por
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownOrdenacao">
                        <li>
                            <a class="dropdown-item <?= ($sortBy == 'nome' && $sortOrder == 'asc') ? 'active' : ''; ?>" 
                               href="<?= current_url(); ?>?<?= http_build_query(array_merge($_GET, ['sort' => 'nome', 'order' => 'asc'])); ?>">
                                Nome (A-Z)
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item <?= ($sortBy == 'nome' && $sortOrder == 'desc') ? 'active' : ''; ?>" 
                               href="<?= current_url(); ?>?<?= http_build_query(array_merge($_GET, ['sort' => 'nome', 'order' => 'desc'])); ?>">
                                Nome (Z-A)
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item <?= ($sortBy == 'preco' && $sortOrder == 'asc') ? 'active' : ''; ?>" 
                               href="<?= current_url(); ?>?<?= http_build_query(array_merge($_GET, ['sort' => 'preco', 'order' => 'asc'])); ?>">
                                Preço (menor para maior)
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item <?= ($sortBy == 'preco' && $sortOrder == 'desc') ? 'active' : ''; ?>" 
                               href="<?= current_url(); ?>?<?= http_build_query(array_merge($_GET, ['sort' => 'preco', 'order' => 'desc'])); ?>">
                                Preço (maior para menor)
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item <?= ($sortBy == 'data_cadastro' && $sortOrder == 'desc') ? 'active' : ''; ?>" 
                               href="<?= current_url(); ?>?<?= http_build_query(array_merge($_GET, ['sort' => 'data_cadastro', 'order' => 'desc'])); ?>">
                                Mais recentes
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item <?= ($sortBy == 'data_cadastro' && $sortOrder == 'asc') ? 'active' : ''; ?>" 
                               href="<?= current_url(); ?>?<?= http_build_query(array_merge($_GET, ['sort' => 'data_cadastro', 'order' => 'asc'])); ?>">
                                Mais antigos
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Lista de produtos -->
            <?php if (empty($produtos)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> Nenhum produto encontrado com os filtros selecionados.
                </div>
            <?php else: ?>
              
                <div class="row g-4">
                    <?php foreach($produtos as $produto): ?>
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="card h-100 shadow-sm product-card">
                                <!-- Badge de desconto -->
                                <?php if (isset($produto['desconto']) && $produto['desconto'] > 0): ?>
                                    <div class="position-absolute top-0 end-0 p-2">
                                        <span class="badge bg-danger">
                                            -<?= $produto['desconto']; ?>%
                                        </span>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Imagem do produto -->
                                <a href="<?= site_url('detalhesproduto/' . $produto['id_produto']); ?>" class="text-decoration-none">
                                    <img src="<?= base_url( 'uploads/produtos/'.($produto['arquivo'] != '' ? $produto['arquivo'] : 'produto-default.jpg'))?>" 
                                         class="card-img-top" 
                                         alt="<?= esc($produto['nome']); ?>">
                                </a>
                                
                                <div class="card-body d-flex flex-column">
                                    <!-- Nome do produto -->
                                    <h5 class="card-title">
                                        <a href="<?= site_url('produto/' . ($produto['slug'] ?? $produto['id_produto'])); ?>" class="text-decoration-none text-dark">
                                            <?= esc($produto['nome']); ?>
                                        </a>
                                    </h5>
                                    
                                    <!-- Preços -->
                                    <?php if (!isset($config['mostrar_precos']) || $config['mostrar_precos'] == 1): ?>
                                        <div class="product-price mt-auto">
                                            <?php if (isset($produto['preco_antigo']) && $produto['preco_antigo'] > 0): ?>
                                                <span class="old-price text-muted text-decoration-line-through">
                                                    <?= $produto['preco_antigo_formatado']; ?>
                                                </span>
                                                <span class="new-price text-primary fw-bold">
                                                    <?= $produto['valor_de_venda']; ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="price text-primary fw-bold">
                                                    <?= $produto['valor_de_venda']; ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Botões -->
                                    <div class="d-flex gap-2 mt-3 align-items-center">
                                        <!-- Botão Ver Detalhes -->
                                        <a href="<?= site_url('produto/' . ($produto['slug'] ?? $produto['id_produto'])); ?>" 
                                        class="btn btn-outline-primary py-2">
                                            <i class="fas fa-eye"></i>
                                            <span class="d-none d-sm-inline ms-1">Ver</span>
                                        </a>
    
                                        <?php if (isset($produto['quantidade']) && $produto['quantidade'] > 0): ?>
                                            <!-- Botão Carrinho (disponível) -->
                                            <a href="<?= site_url('carrinho/adicionar/' . $produto['id_produto']); ?>" 
                                            class="btn btn-primary flex-grow-1 py-2">
                                                <i class="fas fa-shopping-cart me-1 me-sm-2"></i>
                                                <!-- <span class="">Adicionar ao</span> -->
                                                <span class="d-none d-sm-inline">Carrinho</span>
                                            </a>
                                        <?php else: ?>
                                            <!-- Botão Indisponível -->
                                            <button type="button" class="btn btn-secondary flex-grow-1 py-2" disabled>
                                                <i class="fas fa-times-circle me-1 me-sm-2"></i>
                                                <span class="d-none d-sm-inline">Indisponível</span>
                                                <span class="d-inline d-sm-none">Esgotado</span>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Paginação -->
                <?php if (isset($pager)): ?>
                    <div class="col-md-9 d-flex justify-content-center mt-4">
                        <div class="">
                            <?= $pager->links('default', 'bootstrap_full') ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- CSS Adicional -->
<style>
.product-card {
    transition: transform 0.2s;
}

.product-card:hover {
    transform: translateY(-5px);
}

.product-card .card-img-top {
    height: 200px;
    object-fit: cover;
}

.product-card .card-title {
    font-size: 1rem;
    line-height: 1.4;
    height: 2.8em;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.product-price {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.old-price {
    font-size: 0.875rem;
}

.new-price, .price {
    font-size: 1.25rem;
}

/* Estilo para paginação */
.pagination {
    justify-content: center;
}

.pagination .page-item .page-link {
    color: var(--bs-primary);
    border-color: var(--bs-primary);
}

.pagination .page-item.active .page-link {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
    color: white;
}

.pagination .page-item.disabled .page-link {
    color: var(--bs-gray);
    border-color: var(--bs-gray-300);
}

/* Estilo para dropdown de ordenação */
.dropdown-menu {
    min-width: 200px;
}

.dropdown-item.active {
    background-color: var(--bs-primary);
    color: white;
}

/* Estilo para filtros */
.form-select:focus,
.form-control:focus {
    border-color: var(--bs-primary);
    box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.25);
}

/* Responsividade */
@media (max-width: 767.98px) {
    .product-card .card-img-top {
        height: 150px;
    }
    
    .product-card .btn {
        padding: 0.375rem 0.5rem;
        font-size: 0.875rem;
    }
    
    .product-card .card-title {
        font-size: 0.875rem;
        height: 2.4em;
    }
    
    .new-price, .price {
        font-size: 1rem;
    }



    .pagination .page-item .page-link {
        background-color: #dc3545 !important; /* Vermelho do Bootstrap (bg-danger) */
        color: white !important; /* Texto branco para contraste */
        border-color: #dc3545 !important; /* Borda vermelha */
    }
    .pagination .page-item.active .page-link {
        background-color: #a71d2a !important; /* Vermelho mais escuro para página ativa */
        border-color: #a71d2a !important;
    }
    .pagination .page-item.disabled .page-link {
        background-color: #dc3545 !important; /* Vermelho para botões desativados */
        opacity: 0.6;
    }

</style>
