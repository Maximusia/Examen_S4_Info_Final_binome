<?= $this->extend('layouts/header') ?>

<?= $this->section('content') ?>

<?php
    $currentPercent = (int) ($savings_percent ?? 0);
    $savingsBalance = (int) ($savings_balance ?? 0);
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h1 class="mb-2"><i class="fas fa-piggy-bank text-dark"></i> Gestion de l'épargne</h1>
        <p class="text-muted">
            Définissez le pourcentage qui sera automatiquement placé en épargne lorsqu'un transfert entrant est reçu sur votre compte.
        </p>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card shadow">
            <div class="card-body p-4">
                <form action="<?= base_url('/savings') ?>" method="post" class="needs-validation" novalidate>
                    <?= csrf_field() ?>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="alert alert-dark mb-0">
                                <div class="small text-uppercase text-muted">Solde courant</div>
                                <div class="h3 mb-0"><?= number_format((int) ($balance ?? 0), 0, ',', ' ') ?> Ar</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-success mb-0">
                                <div class="small text-uppercase text-muted">Epargne cumulée</div>
                                <div class="h3 mb-0"><?= number_format($savingsBalance, 0, ',', ' ') ?> Ar</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="savings_percent" class="form-label">
                            Pourcentage d'épargne par transfert entrant
                        </label>
                        <div class="input-group">
                            <input
                                type="range"
                                class="form-range me-3"
                                id="savings_percent"
                                name="savings_percent"
                                min="0"
                                max="100"
                                value="<?= esc((string) $currentPercent) ?>"
                                required
                            >
                            <div class="input-group-text" style="min-width: 90px; justify-content: center;">
                                <span id="savings_percent_value"><?= esc((string) $currentPercent) ?>%</span>
                            </div>
                        </div>
                        <small class="form-text text-muted d-block mt-2">
                            0 % signifie qu'aucun montant n'est bloqué en épargne. 100 % signifie que le transfert entrant est totalement orienté vers l'épargne.
                        </small>
                        <div class="invalid-feedback">Veuillez choisir un pourcentage valide.</div>
                    </div>

                    <div class="card border-info mb-4">
                        <div class="card-body">
                            <h6 class="card-title mb-3"><i class="fas fa-calculator"></i> Aperçu rapide</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Exemple sur 10 000 Ar reçus</span>
                                <strong id="preview_savings">0 Ar</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Montant disponible immédiatement</span>
                                <strong id="preview_available">10 000 Ar</strong>
                            </div>
                            <div class="small text-muted mb-0">
                                L'épargne est calculée côté serveur à partir du pourcentage enregistré sur votre compte.
                            </div>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-dark btn-lg">
                            <i class="fas fa-save"></i> Enregistrer l'épargne
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <h6 class="mb-0"><i class="fas fa-bell"></i> Fonctionnement</h6>
            </div>
            <div class="card-body">
                <ul class="small mb-0">
                    <li>Le pourcentage défini s'applique aux transferts entrants reçus sur votre compte.</li>
                    <li>La part épargnée est ajoutée à votre solde d'épargne, séparé du solde principal.</li>
                    <li>Le reste du transfert reste disponible immédiatement dans votre solde courant.</li>
                </ul>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0"><i class="fas fa-chart-pie"></i> Résumé</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Pourcentage actuel</span>
                    <strong><?= esc((string) $currentPercent) ?> %</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Epargne totale</span>
                    <strong><?= number_format($savingsBalance, 0, ',', ' ') ?> Ar</strong>
                </div>
                <div class="d-flex justify-content-between mb-0">
                    <span>Solde principal</span>
                    <strong><?= number_format((int) ($balance ?? 0), 0, ',', ' ') ?> Ar</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const savingsSlider = document.getElementById('savings_percent');
    const savingsValue = document.getElementById('savings_percent_value');
    const previewSavings = document.getElementById('preview_savings');
    const previewAvailable = document.getElementById('preview_available');
    const exampleAmount = 10000;

    function formatAr(value) {
        return new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'MGA',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(value).replace('MGA', 'Ar');
    }

    function refreshSavingsPreview() {
        const percent = parseInt(savingsSlider.value, 10) || 0;
        const savings = Math.floor((exampleAmount * percent) / 100);
        savingsValue.textContent = `${percent}%`;
        previewSavings.textContent = formatAr(savings);
        previewAvailable.textContent = formatAr(exampleAmount - savings);
    }

    savingsSlider.addEventListener('input', refreshSavingsPreview);
    refreshSavingsPreview();
</script>

<?= $this->endSection() ?>
<?= $this->include('layouts/footer') ?>
