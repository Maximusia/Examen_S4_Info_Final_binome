<?= $this->extend('layouts/header') ?>

<?= $this->section('content') ?>

<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm operator-hero">
            <div class="card-body p-4 p-lg-5 text-white">
                <span class="badge bg-light text-dark mb-3">Espace opérateur</span>
                <h1 class="display-6 mb-3">Pilotage Mobile Money</h1>
                <p class="mb-0 text-white-50">
                    Gestion des préfixes, des barèmes de frais et des indicateurs de performance de la plateforme.
                </p>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-4 bg-light">
                <h5 class="mb-3"><i class="fas fa-bullseye text-primary"></i> Vue rapide</h5>
                <div class="d-flex justify-content-between mb-2"><span>Clients</span><strong><?= $total_users ?? 0 ?></strong></div>
                <div class="d-flex justify-content-between mb-2"><span>Transactions</span><strong><?= $total_transactions ?? 0 ?></strong></div>
                <div class="d-flex justify-content-between mb-2"><span>Préfixes</span><strong><?= $total_prefixes ?? 0 ?></strong></div>
                <div class="d-flex justify-content-between"><span>Barèmes</span><strong><?= $total_fee_rules ?? 0 ?></strong></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="card shadow-sm border-0 stat-card stat-blue h-100 text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="small text-uppercase opacity-75">Comptes clients</div>
                        <div class="display-6 fw-bold mb-0"><?= $total_users ?? 0 ?></div>
                    </div>
                    <i class="fas fa-users fa-2x"></i>
                </div>
                <div class="small mt-3 opacity-75">Situation des comptes enregistrés.</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card shadow-sm border-0 stat-card stat-green h-100 text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="small text-uppercase opacity-75">Transactions</div>
                        <div class="display-6 fw-bold mb-0"><?= $total_transactions ?? 0 ?></div>
                    </div>
                    <i class="fas fa-exchange-alt fa-2x"></i>
                </div>
                <div class="small mt-3 opacity-75">Dépôts, retraits et transferts.</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card shadow-sm border-0 stat-card stat-orange h-100 text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="small text-uppercase opacity-75">Gains</div>
                        <div class="display-6 fw-bold mb-0"><?= number_format($total_fees ?? 0, 0, ',', ' ') ?> Ar</div>
                    </div>
                    <i class="fas fa-coins fa-2x"></i>
                </div>
                <div class="small mt-3 opacity-75">Revenus liés aux frais.</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card shadow-sm border-0 stat-card stat-dark h-100 text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="small text-uppercase opacity-75">Barèmes actifs</div>
                        <div class="display-6 fw-bold mb-0"><?= $total_fee_rules ?? 0 ?></div>
                    </div>
                    <i class="fas fa-sliders-h fa-2x"></i>
                </div>
                <div class="small mt-3 opacity-75">Tranches configurées et modifiables.</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-7">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-0 pb-0">
                <h5 class="mb-0"><i class="fas fa-phone text-primary"></i> Préfixes autorisés</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">Les comptes clients sont validés uniquement si leur numéro commence par un préfixe enregistré.</p>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="p-3 rounded bg-primary-subtle">
                            <div class="small text-muted">Préfixes</div>
                            <div class="h4 mb-0"><?= $total_prefixes ?? 0 ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded bg-success-subtle">
                            <div class="small text-muted">Exemples</div>
                            <div class="h4 mb-0">033 / 037</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded bg-warning-subtle">
                            <a href="<?= base_url('/admin/prefixes') ?>" class="btn btn-warning w-100">Gérer les préfixes</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-0 pb-0">
                <h5 class="mb-0"><i class="fas fa-percentage text-success"></i> Barèmes de frais</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2"><span>Retraits</span><strong><?= $withdrawals_count ?? 0 ?></strong></div>
                <div class="d-flex justify-content-between mb-3"><span>Transferts</span><strong><?= $transfers_count ?? 0 ?></strong></div>
                <a href="<?= base_url('/admin/fees') ?>" class="btn btn-outline-success w-100">Modifier les barèmes</a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-0 pb-0">
                <h5 class="mb-0"><i class="fas fa-wallet text-danger"></i> Situation des comptes</h5>
            </div>
            <div class="card-body">
                <div class="row g-3 text-center">
                    <div class="col-6">
                        <div class="p-3 rounded bg-light">
                            <div class="small text-muted">Dépôts</div>
                            <div class="h4 mb-0 text-success"><?= number_format($deposits_volume ?? 0, 0, ',', ' ') ?> Ar</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded bg-light">
                            <div class="small text-muted">Retraits</div>
                            <div class="h4 mb-0 text-warning"><?= number_format($withdrawals_volume ?? 0, 0, ',', ' ') ?> Ar</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded bg-light">
                            <div class="small text-muted">Transferts</div>
                            <div class="h4 mb-0 text-info"><?= number_format($transfers_volume ?? 0, 0, ',', ' ') ?> Ar</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded bg-light">
                            <div class="small text-muted">Volume global</div>
                            <div class="h4 mb-0 text-primary"><?= number_format($total_volume ?? 0, 0, ',', ' ') ?> Ar</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-0 pb-0">
                <h5 class="mb-0"><i class="fas fa-bolt text-secondary"></i> Actions de gestion</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= base_url('/admin/prefixes') ?>" class="btn btn-outline-primary">Gérer les préfixes autorisés</a>
                    <a href="<?= base_url('/admin/fees') ?>" class="btn btn-outline-success">Modifier les frais de retrait et de transfert</a>
                    <a href="<?= base_url('/admin/dashboard') ?>" class="btn btn-outline-dark">Actualiser le tableau de bord</a>
                </div>
                <hr>
                <p class="mb-0 text-muted small">Cet espace est distinct du côté client: il sert au pilotage, aux gains et à la configuration des règles.</p>
            </div>
        </div>
    </div>
</div>

<style>
    .operator-hero { background: linear-gradient(135deg, #0f172a 0%, #1d4ed8 100%); }
    .stat-blue { background: linear-gradient(135deg, #2563eb, #1d4ed8); }
    .stat-green { background: linear-gradient(135deg, #059669, #047857); }
    .stat-orange { background: linear-gradient(135deg, #d97706, #b45309); }
    .stat-dark { background: linear-gradient(135deg, #334155, #0f172a); }
</style>

<?= $this->endSection() ?>
<?= $this->include('layouts/footer') ?>
