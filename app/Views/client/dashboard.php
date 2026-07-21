<?= $this->extend('layouts/header') ?>

<?= $this->section('content') ?>

<div class="row mb-4">
    <div class="col-md-12">
        <h1 class="mb-4">
            <i class="fas fa-chart-line text-primary"></i> Tableau de bord Client
        </h1>
    </div>
</div>

<!-- Affichage du solde -->
<div class="row mb-4">
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="card-text mb-0">Votre solde</p>
                        <h3 class="card-title mt-2">
                            <?= number_format($balance ?? 0, 0, ',', ' ') ?> Ar
                        </h3>
                    </div>
                    <div>
                        <i class="fas fa-wallet" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card text-white bg-dark">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="card-text mb-0">Epargne cumulée</p>
                        <h3 class="card-title mt-2">
                            <?= number_format((int) ($user['savings_balance'] ?? 0), 0, ',', ' ') ?> Ar
                        </h3>
                    </div>
                    <div>
                        <i class="fas fa-piggy-bank" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="card-text mb-0">Transactions</p>
                        <h3 class="card-title mt-2"><?= $total_transactions ?? 0 ?></h3>
                    </div>
                    <div>
                        <i class="fas fa-exchange-alt" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="card-text mb-0">Frais totaux</p>
                        <h3 class="card-title mt-2"><?= number_format($total_fees ?? 0, 0, ',', ' ') ?> Ar</h3>
                    </div>
                    <div>
                        <i class="fas fa-money-bill-wave" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-light border d-flex flex-wrap justify-content-between align-items-center mb-0">
            <div>
                <strong>Préférence d'épargne active :</strong>
                <?= number_format((float) ($user['savings_percent'] ?? 0), 0, ',', ' ') ?> %
            </div>
            <a href="<?= base_url('/savings') ?>" class="btn btn-outline-dark btn-sm mt-2 mt-md-0">
                <i class="fas fa-piggy-bank"></i> Modifier l'épargne
            </a>
        </div>
    </div>
</div>

<!-- Boutons d'action -->
<div class="row mb-5">
    <div class="col-12">
        <h5 class="mb-3">
            <i class="fas fa-th-large"></i> Actions rapides
        </h5>
    </div>
    <div class="col-md-6 col-lg-3 mb-3">
        <a href="<?= base_url('/deposit') ?>" class="btn btn-success btn-lg w-100 py-4">
            <i class="fas fa-arrow-down fa-2x d-block mb-2"></i>
            <span>Dépôt</span>
        </a>
    </div>
    <div class="col-md-6 col-lg-3 mb-3">
        <a href="<?= base_url('/withdraw') ?>" class="btn btn-warning btn-lg w-100 py-4">
            <i class="fas fa-arrow-up fa-2x d-block mb-2"></i>
            <span>Retrait</span>
        </a>
    </div>
    <div class="col-md-6 col-lg-3 mb-3">
        <a href="<?= base_url('/transfer') ?>" class="btn btn-info btn-lg w-100 py-4">
            <i class="fas fa-exchange-alt fa-2x d-block mb-2"></i>
            <span>Transfert</span>
        </a>
    </div>
    <div class="col-md-6 col-lg-3 mb-3">
        <a href="<?= base_url('/history') ?>" class="btn btn-primary btn-lg w-100 py-4">
            <i class="fas fa-history fa-2x d-block mb-2"></i>
            <span>Historique</span>
        </a>
    </div>
    <div class="col-md-6 col-lg-3 mb-3">
        <a href="<?= base_url('/savings') ?>" class="btn btn-dark btn-lg w-100 py-4">
            <i class="fas fa-piggy-bank fa-2x d-block mb-2"></i>
            <span>Epargne</span>
        </a>
    </div>
</div>

<!-- Récapitulatif des dernières transactions -->
<div class="row">
    <div class="col-md-12">
        <h5 class="mb-3">
            <i class="fas fa-list"></i> Dernières transactions
        </h5>
        
        <?php if (!empty($recent_transactions)): ?>
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead class="table-light">
                        <tr>
                            <th><i class="fas fa-calendar"></i> Date</th>
                            <th><i class="fas fa-tag"></i> Type</th>
                            <th><i class="fas fa-money-bill"></i> Montant</th>
                            <th><i class="fas fa-percentage"></i> Frais</th>
                            <th><i class="fas fa-check-circle"></i> Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_transactions as $transaction): ?>
                            <tr>
                                <td>
                                    <small><?= date('d/m/Y H:i', strtotime($transaction['created_at'] ?? '')) ?></small>
                                </td>
                                <td>
                                    <?php 
                                        $type = $transaction['operation_type'] ?? 'Inconnue';
                                        $badge_class = '';
                                        $icon = '';
                                        
                                        if (strpos($type, 'Dépôt') !== false) {
                                            $badge_class = 'bg-success';
                                            $icon = 'fa-arrow-down';
                                        } elseif (strpos($type, 'Retrait') !== false) {
                                            $badge_class = 'bg-warning';
                                            $icon = 'fa-arrow-up';
                                        } elseif (strpos($type, 'Transfert') !== false) {
                                            $badge_class = 'bg-info';
                                            $icon = 'fa-exchange-alt';
                                        }
                                    ?>
                                    <span class="badge <?= $badge_class ?>">
                                        <i class="fas <?= $icon ?>"></i> <?= $type ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><?= number_format($transaction['amount'] ?? 0, 0, ',', ' ') ?> Ar</strong>
                                </td>
                                <td>
                                    <?= number_format($transaction['fee'] ?? 0, 0, ',', ' ') ?> Ar
                                </td>
                                <td>
                                    <?php
                                        $status = strtolower($transaction['status'] ?? 'completed');
                                        $status_badge = $status === 'completed' 
                                            ? '<span class="badge bg-success">Complétée</span>'
                                            : '<span class="badge bg-danger">Échouée</span>';
                                        echo $status_badge;
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Aucune transaction disponible. Commencez par effectuer une opération!
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->include('layouts/footer') ?>
