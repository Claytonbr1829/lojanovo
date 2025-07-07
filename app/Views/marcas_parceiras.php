
<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="h2 mb-3">Marcas Parceiras</h1>
        <p class="lead mb-0">Conheça as empresas que confiam em nossa qualidade e trabalham conosco.</p>
    </div>
    
    <?php if (empty($marcasParceiras)): ?>
        <div class="alert alert-info text-center">
            Nenhuma marca parceira encontrada.
        </div>
    <?php else: ?>
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4 justify-content-center">
            <?php foreach ($marcasParceiras as $marca): ?>
                <div class="col text-center mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body d-flex flex-column justify-content-center p-4">
                            <div class="mb-3">
                                <img src="<?= base_url('uploads/marcas/' . $marca['logo']); ?>"
                                     class="img-fluid marca-logo"
                                     alt="<?= esc($marca['nome']); ?>"
                                     style="max-height: 120px; max-width: 100%;">
                            </div>
                            <h5 class="card-title"><?= esc($marca['nome']); ?></h5>
                            <?php if (!empty($marca['link'])): ?>
                                <a href="<?= $marca['link']; ?>" target="_blank" class="btn btn-sm btn-outline-primary mt-3">
                                    Visitar site
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
    .marca-logo {
        transition: transform 0.3s ease;
    }
    .card:hover .marca-logo {
        transform: scale(1.1);
    }
</style>

