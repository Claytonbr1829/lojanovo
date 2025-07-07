<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);




$routes->get('/', 'Home::index');

// Rotas da loja virtual
$routes->get('categoria/(:segment)', 'Categoria::index/$1');
$routes->get('produto/(:segment)', 'Produto::index/$1');
$routes->get('marcas-parceiras', 'MarcasParceiras::index');
$routes->get('marca/(:num)', 'MarcasParceiras::show/$1');

// Rotas do carrinho
$routes->get('carrinho', 'Carrinho::index');
$routes->get('carrinho/adicionar/(:num)', 'Carrinho::adicionar/$1');
$routes->post('carrinho/adicionar/(:num)', 'Carrinho::adicionar/$1');
$routes->post('carrinho/atualizar', 'Carrinho::atualizar');
$routes->get('carrinho/remover/(:num)', 'Carrinho::remover/$1');
$routes->get('carrinho/limpar', 'Carrinho::limpar');

// Rota para CSS dinâmico
$routes->get('css/dinamico.css', 'Css::index');
$routes->get('css/dark.css', 'Css::dark');

$routes->get('minha-conta', 'MinhaConta::index');
$routes->get('detalhespedido/(:num)', 'MeusPedidos::detalhesPedido/$1');
$routes->get('meuspedidos', 'MeusPedidos::index');
$routes->post('meuspedidos/pesquisa','MeusPedidos::pesquisar');
// Pedidos
$routes->get('pedido/(:num)', 'Pedido::view/$1');
$routes->post('pedido/pagamento/(:num)', 'Pedido::pagamento/$1');
$routes->get('pedido/comprovante/(:num)', 'Pedido::comprovante/$1');

$routes->get('meusendereco','MeusEndereco::index');
// Rota para o Calcular Frete
//$routes->get('Frete','CalcularFrete::index');

$routes->post('calcular-frete', 'CalcularFrete::index');
$routes->options('calcular-frete', function () {
    return service('response')
        ->setHeader('Access-Control-Allow-Origin', '*')
        ->setHeader('Access-Control-Allow-Headers', '*')
        ->setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS');
});


// Rotas para o controlador de Produtos
$routes->get('produtos', 'Produtos::index');
$routes->get('produtos/destaque', 'Produtos::destaque');
$routes->get('produtos/mais-vendidos', 'Produtos::maisVendidos');
$routes->get('produtos/novidades', 'Produtos::novidades');
$routes->get('produtos/buscar', 'Produtos::buscar');

// Rotas para o controlador de Cliente
$routes->get('login', 'Cliente::login');
$routes->get('cadastro', 'Cliente::cadastro');
$routes->post('autenticar', 'Cliente::autenticar');
$routes->match(['post'], 'autenticar', 'Cliente::autenticar');
$routes->post('cadastrar', 'Cliente::salvar');
$routes->get('logout', 'Cliente::logout');
$routes->get('recuperar-senha', 'Cliente::recuperarSenha');
$routes->post('recuperar-senha', 'Cliente::enviarRecuperacao');
$routes->get('recuperar-senha/(:segment)', 'Cliente::redefinirSenha/$1');
$routes->post('redefinir-senha', 'Cliente::processarRedefinicaoSenha');
$routes->get('minha-conta', 'Cliente::minhaConta');
$routes->post('atualizar-dados', 'Cliente::atualizarDados');
$routes->get('alterar-senha', 'Cliente::alterarSenha');
$routes->post('cliente/alterar-senha', 'Cliente::processarAlteracaoSenha');
$routes->get('api/cidades/(:alpha)', 'Cliente::getCidades/$1');
$routes->get('cliente/editar-endereco', 'Cliente::editarEndereco');
$routes->post('cliente/salvar-endereco', 'Cliente::salvarEndereco');

// Rotas para o checkout
$routes->get('checkout', 'Checkout::index');
$routes->post('checkout/processar', 'Checkout::processar');
$routes->post('checkout/salvar-endereco', 'Checkout::salvarEndereco');
$routes->get('api/estados', 'Checkout::getEstados');
$routes->get('checkout/getMunicipios/(:num)', 'Checkout::getMunicipios/$1');


// Rotas para pagamento
$routes->get('meus-pedidos', 'Pagamento::meusPedidos');
$routes->get('pedido/(:num)', 'Pagamento::detalhesPedido/$1');
$routes->get('pedido/pagarP/(:num)', 'Pagamento::pagarPedido/$1');
$routes->post('pedido/pagar/(:any)', 'Pagamento::processarPagamento/$1');
$routes->get('pedido/pagar/(:any)', 'Pagamento::index/$1');

// Rotas para área administrativa
$routes->group('admin', function ($routes) {
    // Rotas de autenticação admin
    $routes->get('login', 'Admin\Auth::index');
    $routes->post('login', 'Admin\Auth::authenticate');
    $routes->get('logout', 'Admin\Auth::logout');

    // Dashboard admin
    $routes->get('dashboard', 'Admin\Dashboard::index');

    // Rotas de marcas parceiras
    $routes->get('marcas-parceiras', 'Admin\MarcasParceiras::index');
    $routes->get('marcas-parceiras/adicionar', 'Admin\MarcasParceiras::create');
    $routes->post('marcas-parceiras/adicionar', 'Admin\MarcasParceiras::store');
    $routes->get('marcas-parceiras/editar/(:num)', 'Admin\MarcasParceiras::edit/$1');
    $routes->post('marcas-parceiras/editar/(:num)', 'Admin\MarcasParceiras::update/$1');
    $routes->get('marcas-parceiras/excluir/(:num)', 'Admin\MarcasParceiras::delete/$1');
});

if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}