/**
 * SwapShop - Sistema de Loja Virtual
 * 
 * Arquivo JS principal para gerenciar funcionalidades da loja
 */

document.addEventListener('DOMContentLoaded', function () {
    // Inicializa todos os tooltips do Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Inicializa as funções específicas das páginas
    initProductPage();
    initCartPage();
    initSearchFilters();

    // Funcionalidade para adicionar ao carrinho
    const addToCartButtons = document.querySelectorAll('.add-to-cart');

    addToCartButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();

            const productId = this.getAttribute('data-product-id');
            const url = this.getAttribute('href');

            // Alterar visual do botão para indicar carregamento
            const originalContent = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            this.disabled = true;

            // Fazer requisição AJAX para adicionar ao carrinho
            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.json())
                .then(data => {
                    // Restaurar botão
                    this.innerHTML = originalContent;
                    this.disabled = false;

                    // Mostrar mensagem de sucesso
                    const alertDiv = document.createElement('div');
                    alertDiv.className = data.success ? 'alert alert-success' : 'alert alert-danger';
                    alertDiv.textContent = data.message;
                    alertDiv.style.position = 'fixed';
                    alertDiv.style.top = '20px';
                    alertDiv.style.right = '20px';
                    alertDiv.style.zIndex = '9999';
                    document.body.appendChild(alertDiv);

                    // Remover alerta após 3 segundos
                    setTimeout(() => {
                        alertDiv.style.opacity = '0';
                        alertDiv.style.transition = 'opacity 0.5s';
                        setTimeout(() => {
                            document.body.removeChild(alertDiv);
                        }, 500);
                    }, 3000);

                    // Atualizar contador do carrinho se houver sucesso
                    if (data.success && data.cartCount) {
                        updateCartCount(data.cartCount);
                    }
                })
                .catch(error => {
                    console.error('Erro ao adicionar produto ao carrinho:', error);
                    this.innerHTML = originalContent;
                    this.disabled = false;

                    // Mostrar mensagem de erro
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-danger';
                    alertDiv.textContent = 'Erro ao adicionar produto ao carrinho. Tente novamente.';
                    alertDiv.style.position = 'fixed';
                    alertDiv.style.top = '20px';
                    alertDiv.style.right = '20px';
                    alertDiv.style.zIndex = '9999';
                    document.body.appendChild(alertDiv);

                    // Remover alerta após 3 segundos
                    setTimeout(() => {
                        alertDiv.style.opacity = '0';
                        alertDiv.style.transition = 'opacity 0.5s';
                        setTimeout(() => {
                            document.body.removeChild(alertDiv);
                        }, 500);
                    }, 3000);
                });
        });
    });
});

/**
 * Funções para a página de produto
 */
function initProductPage() {
    // Seletor de quantidade
    const qtyBtns = document.querySelectorAll('.qty-btn');
    if (qtyBtns.length > 0) {
        qtyBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                const input = this.closest('.quantity-control').querySelector('input');
                const currentVal = parseInt(input.value);

                if (this.classList.contains('qty-minus')) {
                    if (currentVal > 1) {
                        input.value = currentVal - 1;
                    }
                } else {
                    const maxStock = parseInt(input.getAttribute('data-max') || 999);
                    if (currentVal < maxStock) {
                        input.value = currentVal + 1;
                    }
                }

                // Dispara evento para atualizar valores
                const event = new Event('change');
                input.dispatchEvent(event);
            });
        });
    }

    // Troca de imagens na galeria de produtos
    const productThumbnails = document.querySelectorAll('.product-thumbnail');
    if (productThumbnails.length > 0) {
        productThumbnails.forEach(thumb => {
            thumb.addEventListener('click', function () {
                const mainImg = document.querySelector('.product-main-img');
                const imgSrc = this.getAttribute('src');

                // Remove a classe active de todas as miniaturas
                productThumbnails.forEach(t => t.classList.remove('active'));

                // Adiciona a classe active na miniatura clicada
                this.classList.add('active');

                // Atualiza a imagem principal
                mainImg.setAttribute('src', imgSrc);
            });
        });
    }

    // Adicionar ao carrinho via AJAX
    const addToCartForm = document.getElementById('add-to-cart-form');
    if (addToCartForm) {
        addToCartForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const url = this.getAttribute('action');

            // Desabilita o botão para evitar múltiplos cliques
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;

            // Mostra um spinner no botão
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Adicionando...';

            fetch(url, {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Exibe mensagem de sucesso
                        showAlert('success', data.message || 'Produto adicionado ao carrinho!');

                        // Atualiza o contador do carrinho no header
                        updateCartCounter(data.cartCount);
                    } else {
                        // Exibe mensagem de erro
                        showAlert('danger', data.message || 'Erro ao adicionar produto ao carrinho.');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showAlert('danger', 'Ocorreu um erro ao adicionar o produto ao carrinho.');
                })
                .finally(() => {
                    // Restaura o botão
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
        });
    }
}

/**
 * Funções para a página de carrinho
 */
function initCartPage() {
    // Atualização de quantidade no carrinho
    const cartQtyInputs = document.querySelectorAll('.cart-qty-input');
    if (cartQtyInputs.length > 0) {
        cartQtyInputs.forEach(input => {
            // Armazena o valor original para comparação
            input.dataset.originalValue = input.value;

            // Adiciona evento de change
            input.addEventListener('change', function () {
                const itemId = this.dataset.itemId;
                const newQty = parseInt(this.value);

                // Verifica se a quantidade é válida
                if (isNaN(newQty) || newQty < 1) {
                    this.value = this.dataset.originalValue;
                    return;
                }

                // Atualiza o carrinho via AJAX
                updateCartItem(itemId, newQty, this);
            });
        });
    }

    // Botões de aumento/diminuição de quantidade
    const cartQtyBtns = document.querySelectorAll('.cart-qty-btn');
    if (cartQtyBtns.length > 0) {
        cartQtyBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                const input = this.closest('.quantity-control').querySelector('input');
                const currentVal = parseInt(input.value);

                if (this.classList.contains('qty-minus')) {
                    if (currentVal > 1) {
                        input.value = currentVal - 1;
                    }
                } else {
                    input.value = currentVal + 1;
                }

                // Dispara evento change para atualizar o carrinho
                const event = new Event('change');
                input.dispatchEvent(event);
            });
        });
    }

    // Remoção de item do carrinho
    const removeButtons = document.querySelectorAll('.remove-cart-item');
    if (removeButtons.length > 0) {
        removeButtons.forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();

                if (confirm('Tem certeza que deseja remover este item do carrinho?')) {
                    const url = this.getAttribute('href');

                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Remove o item da interface
                                this.closest('tr').remove();

                                // Atualiza os totais
                                updateCartTotals(data.subtotal, data.total);

                                // Atualiza o contador do carrinho
                                updateCartCounter(data.cartCount);

                                // Verifica se o carrinho está vazio
                                if (data.cartCount === 0) {
                                    document.querySelector('.cart-items').innerHTML =
                                        '<tr><td colspan="6" class="text-center py-4">Seu carrinho está vazio.</td></tr>';
                                    document.querySelector('.cart-actions').style.display = 'none';
                                }

                                showAlert('success', data.message || 'Item removido do carrinho!');
                            } else {
                                showAlert('danger', data.message || 'Erro ao remover item do carrinho.');
                            }
                        })
                        .catch(error => {
                            console.error('Erro:', error);
                            showAlert('danger', 'Ocorreu um erro ao remover o item do carrinho.');
                        });
                }
            });
        });
    }

    // Limpar carrinho
    const clearCartBtn = document.getElementById('clear-cart');
    if (clearCartBtn) {
        clearCartBtn.addEventListener('click', function (e) {
            e.preventDefault();

            if (confirm('Tem certeza que deseja limpar o carrinho? Todos os itens serão removidos.')) {
                const url = this.getAttribute('href');

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Atualiza a interface para mostrar o carrinho vazio
                            document.querySelector('.cart-items').innerHTML =
                                '<tr><td colspan="6" class="text-center py-4">Seu carrinho está vazio.</td></tr>';
                            document.querySelector('.cart-actions').style.display = 'none';

                            // Atualiza o contador do carrinho
                            updateCartCounter(0);

                            showAlert('success', data.message || 'Carrinho esvaziado com sucesso!');
                        } else {
                            showAlert('danger', data.message || 'Erro ao limpar o carrinho.');
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        showAlert('danger', 'Ocorreu um erro ao limpar o carrinho.');
                    });
            }
        });
    }
}

/**
 * Função para atualizar item do carrinho
 */
function updateCartItem(itemId, quantity, inputElement) {
    const url = '/carrinho/atualizar';
    const formData = new FormData();
    formData.append('id', itemId);
    formData.append('quantidade', quantity);

    // Armazena o elemento da linha
    const row = inputElement.closest('tr');

    // Adiciona spinner ao lado do input
    const qtyCell = inputElement.closest('td');
    const spinner = document.createElement('span');
    spinner.className = 'spinner-border spinner-border-sm ms-2';
    spinner.setAttribute('role', 'status');
    qtyCell.appendChild(spinner);

    fetch(url, {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Atualiza o preço do item
                const subtotalCell = row.querySelector('.item-subtotal');
                if (subtotalCell) {
                    subtotalCell.textContent = data.itemSubtotal;
                }

                // Atualiza os totais
                updateCartTotals(data.subtotal, data.total);

                // Atualiza o valor original do input
                inputElement.dataset.originalValue = quantity;

                // Mostra mensagem de sucesso
                showAlert('success', data.message || 'Carrinho atualizado!', 2000);
            } else {
                // Se houve erro, reverte para o valor original
                inputElement.value = inputElement.dataset.originalValue;
                showAlert('danger', data.message || 'Erro ao atualizar o carrinho.');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            // Se houve erro, reverte para o valor original
            inputElement.value = inputElement.dataset.originalValue;
            showAlert('danger', 'Ocorreu um erro ao atualizar o carrinho.');
        })
        .finally(() => {
            // Remove o spinner
            if (spinner) {
                spinner.remove();
            }
        });
}

/**
 * Filtros de busca de produtos
 */
function initSearchFilters() {
    // Ordenação de produtos
    const orderingLinks = document.querySelectorAll('.dropdown-item[href*="sort="]');
    if (orderingLinks.length > 0) {
        orderingLinks.forEach(link => {
            link.addEventListener('click', function (e) {
                // Não é preciso prevenir o comportamento padrão pois já manipulamos a URL
            });
        });
    }

    // Form de filtro - Limpar campos do form e submeter ao clicar no botão limpar
    const clearFilterBtn = document.querySelector('a.btn-outline-secondary[href*="produtos"]');
    if (clearFilterBtn) {
        clearFilterBtn.addEventListener('click', function (e) {
            e.preventDefault();

            // Limpa os campos do formulário
            const form = document.getElementById('filtro-form');

            // Limpa todos os inputs de texto e número
            form.querySelectorAll('input[type="text"], input[type="number"]').forEach(input => {
                input.value = '';
            });

            // Redefine selects para a primeira opção
            form.querySelectorAll('select').forEach(select => {
                select.selectedIndex = 0;
            });

            // Submete o formulário
            form.submit();
        });
    }
}

/**
 * Atualiza os totais do carrinho
 */
function updateCartTotals(subtotal, total) {
    const subtotalElement = document.getElementById('cart-subtotal');
    const totalElement = document.getElementById('cart-total');

    if (subtotalElement) {
        subtotalElement.textContent = subtotal;
    }

    if (totalElement) {
        totalElement.textContent = total;
    }
}

/**
 * Atualiza o contador de itens no carrinho no header
 */
function updateCartCounter(count) {
    const cartBadge = document.querySelector('.fa-shopping-cart').nextElementSibling;

    if (count > 0) {
        if (cartBadge) {
            cartBadge.textContent = count;
        } else {
            const newBadge = document.createElement('span');
            newBadge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger';
            newBadge.textContent = count;
            document.querySelector('.fa-shopping-cart').parentNode.appendChild(newBadge);
        }
    } else {
        if (cartBadge) {
            cartBadge.remove();
        }
    }
}

/**
 * Exibe alertas temporários
 */
function showAlert(type, message, duration = 4000) {
    // Cria elemento de alerta
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.style.maxWidth = '350px';

    // Adiciona o texto e botão fechar
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

    // Adiciona ao corpo do documento
    document.body.appendChild(alertDiv);

    // Remove automaticamente após o tempo definido
    setTimeout(() => {
        alertDiv.classList.remove('show');
        setTimeout(() => alertDiv.remove(), 300);
    }, duration);
}

// Função para atualizar o contador do carrinho
function updateCartCount(count) {
    const cartCountElement = document.querySelector('.fa-shopping-cart + .badge');
    if (cartCountElement) {
        cartCountElement.textContent = count;
    } else {
        // Se não existir o badge, criar um novo
        const cartIcon = document.querySelector('.position-relative .fa-shopping-cart');
        if (cartIcon) {
            const parent = cartIcon.closest('.position-relative');
            const badge = document.createElement('span');
            badge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger';
            badge.textContent = count;
            parent.appendChild(badge);
        }
    }
}

/**
 * JavaScript para funcionalidades da loja
 * 
 * Funções AJAX para o carrinho de compras e outras funcionalidades
 */

// Função para exibir notificações
function showNotification(message, type = 'success') {
    const notification = $('<div>')
        .addClass('toast position-fixed top-0 end-0 m-3')
        .attr('role', 'alert')
        .attr('aria-live', 'assertive')
        .attr('aria-atomic', 'true')
        .attr('data-bs-delay', '3000');

    const body = $('<div>')
        .addClass('toast-body d-flex align-items-center')
        .addClass(type === 'success' ? 'bg-success text-white' : 'bg-danger text-white');

    const icon = $('<i>')
        .addClass(type === 'success' ? 'fas fa-check-circle me-2' : 'fas fa-exclamation-circle me-2');

    body.append(icon).append(message);
    notification.append(body);

    // Adiciona e mostra a notificação
    $('body').append(notification);
    const toast = new bootstrap.Toast(notification);
    toast.show();

    // Remove do DOM quando fechada
    notification.on('hidden.bs.toast', function () {
        $(this).remove();
    });
}

$(document).ready(function () {
    // Atualização da quantidade de itens no carrinho
    $('.cart-quantity-input').on('change', function () {
        const $input = $(this);
        const produtoId = $input.data('product-id');
        const quantidade = $input.val();

        $.ajax({
            url: baseUrl + '/carrinho/atualizar',
            type: 'POST',
            data: {
                id: produtoId,
                quantidade: quantidade
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    // Atualiza os valores na tela
                    $('#subtotal-' + produtoId).text(response.subtotal);
                    $('#total-carrinho').text(response.total);

                    // Atualiza o badge do carrinho
                    updateCartBadge(response.total_itens);

                    // Feedback visual
                    $input.addClass('border-success');
                    setTimeout(function () {
                        $input.removeClass('border-success');
                    }, 1000);
                } else {
                    // Em caso de erro, exibe a mensagem e restaura o valor anterior
                    showNotification(response.message, 'error');
                    $input.val($input.data('original-value'));
                }
            },
            error: function () {
                // Em caso de erro no servidor
                showNotification('Erro ao atualizar o carrinho', 'error');
                $input.val($input.data('original-value'));
            }
        });
    });

    // Salva o valor original para restauração em caso de erro
    $('.cart-quantity-input').each(function () {
        $(this).data('original-value', $(this).val());
    });

    // Função para atualizar o badge do carrinho
    function updateCartBadge(count) {
        const $badge = $('.fa-shopping-cart').closest('a').find('.badge');

        if (count > 0) {
            if ($badge.length) {
                $badge.text(count);
            } else {
                $('.fa-shopping-cart').closest('a').append('<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">' + count + '</span>');
            }
        } else {
            $badge.remove();
        }
    }
}); 