<?= $this->extend('layouts/header') ?>

<?= $this->section('content') ?>

<div class="row mb-4">
    <div class="col-md-12">
        <h1 class="mb-2">
            <i class="fas fa-percentage text-success"></i> Configuration des barèmes de frais
        </h1>
        <p class="text-muted">Barèmes séparés pour le retrait et le transfert, modifiables directement en ligne.</p>
    </div>
</div>

<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle"></i>
    Les modifications prennent effet immédiatement sur les nouvelles transactions.
</div>

<ul class="nav nav-tabs mb-4" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#withdrawals" type="button" role="tab">
            <i class="fas fa-arrow-up"></i> Retraits
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#transfers" type="button" role="tab">
            <i class="fas fa-exchange-alt"></i> Transferts
        </button>
    </li>
</ul>

<div class="tab-content">
    <div class="tab-pane fade show active" id="withdrawals" role="tabpanel">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h5 class="mb-0">Barème des frais de retrait</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Plage</th>
                            <th>Frais actuels</th>
                            <th>Modifier</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($withdrawal_fees)): ?>
                            <?php foreach ($withdrawal_fees as $fee): ?>
                                <tr>
                                    <td>
                                        <strong><?= number_format($fee['min_amount'] ?? 0, 0, ',', ' ') ?> Ar</strong>
                                        <span class="text-muted"> - <?= number_format($fee['max_amount'] ?? 0, 0, ',', ' ') ?> Ar</span>
                                    </td>
                                    <td><span class="badge bg-warning text-dark"><?= number_format($fee['fee'] ?? 0, 0, ',', ' ') ?> Ar</span></td>
                                    <td>
                                        <form action="<?= base_url('/admin/fees/update/' . ($fee['id'] ?? '')) ?>" method="post" class="d-flex gap-2">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="operation_type" value="retrait">
                                            <input type="number" name="fee" value="<?= $fee['fee'] ?? 0 ?>" min="0" class="form-control form-control-sm" style="max-width: 120px;" required>
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="fas fa-save"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" class="text-center text-muted py-4">Aucun barème de retrait configuré.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="transfers" role="tabpanel">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h5 class="mb-0">Barème des frais de transfert</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Plage</th>
                            <th>Frais actuels</th>
                            <th>Modifier</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($transfer_fees)): ?>
                            <?php foreach ($transfer_fees as $fee): ?>
                                <tr>
                                    <td>
                                        <strong><?= number_format($fee['min_amount'] ?? 0, 0, ',', ' ') ?> Ar</strong>
                                        <span class="text-muted"> - <?= number_format($fee['max_amount'] ?? 0, 0, ',', ' ') ?> Ar</span>
                                    </td>
                                    <td><span class="badge bg-info"><?= number_format($fee['fee'] ?? 0, 0, ',', ' ') ?> Ar</span></td>
                                    <td>
                                        <form action="<?= base_url('/admin/fees/update/' . ($fee['id'] ?? '')) ?>" method="post" class="d-flex gap-2">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="operation_type" value="transfer">
                                            <input type="number" name="fee" value="<?= $fee['fee'] ?? 0 ?>" min="0" class="form-control form-control-sm" style="max-width: 120px;" required>
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="fas fa-save"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" class="text-center text-muted py-4">Aucun barème de transfert configuré.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4 g-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted mb-2">Retrait</h6>
                <div class="h5 mb-0 text-warning"><?= !empty($withdrawal_fees) ? count($withdrawal_fees) : 0 ?> barèmes</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted mb-2">Transfert</h6>
                <div class="h5 mb-0 text-info"><?= !empty($transfer_fees) ? count($transfer_fees) : 0 ?> barèmes</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted mb-2">Action</h6>
                <a href="<?= base_url('/admin/dashboard') ?>" class="btn btn-outline-secondary w-100">Retour au tableau de bord</a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->include('layouts/footer') ?>
