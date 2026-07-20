<?= $this->extend('layouts/header') ?>

<?= $this->section('content') ?>

<div class="row mb-4">
    <div class="col-md-12">
        <h1 class="mb-2">
            <i class="fas fa-chart-bar text-primary"></i> Tableau de bord Opérateur
        </h1>
        <p class="text-muted">Statistiques et gestion de la plateforme Mobile Money</p>
    </div>
</div>

<!-- Indicateurs clés (KPI) -->
<div class="row mb-4">
    <!-- Total Utilisateurs -->
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card border-left-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Utilisateurs
                        </div>
                        <div class="h3 mb-0 font-weight-bold">
                            <?= $total_users ?? 0 ?>
                        </div>
                    </div>
                    <i class="fas fa-users fa-2x text-primary opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Transactions -->
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card border-left-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Transactions
                        </div>
                        <div class="h3 mb-0 font-weight-bold">
                            <?= $total_transactions ?? 0 ?>
                        </div>
                    </div>
                    <i class="fas fa-exchange-alt fa-2x text-success opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenu Frais -->
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card border-left-danger">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Revenu Frais
                        </div>
                        <div class="h3 mb-0 font-weight-bold">
                            <?= number_format($total_fees ?? 0, 0, ',', ' ') ?> FCFA
                        </div>
                    </div>
                    <i class="fas fa-coins fa-2x text-danger opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Volume Total -->
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card border-left-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Volume Total
                        </div>
                        <div class="h3 mb-0 font-weight-bold">
                            <?= number_format($total_volume ?? 0, 0, ',', ' ') ?> FCFA
                        </div>
                    </div>
                    <i class="fas fa-chart-pie fa-2x text-warning opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistiques détaillées par type d'opération -->
<div class="row mb-4">
    <div class="col-md-12">
        <h5 class="mb-3">
            <i class="fas fa-bar-chart"></i> Détail par type d'opération
        </h5>
    </div>

    <!-- Dépôts -->
    <div class="col-md-4 col-lg-3 mb-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-success">
                    <i class="fas fa-arrow-down"></i> Dépôts
                </h6>
                <p class="card-text">
                    <span class="badge bg-success">
                        <?= $deposits_count ?? 0 ?> transactions
                    </span>
                </p>
                <div class="h5 text-success mb-0">
                    <?= number_format($deposits_volume ?? 0, 0, ',', ' ') ?> FCFA
                </div>
                <small class="text-muted">Aucuns frais</small>
            </div>
        </div>
    </div>

    <!-- Retraits -->
    <div class="col-md-4 col-lg-3 mb-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-warning">
                    <i class="fas fa-arrow-up"></i> Retraits
                </h6>
                <p class="card-text">
                    <span class="badge bg-warning">
                        <?= $withdrawals_count ?? 0 ?> transactions
                    </span>
                </p>
                <div class="h5 text-warning mb-0">
                    <?= number_format($withdrawals_volume ?? 0, 0, ',', ' ') ?> FCFA
                </div>
                <small class="text-muted">
                    Frais: <?= number_format($withdrawals_fees ?? 0, 0, ',', ' ') ?> FCFA
                </small>
            </div>
        </div>
    </div>

    <!-- Transferts -->
    <div class="col-md-4 col-lg-3 mb-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-info">
                    <i class="fas fa-exchange-alt"></i> Transferts
                </h6>
                <p class="card-text">
                    <span class="badge bg-info">
                        <?= $transfers_count ?? 0 ?> transactions
                    </span>
                </p>
                <div class="h5 text-info mb-0">
                    <?= number_format($transfers_volume ?? 0, 0, ',', ' ') ?> FCFA
                </div>
                <small class="text-muted">
                    Frais: <?= number_format($transfers_fees ?? 0, 0, ',', ' ') ?> FCFA
                </small>
            </div>
        </div>
    </div>

    <!-- Bilan Frais -->
    <div class="col-md-4 col-lg-3 mb-3">
        <div class="card bg-light">
            <div class="card-body">
                <h6 class="card-title">
                    <i class="fas fa-balance-scale"></i> Bilan
                </h6>
                <div class="small mb-2">
                    <strong>Retraits:</strong>
                    <span class="text-danger">
                        -<?= number_format($withdrawals_fees ?? 0, 0, ',', ' ') ?> FCFA
                    </span>
                </div>
                <div class="small mb-2">
                    <strong>Transferts:</strong>
                    <span class="text-danger">
                        -<?= number_format($transfers_fees ?? 0, 0, ',', ' ') ?> FCFA
                    </span>
                </div>
                <hr>
                <div class="h6 text-success mb-0">
                    <i class="fas fa-profit-loss"></i>
                    Revenu: <?= number_format($total_fees ?? 0, 0, ',', ' ') ?> FCFA
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Panneau de gestion rapide -->
<div class="row mb-4">
    <div class="col-md-12">
        <h5 class="mb-3">
            <i class="fas fa-sliders-h"></i> Gestion rapide
        </h5>
    </div>

    <div class="col-md-6 col-lg-3 mb-3">
        <a href="<?= base_url('/admin/prefixes') ?>" class="btn btn-outline-primary btn-lg w-100 py-4">
            <i class="fas fa-list fa-2x d-block mb-2"></i>
            <span>Gérer les préfixes</span>
            <small class="d-block mt-2">
                <?= $total_prefixes ?? 0 ?> préfixes
            </small>
        </a>
    </div>

    <div class="col-md-6 col-lg-3 mb-3">
        <a href="<?= base_url('/admin/fees') ?>" class="btn btn-outline-success btn-lg w-100 py-4">
            <i class="fas fa-percentage fa-2x d-block mb-2"></i>
            <span>Configurer les frais</span>
            <small class="d-block mt-2">
                <?= $total_fee_rules ?? 0 ?> barèmes
            </small>
        </a>
    </div>

    <div class="col-md-6 col-lg-3 mb-3">
        <a href="<?= base_url('/admin/statistics') ?>" class="btn btn-outline-info btn-lg w-100 py-4">
            <i class="fas fa-chart-line fa-2x d-block mb-2"></i>
            <span>Voir les statistiques</span>
            <small class="d-block mt-2">
                Analytiques détaillées
            </small>
        </a>
    </div>

    <div class="col-md-6 col-lg-3 mb-3">
        <a href="<?= base_url('/admin/dashboard') ?>" class="btn btn-outline-warning btn-lg w-100 py-4">
            <i class="fas fa-refresh fa-2x d-block mb-2"></i>
            <span>Actualiser</span>
            <small class="d-block mt-2">
                Dernière mise à jour
            </small>
        </a>
    </div>
</div>

<!-- Avertissements et informations -->
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-info" role="alert">
            <i class="fas fa-info-circle"></i>
            <strong>Information:</strong> 
            Les statistiques sont mises à jour en temps réel. 
            Les modifications aux préfixes et frais sont appliquées immédiatement.
        </div>
    </div>
</div>

<!-- Style personnalisé pour les cartes KPI -->
<style>
    .border-left-primary {
        border-left: 4px solid #007bff !important;
    }
    .border-left-success {
        border-left: 4px solid #28a745 !important;
    }
    .border-left-danger {
        border-left: 4px solid #dc3545 !important;
    }
    .border-left-warning {
        border-left: 4px solid #ffc107 !important;
    }
    .text-xs {
        font-size: 0.75rem;
    }
    .opacity-50 {
        opacity: 0.5;
    }
</style>

<?= $this->endSection() ?>
<?= $this->include('layouts/footer') ?>
