<!-- Rodapé -->
<footer class="main-footer bg-dark text-white py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4 mb-md-0">
                <h5>Sobre a <?= isset($config['nome_loja']) ? $config['nome_loja'] : (isset($aparencia['titulo_site']) ? $aparencia['titulo_site'] : 'SwapShop') ?></h5>
                <p><?= isset($aparencia['descricao_site']) ? $aparencia['descricao_site'] : (isset($config['descricao']) ? $config['descricao'] : 'Somos uma loja virtual especializada em oferecer os melhores produtos com qualidade e preço justo.') ?></p>
                <?php if(isset($aparencia['texto_rodape']) && !empty($aparencia['texto_rodape'])): ?>
                <p><?= $aparencia['texto_rodape'] ?></p>
                <?php endif; ?>
                <div class="social-links mt-3">
                    <?php if (isset($config['facebook_url']) && !empty($config['facebook_url'])): ?>
                        <a href="<?= $config['facebook_url'] ?>" target="_blank" class="me-2"><i class="fab fa-facebook-f"></i></a>
                    <?php endif; ?>
                    <?php if (isset($config['linkedin_url']) && !empty($config['linkedin_url'])): ?>
                        <a href="<?= $config['linkedin_url'] ?>" target="_blank" class="me-2"><i class="fab fa-linkedin-in"></i></a>
                    <?php endif; ?>
                    <?php if (isset($config['twitter_url']) && !empty($config['twitter_url'])): ?>
                        <a href="<?= $config['twitter_url'] ?>" target="_blank" class="me-2"><i class="fab fa-twitter"></i></a>
                    <?php endif; ?>
                    <?php if (isset($config['instagram']) && !empty($config['instagram'])): ?>
                        <a href="<?= $config['instagram'] ?>" target="_blank" class="me-2"><i class="fab fa-instagram"></i></a>
                    <?php endif; ?>
                    <?php if (isset($config['youtube']) && !empty($config['youtube'])): ?>
                        <a href="<?= $config['youtube'] ?>" target="_blank"><i class="fab fa-youtube"></i></a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-4 mb-4 mb-md-0">
                <h5>Informações</h5>
                <ul class="list-unstyled">
                    <li><a href="<?= site_url('termos-de-uso') ?>">Termos de Uso</a></li>
                    <li><a href="<?= site_url('politica-de-privacidade') ?>">Política de Privacidade</a></li>
                    <li><a href="<?= site_url('politica-de-envio') ?>">Política de Envio</a></li>
                    <li><a href="<?= site_url('politica-de-devolucao') ?>">Política de Devolução</a></li>
                    <li><a href="<?= site_url('perguntas-frequentes') ?>">Perguntas Frequentes</a></li>
                </ul>
            </div>
            
            <?php if(isset($config['mostrar_contato_rodape']) && $config['mostrar_contato_rodape'] == 1): ?>
            <div class="col-md-4">
                <h5>Contato</h5>
                <ul class="list-unstyled">
                    <?php if (isset($config['endereco']) && !empty($config['endereco'])): ?>
                        <li><i class="fas fa-map-marker-alt me-2"></i> <?= $config['endereco'] ?></li>
                    <?php endif; ?>
                    <?php if (isset($config['telefone']) && !empty($config['telefone'])): ?>
                        <li><i class="fas fa-phone-alt me-2"></i> <?= $config['telefone'] ?></li>
                    <?php endif; ?>
                    <?php if (isset($config['email_contato']) && !empty($config['email_contato'])): ?>
                        <li><i class="fas fa-envelope me-2"></i> <?= $config['email_contato'] ?></li>
                    <?php endif; ?>
                    <?php if (isset($config['horario_funcionamento']) && !empty($config['horario_funcionamento'])): ?>
                        <li><i class="fas fa-clock me-2"></i> <?= $config['horario_funcionamento'] ?></li>
                    <?php endif; ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>
    </div>
</footer>

<!-- Copyright -->
<div class="copyright py-3 bg-secondary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="mb-md-0">© <?= date('Y') ?> <?= isset($config['nome_loja']) ? $config['nome_loja'] : 'SwapShop' ?>. Todos os direitos reservados.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <img src="<?= base_url('assets/img/pagamentos.png') ?>" alt="Métodos de Pagamento" class="payment-methods">
            </div>
        </div>
    </div>
</div> 