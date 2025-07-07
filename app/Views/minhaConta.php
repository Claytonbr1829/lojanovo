<?php
namespace App\Views\minhaConta; ?>

<div class="container">
    <div class="row row-cols-2">
        <!-- Coluna Principal -->
        <article class="col-md-9">
            <h3 class="mb-3">Minha Conta</h3>
            <h4 class="lead mb-4">Bem vindo a sua conta! Aqui você pode gerenciar todos seus dados e verificar seus
                pedidos.</h4>

            <div class="row">
                <!-- Bloco Meus Pedidos -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <a href="<?= site_url('meuspedidos') ?>">
                            <div class="card-body d-flex align-items-center">

                                <div class="me-4">
                                    <img src="/MinhaConta/meuspedidos.png" alt="Meus pedidos"
                                        class="img-fluid hover-icon" style="width: 60px;">
                                </div>
                                <div>
                                    <h3 class="h5 text-danger mb-1">MEUS PEDIDOS</h3>
                                    <p class="mb-0 text-muted">Acompanhe todos os seus pedidos.</p>
                                </div>

                            </div>
                        </a>
                    </div>
                </div>

                <!-- Bloco Meus Orçamentos -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <a href="">
                            <div class="card-body d-flex align-items-center">

                                <div class="me-4">
                                    <img src="/MinhaConta/meusocamentos.png" alt="Meus Orçamentos"
                                        class="img-fluid hover-icon" style="width: 60px;">
                                </div>
                                <div>
                                    <h3 class="h5 text-danger mb-1">MEUS ORÇAMENTOS</h3>
                                    <p class="mb-0 text-muted">Veja o histórico dos orçamentos solicitados e recebidos.
                                    </p>
                                </div>

                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Bloco Central de Mensagens -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <a href="">
                            <div class="card-body d-flex align-items-center">

                                <div class="me-4">
                                    <img src="/MinhaConta/mensagens.png" alt="Central de Mensagens"
                                        class="img-fluid hover-icon" style="width: 60px;">
                                </div>
                                <div>
                                    <h3 class="h5 text-danger mb-1">CENTRAL DE MENSAGENS</h3>
                                    <p class="mb-0 text-muted">Acesse suas mensagens recebidas e enviadas.</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Bloco Meus Endereços -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <a href="<?= site_url('meusendereco') ?>">
                            <div class="card-body d-flex align-items-center">
                                <div class="me-4">
                                    <img src="/MinhaConta/meusenderecos.png" alt="Meus Endereços"
                                        class="img-fluid hover-icon" style="width: 60px;">
                                </div>
                                <div>
                                    <h3 class="h5 text-danger mb-1">MEUS ENDEREÇOS</h3>
                                    <p class="mb-0 text-muted">Você pode adicionar, remover ou editar seus endereços.
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Bloco Informações Pessoais -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <a href="<?= site_url('cliente/editar-endereco') ?>">
                            <div class="card-body d-flex align-items-center">
                                <div class="me-4">
                                    <img src="/MinhaConta/informacoespessoais.png" alt="Informações Pessoais"
                                        class="img-fluid hover-icon" style="width: 60px;">
                                </div>
                                <div>
                                    <h3 class="h5 text-danger mb-1">INFORMAÇÕES PESSOAIS</h3>
                                    <p class="mb-0 text-muted">Acesse seus dados pessoais.</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Bloco Mudar Senha -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <a href="<?= site_url('alterar-senha') ?>">
                            <div class="card-body d-flex align-items-center">
                                <div class="me-4">
                                    <img src="/MinhaConta/mudarsenha.png" alt="Mudar minha senha"
                                        class="img-fluid hover-icon" style="width: 60px;">
                                </div>
                                <div>
                                    <h3 class="h5 text-danger mb-1">MUDAR MINHA SENHA</h3>
                                    <p class="mb-0 text-muted">Altere sua senha.</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Bloco Lista de Desejos -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <a href="">
                            <div class="card-body d-flex align-items-center">
                                <div class="me-4">
                                    <img src="/MinhaConta/minhalistadesenhos.png" alt="Minha Lista de Desejos"
                                        class="img-fluid hover-icon" style="width: 60px;">
                                </div>
                                <div>
                                    <h3 class="h5 text-danger mb-1">MINHA LISTA DE DESEJOS</h3>
                                    <p class="mb-0 text-muted">Veja os produtos da sua lista de desejos.</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Bloco Newsletter -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <a href="">
                            <div class="card-body d-flex align-items-center">
                                <div class="me-4">
                                    <img src="/MinhaConta/news.png" alt="Gerenciador de Newsletter"
                                        class="img-fluid hover-icon" style="width: 60px;">
                                </div>
                                <div>
                                    <h3 class="h5 text-danger mb-1">GERENCIADOR DE NEWSLETTER</h3>
                                    <p class="mb-0 text-muted">Escolha quais os comunicados você deseja receber.</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Bloco Logoff -->
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <a href="<?= site_url('logout') ?>">
                            <div class="card-body d-flex align-items-center">
                                <div class="me-4">
                                    <img src="/MinhaConta/logoff.png" alt="Fazer Logoff" class="img-fluid hover-icon"
                                        style="width: 60px;">
                                </div>
                                <div>
                                    <h3 class="h5 text-danger mb-1">FAZER LOGOFF</h3>
                                    <p class="mb-0 text-muted">Para sair do sistema.</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </article>

        <!-- Sidebar -->

        <aside class="col-md-3">
            <!-- Seção de Produtos -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header border-bottom bg-primary produtos">
                    <h3 class="h6 mb-0 text-white">Categorias</h3>
                </div>
                <div class="card-body p-0">
                    <div class="accordion accordion-flush" id="accordionCategorias">
                        <?php foreach ($categorias as $index => $categoria): ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="flush-heading<?= $index ?>">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#flush-collapse<?= $index ?>" aria-expanded="false"
                                        aria-controls="flush-collapse<?= $index ?>">
                                        <?= esc($categoria['nome']) ?>
                                    </button>
                                </h2>
                                <div id="flush-collapse<?= $index ?>" class="accordion-collapse collapse"
                                    aria-labelledby="flush-heading<?= $index ?>" data-bs-parent="#accordionCategorias">
                                    <div class="accordion-body">
                                        <ul class="list-unstyled ps-3">
                                            <?php foreach ($produtos as $produto): ?>
                                                <?php if ($produto['id_categoria'] == $categoria['id_categoria']): ?>
                                                    <li class="mb-1">
                                                        <span class="me-1 text-primary">•</span>
                                                        <a href="<?= site_url('produto/' . ($produto['slug'] ?? $produto['id_produto'])); ?>"
                                                            class="text-decoration-none fst-normal text-black">
                                                            <?= esc($produto['nome']) ?>
                                                        </a>
                                                    </li>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </ul>


                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>



            <!-- Lista de Desejos -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h3 class="h6 mb-0">LISTA DE DESEJOS</h3>
                </div>
                <div class="card-body text-center">
                    <p class="text-muted">Nenhum Produto favoritado!</p>
                    <a href="#" class="text-danger fw-bold">» Minha lista</a>
                </div>
            </div>
        </aside>
    </div>
</div>