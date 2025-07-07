/**
 * SwapShop - Script principal da loja
 * Desenvolvido para CodeIgniter 4.0
 */

/* Funções para executar quando o DOM estiver carregado */
document.addEventListener('DOMContentLoaded', function () {
    // Inicializa os tooltips do Bootstrap
    initTooltips();

    // Inicializa os dropdowns do Bootstrap
    initDropdowns();

    // Configurações para o carrossel principal
    initMainCarousel();

    // Inicializa o comportamento do botão de voltar ao topo
    initBackToTop();

    // Inicializa comportamentos de validação de formulários
    initFormValidation();

    // Inicializa o carrinho de compras
    initCart();
});

/**
 * Inicializa tooltips do Bootstrap
 */
function initTooltips() {
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
}

/**
 * Inicializa dropdowns do Bootstrap
 */
function initDropdowns() {
    const dropdownElementList = document.querySelectorAll('.dropdown-toggle');
    [...dropdownElementList].map(dropdownToggleEl => new bootstrap.Dropdown(dropdownToggleEl));
}

/**
 * Configurações para o carrossel principal
 */
function initMainCarousel() {
    const mainCarousel = document.getElementById('mainCarousel');
    if (mainCarousel) {
        new bootstrap.Carousel(mainCarousel, {
            interval: 5000,
            keyboard: true,
            pause: 'hover',
            wrap: true
        });
    }
}

/**
 * Inicializa o botão de voltar ao topo
 */
function initBackToTop() {
    // Cria um botão de voltar ao topo dinamicamente se ele não existir
    if (!document.querySelector('.back-to-top')) {
        const backToTopBtn = document.createElement('button');
        backToTopBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
        backToTopBtn.className = 'back-to-top';
        backToTopBtn.setAttribute('title', 'Voltar ao topo');
        document.body.appendChild(backToTopBtn);

        // Mostra ou esconde o botão conforme o scroll
        window.addEventListener('scroll', function () {
            if (window.pageYOffset > 300) {
                backToTopBtn.classList.add('show');
            } else {
                backToTopBtn.classList.remove('show');
            }
        });

        // Evento de clique para rolar para o topo
        backToTopBtn.addEventListener('click', function () {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
}

/**
 * Inicializa comportamentos de validação de formulários
 */
function initFormValidation() {
    // Seleciona todos os formulários com a classe 'needs-validation'
    const forms = document.querySelectorAll('.needs-validation');

    // Itera sobre eles e previne o envio se inválidos
    Array.from(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }

            form.classList.add('was-validated');
        }, false);
    });
}

/**
 * Inicializa funcionalidades do carrinho de compras
 */
function initCart() {
    // Seleciona os botões de adicionar ao carrinho
    const addToCartButtons = document.querySelectorAll('.add-to-cart');

    // Adiciona o evento de clique em cada botão
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();

            const productId = this.getAttribute('data-product-id');
            const quantity = document.querySelector(`#quantity_${productId}`)
                ? document.querySelector(`#quantity_${productId}`).value
                : 1;

            addToCart(productId, quantity);
        });
    });

    // Seleciona os botões de quantidade
    const quantityButtons = document.querySelectorAll('.btn-quantity');

    // Adiciona eventos nos botões de quantidade
    quantityButtons.forEach(button => {
        button.addEventListener('click', function () {
            const action = this.getAttribute('data-action');
            const input = this.closest('.quantity-control').querySelector('input');
            let value = parseInt(input.value, 10);

            if (action === 'increase') {
                if (value < parseInt(input.getAttribute('max'), 10)) {
                    input.value = value + 1;
                }
            } else {
                if (value > parseInt(input.getAttribute('min'), 10)) {
                    input.value = value - 1;
                }
            }

            // Dispara evento de alteração para atualizar qualquer lógica dependente
            input.dispatchEvent(new Event('change'));
        });
    });
}

/**
 * Adiciona um produto ao carrinho via AJAX
 */
function addToCart(productId, quantity) {
    // Mostra indicador de carregamento
    showLoading();

    // Faz a requisição AJAX para adicionar ao carrinho
    fetch(`${baseUrl}/carrinho/adicionar`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            id_produto: productId,
            quantidade: quantity
        })
    })
        .then(response => response.json())
        .then(data => {
            hideLoading();

            if (data.success) {
                showNotification('Produto adicionado ao carrinho!', 'success');

                // Atualiza o contador do carrinho
                const cartCounter = document.querySelector('.cart-count');
                if (cartCounter) {
                    cartCounter.textContent = data.total_itens;
                }
            } else {
                showNotification(data.message || 'Erro ao adicionar produto.', 'error');
            }
        })
        .catch(error => {
            hideLoading();
            showNotification('Erro ao adicionar produto ao carrinho.', 'error');
            console.error('Erro:', error);
        });
}

/**
 * Exibe uma notificação para o usuário
 */
function showNotification(message, type = 'info') {
    // Verifica se já existe o container de notificações
    let notifContainer = document.querySelector('.notification-container');

    if (!notifContainer) {
        notifContainer = document.createElement('div');
        notifContainer.className = 'notification-container';
        document.body.appendChild(notifContainer);
    }

    // Cria a notificação
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <p>${message}</p>
        </div>
        <button class="notification-close">&times;</button>
    `;

    notifContainer.appendChild(notification);

    // Adiciona a classe show após um pequeno delay para ativar a animação
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);

    // Configura a remoção automática após 5 segundos
    setTimeout(() => {
        notification.classList.remove('show');

        // Remove o elemento após a animação
        notification.addEventListener('transitionend', function () {
            notifContainer.removeChild(notification);
        });
    }, 5000);

    // Adiciona evento no botão de fechar
    const closeBtn = notification.querySelector('.notification-close');
    closeBtn.addEventListener('click', function () {
        notification.classList.remove('show');

        // Remove o elemento após a animação
        notification.addEventListener('transitionend', function () {
            notifContainer.removeChild(notification);
        });
    });
}

/**
 * Mostra indicador de carregamento
 */
function showLoading() {
    // Verifica se já existe o overlay de loading
    let loadingOverlay = document.querySelector('.loading-overlay');

    if (!loadingOverlay) {
        loadingOverlay = document.createElement('div');
        loadingOverlay.className = 'loading-overlay';
        loadingOverlay.innerHTML = '<div class="spinner"><i class="fas fa-circle-notch fa-spin"></i></div>';
        document.body.appendChild(loadingOverlay);
    }

    // Mostra o overlay
    loadingOverlay.classList.add('active');
}

/**
 * Esconde indicador de carregamento
 */
function hideLoading() {
    const loadingOverlay = document.querySelector('.loading-overlay');

    if (loadingOverlay) {
        loadingOverlay.classList.remove('active');

        // Remove após a animação
        loadingOverlay.addEventListener('transitionend', function () {
            if (!loadingOverlay.classList.contains('active')) {
                document.body.removeChild(loadingOverlay);
            }
        });
    }
}

/**
 * Define a URL base para uso em chamadas AJAX
 * Deve ser definida no layout principal por meio de uma variável global
 */
let baseUrl = document.querySelector('meta[name="base-url"]')
    ? document.querySelector('meta[name="base-url"]').getAttribute('content')
    : window.location.origin; 