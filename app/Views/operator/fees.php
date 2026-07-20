<?= $this->extend('layouts/header') ?>

<?= $this->section('content') ?>

<div class="row mb-4">
    <div class="col-md-12">
        <h1 class="mb-2">
            <i class="fas fa-percentage text-success"></i> Configuration des barèmes de frais
        </h1>
        <p class="text-muted">Gérez les frais appliqués aux retraits et transferts</p>
    </div>
</div>

<!-- Avertissement important -->
<div class="alert alert-warning" role="alert">
    <i class="fas fa-exclamation-triangle"></i>
    <strong>Important!</strong> Les modifications apportées ici s'appliqueront immédiatement à toutes les nouvelles transactions.
</div>

<!-- Tabs pour séparer Retrait et Transfert -->
<ul class="nav nav-tabs mb-4" role="tablist">
    <li class="nav-item" role="presentation">
        <button 
            class="nav-link active" 
            id="retrait-tab" 
            data-bs-toggle="tab" 
            data-bs-target="#retrait-content" 
            type="button" 
            role="tab"
        >
            <i class="fas fa-arrow-up"></i> Frais de Retrait
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button 
            class="nav-link" 
            id="transfert-tab" 
            data-bs-toggle="tab" 
            data-bs-target="#transfert-content" 
            type="button" 
            role="tab"
        >
            <i class="fas fa-exchange-alt"></i> Frais de Transfert
        </button>
    </li>
</ul>

<div class="tab-content">
    <!-- TAB 1: RETRAITS -->
    <div class="tab-pane fade show active" id="retrait-content" role="tabpanel">
        <div class="row mb-4">
            <div class="col-md-12">
                <h5 class="mb-3">
                    <i class="fas fa-arrow-up"></i> Barème des frais de retrait
                </h5>

                <?php if (!empty($withdrawal_fees)): ?>
                    <div class="card shadow">
                        <div class="table-responsive">
                            <table class="table table-hover table-sm mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 10%;">ID</th>
                                        <th style="width: 20%;">Montant min</th>
                                        <th style="width: 20%;">Montant max</th>
                                        <th style="width: 20%;">Frais actuels</th>
                                        <th style="width: 20%;">Nouveau frais</th>
                                        <th style="width: 10%;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($withdrawal_fees as $fee): ?>
                                        <tr>
                                            <td>
                                                <small class="text-muted"><?= $fee['id'] ?? '-' ?></small>
                                            </td>
                                            <td>
                                                <strong><?= number_format($fee['min_amount'] ?? 0, 0, ',', ' ') ?> FCFA</strong>
                                            </td>
                                            <td>
                                                <strong><?= number_format($fee['max_amount'] ?? 0, 0, ',', ' ') ?> FCFA</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <?= number_format($fee['fee'] ?? 0, 0, ',', ' ') ?> FCFA
                                                </span>
                                            </td>
                                            <!-- Formulaire inline de modification -->
                                            <td>
                                                <form 
                                                    action="<?= base_url('/admin/fees/update/' . ($fee['id'] ?? '')) ?>" 
                                                    method="post"
                                                    class="d-flex gap-2"
                                                    style="display: inline-flex !important;"
                                                >
                                                    <?= csrf_field() ?>
                                                    <input 
                                                        type="hidden"
                                                        name="operation_type"
                                                        value="retrait"
                                                    >
                                                    <input 
                                                        type="number" 
                                                        name="fee" 
                                                        value="<?= $fee['fee'] ?? 0 ?>"
                                                        class="form-control form-control-sm"
                                                        style="width: 100px;"
                                                        min="0"
                                                        required
                                                    >
                                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Mettre à jour">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            </td>
                                            <td>
                                                <button 
                                                    class="btn btn-sm btn-outline-info"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#detailsModal<?= $fee['id'] ?? '' ?>"
                                                    title="Détails"
                                                >
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Modal pour détails -->
                                        <div class="modal fade" id="detailsModal<?= $fee['id'] ?? '' ?>" tabindex="-1">
                                            <div class="modal-dialog modal-sm">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Détails du barème</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>
                                                            <strong>Plage:</strong>
                                                            <?= number_format($fee['min_amount'] ?? 0, 0, ',', ' ') ?> - 
                                                            <?= number_format($fee['max_amount'] ?? 0, 0, ',', ' ') ?> FCFA
                                                        </p>
                                                        <p>
                                                            <strong>Frais:</strong>
                                                            <?= number_format($fee['fee'] ?? 0, 0, ',', ' ') ?> FCFA
                                                        </p>
                                                        <p>
                                                            <strong>Pourcentage:</strong>
                                                            <?php
                                                                $percentage = ($fee['fee'] ?? 0) / ($fee['min_amount'] ?? 1) * 100;
                                                                echo number_format($percentage, 2) . '%';
                                                            ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Statistiques des retraits -->
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="card bg-light text-center">
                                <div class="card-body">
                                    <h6 class="card-title text-muted">Frais minimum</h6>
                                    <h5 class="text-warning">
                                        <?php
                                            $min_fee = min(array_column($withdrawal_fees, 'fee'));
                                            echo number_format($min_fee, 0, ',', ' ') . ' FCFA';
                                        ?>
                                    </h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light text-center">
                                <div class="card-body">
                                    <h6 class="card-title text-muted">Frais maximum</h6>
                                    <h5 class="text-danger">
                                        <?php
                                            $max_fee = max(array_column($withdrawal_fees, 'fee'));
                                            echo number_format($max_fee, 0, ',', ' ') . ' FCFA';
                                        ?>
                                    </h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light text-center">
                                <div class="card-body">
                                    <h6 class="card-title text-muted">Montants couverts</h6>
                                    <h5 class="text-info">
                                        <?php
                                            $min = min(array_column($withdrawal_fees, 'min_amount'));
                                            $max = max(array_column($withdrawal_fees, 'max_amount'));
                                            echo number_format($min, 0, ',', ' ') . ' - ' . number_format($max, 0, ',', ' ');
                                        ?>
                                        FCFA
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Aucun barème de retrait configuré
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- TAB 2: TRANSFERTS -->
    <div class="tab-pane fade" id="transfert-content" role="tabpanel">
        <div class="row mb-4">
            <div class="col-md-12">
                <h5 class="mb-3">
                    <i class="fas fa-exchange-alt"></i> Barème des frais de transfert
                </h5>

                <?php if (!empty($transfer_fees)): ?>
                    <div class="card shadow">
                        <div class="table-responsive">
                            <table class="table table-hover table-sm mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 10%;">ID</th>
                                        <th style="width: 20%;">Montant min</th>
                                        <th style="width: 20%;">Montant max</th>
                                        <th style="width: 20%;">Frais actuels</th>
                                        <th style="width: 20%;">Nouveau frais</th>
                                        <th style="width: 10%;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($transfer_fees as $fee): ?>
                                        <tr>
                                            <td>
                                                <small class="text-muted"><?= $fee['id'] ?? '-' ?></small>
                                            </td>
                                            <td>
                                                <strong><?= number_format($fee['min_amount'] ?? 0, 0, ',', ' ') ?> FCFA</strong>
                                            </td>
                                            <td>
                                                <strong><?= number_format($fee['max_amount'] ?? 0, 0, ',', ' ') ?> FCFA</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <?= number_format($fee['fee'] ?? 0, 0, ',', ' ') ?> FCFA
                                                </span>
                                            </td>
                                            <!-- Formulaire inline de modification -->
                                            <td>
                                                <form 
                                                    action="<?= base_url('/admin/fees/update/' . ($fee['id'] ?? '')) ?>" 
                                                    method="post"
                                                    class="d-flex gap-2"
                                                    style="display: inline-flex !important;"
                                                >
                                                    <?= csrf_field() ?>
                                                    <input 
                                                        type="hidden"
                                                        name="operation_type"
                                                        value="transfer"
                                                    >
                                                    <input 
                                                        type="number" 
                                                        name="fee" 
                                                        value="<?= $fee['fee'] ?? 0 ?>"
                                                        class="form-control form-control-sm"
                                                        style="width: 100px;"
                                                        min="0"
                                                        required
                                                    >
                                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Mettre à jour">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            </td>
                                            <td>
                                                <button 
                                                    class="btn btn-sm btn-outline-info"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#detailsTransferModal<?= $fee['id'] ?? '' ?>"
                                                    title="Détails"
                                                >
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Modal pour détails -->
                                        <div class="modal fade" id="detailsTransferModal<?= $fee['id'] ?? '' ?>" tabindex="-1">
                                            <div class="modal-dialog modal-sm">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Détails du barème</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>
                                                            <strong>Plage:</strong>
                                                            <?= number_format($fee['min_amount'] ?? 0, 0, ',', ' ') ?> - 
                                                            <?= number_format($fee['max_amount'] ?? 0, 0, ',', ' ') ?> FCFA
                                                        </p>
                                                        <p>
                                                            <strong>Frais:</strong>
                                                            <?= number_format($fee['fee'] ?? 0, 0, ',', ' ') ?> FCFA
                                                        </p>
                                                        <p>
                                                            <strong>Pourcentage:</strong>
                                                            <?php
                                                                $percentage = ($fee['fee'] ?? 0) / ($fee['min_amount'] ?? 1) * 100;
                                                                echo number_format($percentage, 2) . '%';
                                                            ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Statistiques des transferts -->
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="card bg-light text-center">
                                <div class="card-body">
                                    <h6 class="card-title text-muted">Frais minimum</h6>
                                    <h5 class="text-info">
                                        <?php
                                            $min_fee = min(array_column($transfer_fees, 'fee'));
                                            echo number_format($min_fee, 0, ',', ' ') . ' FCFA';
                                        ?>
                                    </h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light text-center">
                                <div class="card-body">
                                    <h6 class="card-title text-muted">Frais maximum</h6>
                                    <h5 class="text-danger">
                                        <?php
                                            $max_fee = max(array_column($transfer_fees, 'fee'));
                                            echo number_format($max_fee, 0, ',', ' ') . ' FCFA';
                                        ?>
                                    </h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light text-center">
                                <div class="card-body">
                                    <h6 class="card-title text-muted">Montants couverts</h6>
                                    <h5 class="text-success">
                                        <?php
                                            $min = min(array_column($transfer_fees, 'min_amount'));
                                            $max = max(array_column($transfer_fees, 'max_amount'));
                                            echo number_format($min, 0, ',', ' ') . ' - ' . number_format($max, 0, ',', ' ');
                                        ?>
                                        FCFA
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Aucun barème de transfert configuré
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Comparaison des deux types -->
<div class="row mt-5 mb-4">
    <div class="col-md-12">
        <h5 class="mb-3">
            <i class="fas fa-compress"></i> Comparaison retrait vs transfert
        </h5>
        <div class="card">
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Montant</th>
                            <th class="text-warning">Frais Retrait</th>
                            <th class="text-info">Frais Transfert</th>
                            <th>Différence</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            if (!empty($withdrawal_fees) && !empty($transfer_fees)) {
                                // Créer des comparaisons
                                $samples = [100, 1000, 5000, 10000, 25000, 50000, 100000, 250000, 500000, 1000000];
                                
                                foreach ($samples as $amount) {
                                    $withdrawal_fee = 0;
                                    $transfer_fee = 0;
                                    
                                    // Trouver les frais pour ce montant
                                    foreach ($withdrawal_fees as $fee) {
                                        if ($amount >= $fee['min_amount'] && $amount <= $fee['max_amount']) {
                                            $withdrawal_fee = $fee['fee'];
                                            break;
                                        }
                                    }
                                    
                                    foreach ($transfer_fees as $fee) {
                                        if ($amount >= $fee['min_amount'] && $amount <= $fee['max_amount']) {
                                            $transfer_fee = $fee['fee'];
                                            break;
                                        }
                                    }
                                    
                                    $diff = $withdrawal_fee - $transfer_fee;
                                    $diff_class = $diff === 0 ? 'text-success' : ($diff > 0 ? 'text-danger' : 'text-success');
                        ?>
                        <tr>
                            <td><strong><?= number_format($amount, 0, ',', ' ') ?> FCFA</strong></td>
                            <td class="text-warning"><?= number_format($withdrawal_fee, 0, ',', ' ') ?> FCFA</td>
                            <td class="text-info"><?= number_format($transfer_fee, 0, ',', ' ') ?> FCFA</td>
                            <td class="<?= $diff_class ?>">
                                <?php if ($diff === 0) echo 'Identique';
                                elseif ($diff > 0) echo '+' . number_format($diff, 0, ',', ' ') . ' FCFA';
                                else echo number_format($diff, 0, ',', ' ') . ' FCFA';
                                ?>
                            </td>
                        </tr>
                        <?php } } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Informations et conseils -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card bg-light">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-lightbulb"></i> Conseils de configuration
                </h6>
            </div>
            <div class="card-body small">
                <ul class="list-unstyled mb-0">
                    <li><i class="fas fa-check-circle text-success"></i> Vérifiez avant de modifier</li>
                    <li><i class="fas fa-check-circle text-success"></i> Les changements sont immédiats</li>
                    <li><i class="fas fa-check-circle text-success"></i> Informez les utilisateurs des modifications</li>
                    <li><i class="fas fa-check-circle text-success"></i> Gardez les frais compétitifs</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card bg-light">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle"></i> À propos des frais
                </h6>
            </div>
            <div class="card-body small">
                <p>
                    Les frais sont appliqués automatiquement selon le montant de la transaction.
                    L'utilisateur peut voir les frais avant de confirmer l'opération.
                </p>
                <p class="mb-0">
                    Les dépôts ne sont jamais soumis à des frais pour encourager les utilisateurs.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Retour au tableau de bord -->
<div class="row mt-4">
    <div class="col-md-12">
        <a href="<?= base_url('/admin/dashboard') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour au tableau de bord
        </a>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->include('layouts/footer') ?>
