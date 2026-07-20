<?= $this->extend('layouts/header') ?>

<?= $this->section('content') ?>

<div class="row mb-4">
    <div class="col-md-12">
        <h1 class="mb-2">
            <i class="fas fa-history text-primary"></i> Historique des transactions
        </h1>
        <p class="text-muted">Consultez toutes vos transactions passées</p>
    </div>
</div>

<!-- Filtres et recherche -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card bg-light">
            <div class="card-body">
                <form action="<?= base_url('/history') ?>" method="get" class="row g-3">
                    <!-- Filtre par type -->
                    <div class="col-md-4">
                        <label for="type_filter" class="form-label">
                            <i class="fas fa-filter"></i> Filtrer par type
                        </label>
                        <select class="form-select" id="type_filter" name="type">
                            <option value="">-- Tous les types --</option>
                            <option value="depos" <?= isset($_GET['type']) && $_GET['type'] === 'depos' ? 'selected' : '' ?>>Dépôts</option>
                            <option value="retrait" <?= isset($_GET['type']) && $_GET['type'] === 'retrait' ? 'selected' : '' ?>>Retraits</option>
                            <option value="transfer" <?= isset($_GET['type']) && $_GET['type'] === 'transfer' ? 'selected' : '' ?>>Transferts</option>
                        </select>
                    </div>

                    <!-- Filtre par statut -->
                    <div class="col-md-4">
                        <label for="status_filter" class="form-label">
                            <i class="fas fa-check-circle"></i> Statut
                        </label>
                        <select class="form-select" id="status_filter" name="status">
                            <option value="">-- Tous les statuts --</option>
                            <option value="completed" <?= isset($_GET['status']) && $_GET['status'] === 'completed' ? 'selected' : '' ?>>Complétées</option>
                            <option value="failed" <?= isset($_GET['status']) && $_GET['status'] === 'failed' ? 'selected' : '' ?>>Échouées</option>
                        </select>
                    </div>

                    <!-- Bouton de filtrage -->
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Filtrer
                        </button>
                        <a href="<?= base_url('/history') ?>" class="btn btn-secondary ms-2">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Statistiques rapides -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h6 class="card-title text-muted">Total transactions</h6>
                <h3 class="card-text"><?= $total_transactions ?? 0 ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h6 class="card-title text-muted">Montant total</h6>
                <h3 class="card-text text-success">
                    <?= number_format($total_amount ?? 0, 0, ',', ' ') ?> Ar
                </h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h6 class="card-title text-muted">Frais payés</h6>
                <h3 class="card-text text-danger">
                    <?= number_format($total_fees ?? 0, 0, ',', ' ') ?> Ar
                </h3>
            </div>
        </div>
    </div>
</div>

<!-- Tableau des transactions -->
<div class="row">
    <div class="col-md-12">
        <?php if (!empty($transactions)): ?>
            <div class="card shadow">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th><i class="fas fa-calendar"></i> Date et heure</th>
                                <th><i class="fas fa-tag"></i> Type</th>
                                <th><i class="fas fa-money-bill"></i> Montant</th>
                                <th><i class="fas fa-percentage"></i> Frais</th>
                                <th><i class="fas fa-check-circle"></i> Statut</th>
                                <th><i class="fas fa-info-circle"></i> Détails</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transactions as $transaction): ?>
                                <tr>
                                    <!-- Date et heure -->
                                    <td>
                                        <small>
                                            <i class="fas fa-calendar-alt text-muted"></i>
                                            <?= date('d/m/Y H:i:s', strtotime($transaction['created_at'] ?? '')) ?>
                                        </small>
                                    </td>

                                    <!-- Type d'opération -->
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

                                    <!-- Montant -->
                                    <td>
                                        <strong class="text-primary">
                                            <?= number_format($transaction['amount'] ?? 0, 0, ',', ' ') ?> Ar
                                        </strong>
                                    </td>

                                    <!-- Frais -->
                                    <td>
                                        <?php if (($transaction['fee'] ?? 0) > 0): ?>
                                            <span class="text-danger">
                                                -<?= number_format($transaction['fee'] ?? 0, 0, ',', ' ') ?> Ar
                                            </span>
                                        <?php else: ?>
                                            <span class="text-success">Gratuit</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Statut -->
                                    <td>
                                        <?php
                                            $status = strtolower($transaction['status'] ?? 'completed');
                                            if ($status === 'completed') {
                                                $status_badge = '<span class="badge bg-success"><i class="fas fa-check-circle"></i> Complétée</span>';
                                            } else {
                                                $status_badge = '<span class="badge bg-danger"><i class="fas fa-times-circle"></i> Échouée</span>';
                                            }
                                            echo $status_badge;
                                        ?>
                                    </td>

                                    <!-- Détails -->
                                    <td>
                                        <button 
                                            class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#detailsModal<?= $transaction['id'] ?? '' ?>"
                                            title="Voir les détails"
                                        >
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Modal des détails -->
                                <div class="modal fade" id="detailsModal<?= $transaction['id'] ?? '' ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">
                                                    <i class="fas fa-file-alt"></i> Détails de la transaction
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row mb-3">
                                                    <div class="col-6">
                                                        <small class="text-muted">ID Transactions</small>
                                                        <p><strong><?= $transaction['id'] ?? '-' ?></strong></p>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted">Type</small>
                                                        <p><strong><?= $transaction['operation_type'] ?? '-' ?></strong></p>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-6">
                                                        <small class="text-muted">Montant</small>
                                                        <p><strong><?= number_format($transaction['amount'] ?? 0, 0, ',', ' ') ?> Ar</strong></p>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted">Frais</small>
                                                        <p><strong><?= number_format($transaction['fee'] ?? 0, 0, ',', ' ') ?> Ar</strong></p>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <small class="text-muted">Date</small>
                                                    <p><strong><?= date('d/m/Y H:i:s', strtotime($transaction['created_at'] ?? '')) ?></strong></p>
                                                </div>
                                                <div class="mb-3">
                                                    <small class="text-muted">Statut</small>
                                                    <p>
                                                        <?php
                                                            $status = strtolower($transaction['status'] ?? 'completed');
                                                            if ($status === 'completed') {
                                                                echo '<span class="badge bg-success">Complétée</span>';
                                                            } else {
                                                                echo '<span class="badge bg-danger">Échouée</span>';
                                                            }
                                                        ?>
                                                    </p>
                                                </div>
                                                <?php if (!empty($transaction['receiver_phone'])): ?>
                                                    <div class="mb-3">
                                                        <small class="text-muted">Destinataire</small>
                                                        <p><strong><?= $transaction['receiver_phone'] ?? '-' ?></strong></p>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    Fermer
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination (optionnel) -->
            <?php if (isset($pager) && !empty($pager)): ?>
                <div class="mt-4">
                    <?= $pager->links() ?>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>Aucune transaction</strong><br>
                Vous n'avez pas effectué de transaction pour le moment. 
                <a href="<?= base_url('/dashboard') ?>" class="alert-link">Retour au tableau de bord</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Actions supplémentaires -->
<div class="row mt-4">
    <div class="col-md-12">
        <a href="<?= base_url('/dashboard') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour au tableau de bord
        </a>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->include('layouts/footer') ?>
