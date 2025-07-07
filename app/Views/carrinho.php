<?php
// View da página de carrinho
?>

<div class="container py-4">
    <h1 class="h3 mb-4">Carrinho de Compras</h1>
    
    <!-- Mensagens de feedback -->
    <?php if (session()->has('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php endif; ?>
    
    <?php if (session()->has('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php endif; ?>
    
    <?php if (empty($itens)): ?>
        <!-- Carrinho vazio -->
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-shopping-cart fa-4x text-muted"></i>
            </div>
            <h2 class="h4 mb-3">Seu carrinho está vazio</h2>
            <p class="text-muted mb-4">Parece que você ainda não adicionou nenhum produto ao seu carrinho.</p>
            <a href="<?= site_url('produtos') ?>" class="btn btn-primary">
                <i class="fas fa-arrow-left me-2"></i> Continuar Comprando
            </a>
        </div>
    <?php else: ?>
        <!-- Conteúdo do carrinho -->
        <div class="row">
            <!-- Itens do carrinho -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 mb-0">Produtos (<?= count($itens) ?>)</span>
                            <a href="<?= site_url('carrinho/limpar') ?>" class="btn btn-sm btn-outline-danger" 
                               onclick="return confirm('Tem certeza que deseja limpar o carrinho?')">
                                <i class="fas fa-trash me-1"></i> Limpar Carrinho
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <?php foreach ($itens as $item): ?>
                                <li class="list-group-item py-3" id="item-<?= $item['id'] ?>">
                                    <div class="row align-items-center">
                                        <!-- Imagem do produto -->
                                        <div class="col-2 col-md-2">
                                            <a href="<?= site_url('produto/' . ($item['slug'] ?? $item['id'])) ?>">
                                                <img src="<?= base_url('uploads/produtos/' . $item['imagem']) ?>" 
                                                     class="img-fluid rounded" alt="<?= esc($item['nome']) ?>">
                                            </a>
                                        </div>
                                        
                                        <!-- Informações do produto -->
                                        <div class="col-10 col-md-10">
                                            <div class="row align-items-center">
                                                <div class="col-12 col-md-5">
                                                    <h5 class="mb-1">
                                                        <a href="<?= site_url('produto/' . ($item['slug'] ?? $item['id'])) ?>" 
                                                           class="text-dark text-decoration-none">
                                                            <?= esc($item['nome']) ?>
                                                        </a>
                                                    </h5>
                                                    <p class="text-muted small mb-0">Preço unitário: <?= $item['preco_formatado'] ?></p>
                                                </div>
                                                
                                                <!-- Controle de quantidade -->
                                                <div class="col-5 col-md-3">
                                                    <div class="input-group input-group-sm">
                                                        <button type="button" class="btn btn-sm btn-quantity" data-action="decrease" data-id="<?= $item['id'] ?>">-</button>
                                                        <input type="number" name="quantidade[<?= $item['id'] ?>]" id="quantity_<?= $item['id'] ?>" 
                                                        class="form-control form-control-sm text-center cart-quantity" data-id="<?= $item['id'] ?>" 
                                                            value="<?= $item['quantidade'] ?>" 
                                                            min="1" max="<?= $item['estoque'] ?>" readonly>
                                                        <button type="button" class="btn btn-sm btn-quantity" data-action="increase" data-id="<?= $item['id'] ?>">+</button>
                                                    </div>
                                                    <small class="d-block text-muted mt-1">
                                                        <?= $item['estoque'] ?> disponíveis
                                                    </small>
                                                </div>
                                                
                                                <!-- Subtotal -->
                                                <div class="col-4 col-md-2 text-end">
                                                    <span class="fw-bold item-subtotal"><?= $item['subtotal_formatado'] ?></span>
                                                </div>
                                                
                                                <!-- Remover item -->
                                                <div class="col-3 col-md-2 text-end">
                                                    <a href="<?= site_url('carrinho/remover/' . $item['id']) ?>" 
                                                       class="btn btn-sm btn-outline-danger"
                                                       onclick="return confirm('Tem certeza que deseja remover este item?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="card-footer bg-white">
                        <a href="<?= base_url() ?>" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i> Continuar Comprando
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Resumo do pedido -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Resumo do Pedido</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span>Subtotal:</span>
                            <span class="fw-bold cart-total"><?= $total_formatado ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Frete:</span>
                            <span>Calculado no próximo passo</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-bold">Total:</span>
                            <span class="fw-bold cart-total"><?= $total_formatado ?></span>
                        </div>
                        <a href="<?= site_url('checkout') ?>" class="btn btn-primary w-100 <?= $total > 0 ? '' : 'disabled' ?>">
                            Finalizar Compra <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Cupom de desconto (para implementação futura) -->
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Cupom de Desconto</h5>
                    </div>
                    <div class="card-body">
                        <form action="#" method="post">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" placeholder="Código do cupom" aria-label="Código do cupom">
                                <button class="btn btn-outline-secondary" type="button">Aplicar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Script para atualizar o carrinho via AJAX -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const baseUrl = window.location.origin;
    
    // Função para mostrar notificação
    function showNotification(message, type = 'success') {
        // Implemente com seu sistema de notificação preferido
        console.log(`${type}: ${message}`);
        alert(`${type}: ${message}`);
    }
    
    // Função para atualizar o carrinho
    async function updateCartItem(id, quantidade) {
        const input = document.querySelector(`.cart-quantity[data-id="${id}"]`);
        if (!input) return;
        
        // Salva o valor antigo para possível reversão
        const oldValue = input.value;
        
        try {
            const response = await fetch(`${baseUrl}/carrinho/atualizar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `id=${id}&quantidade=${quantidade}`
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Atualiza a interface
                updateCartUI(data.data);
                
                // Mostra mensagem de sucesso
                //showNotification(data.message);
            } else {
                // Reverte o valor no input
                input.value = oldValue;
                
                // Mostra mensagem de erro
                showNotification(data.message, 'error');
                
                // Atualiza estoque disponível se fornecido
                if (data.data?.estoque_disponivel) {
                    const stockElement = document.querySelector(`#item-${id} .stock-available`);
                    if (stockElement) {
                        stockElement.textContent = data.data.estoque_disponivel;
                    }
                }
            }
        } catch (error) {
            console.error('Erro:', error);
            input.value = oldValue;
            showNotification('Erro ao conectar com o servidor', 'error');
        }
    }
    
    // Função para atualizar a interface
    function updateCartUI(data) {
        // Atualiza subtotal do item específico
        if (data.item_id) {
            const itemElement = document.querySelector(`#item-${data.item_id}`);
            if (itemElement) {
                const subtotalElement = itemElement.querySelector('.item-subtotal');
                if (subtotalElement) {
                    subtotalElement.textContent = data.subtotal;
                }
                
                // Atualiza estoque disponível
                const stockElement = itemElement.querySelector('.stock-available');
                if (stockElement && data.estoque_disponivel !== undefined) {
                    stockElement.textContent = data.estoque_disponivel;
                }
                
                // Remove item se quantidade for zero
                if (data.quantidade <= 0) {
                    itemElement.remove();
                }
            }
        }
        
        // Atualiza totais do carrinho
        document.querySelectorAll('.cart-total').forEach(el => {
            el.textContent = data.total;
        });
        
        // Atualiza contador de itens
        const cartCount = document.querySelector('.cart-count');
        if (cartCount) {
            cartCount.textContent = data.total_itens;
        }
        
        // Desabilita botão de finalizar compra se carrinho vazio
        const checkoutBtn = document.querySelector('.btn-checkout');
        if (checkoutBtn) {
            checkoutBtn.disabled = data.total_itens === 0;
        }
    }
    
    // Event listeners para botões de quantidade
    document.querySelectorAll('.btn-quantity').forEach(button => {
        button.addEventListener('click', function() {
            const action = this.dataset.action;
            const id = this.dataset.id;
            const input = document.querySelector(`.cart-quantity[data-id="${id}"]`);
            
            if (!input) return;
            
            let value = parseInt(input.value);
            const min = parseInt(input.min) || 1;
            const max = parseInt(input.max) || Infinity;
            
            if (action === 'increase' && value < max) {
                input.value = value + 1;
                updateCartItem(id, input.value);
            } else if (action === 'decrease' && value > min) {
                input.value = value - 1;
                updateCartItem(id, input.value);
            }
        });
    });
    
    // Event listener para alteração manual no input
    document.querySelectorAll('.cart-quantity').forEach(input => {
        input.addEventListener('change', function() {
            const id = this.dataset.id;
            const value = parseInt(this.value);
            const min = parseInt(this.min) || 1;
            const max = parseInt(this.max) || Infinity;
            
            if (isNaN(value) || value < min || value > max) {
                this.value = this.defaultValue;
                showNotification(`Quantidade deve estar entre ${min} e ${max}`, 'error');
                return;
            }
            
            updateCartItem(id, value);
        });
    });
});

</script> 