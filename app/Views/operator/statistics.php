<?= $this->extend('layouts/header') ?>

<?= $this->section('content') ?>

<div class="row mb-4">
    <div class="col-md-12">
        <h1 class="mb-2"><i class="fas fa-chart-line text-primary"></i> Statistiques operateur</h1>
        <p class="text-muted">Vue de synthese des gains internes, commissions externes et montants a regler.</p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="text-muted">Gains internes</div>
                <div class="h4 mb-0"><?= number_format((int) ($withdrawal_fees ?? 0) + (int) ($internal_transfer_fees ?? 0), 0, ',', ' ') ?> Ar</div>
                <small class="text-muted">Retraits + transferts internes</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="text-muted">Transferts externes</div>
                <div class="h4 mb-0"><?= number_format((int) ($external_transfer_base_fees ?? 0), 0, ',', ' ') ?> Ar</div>
                <small class="text-muted"><?= esc((string) ($external_transfers_count ?? 0)) ?> transferts</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="text-muted">Commissions externes</div>
                <div class="h4 mb-0"><?= number_format((int) ($external_commissions ?? 0), 0, ',', ' ') ?> Ar</div>
                <small class="text-muted">Total des commissions dues</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="text-muted">Transferts internes</div>
                <div class="h4 mb-0"><?= esc((string) ($internal_transfers_count ?? 0)) ?></div>
                <small class="text-muted">Nombre de transferts internes</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <strong>Commission par operateur</strong>
            </div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Operateur</th>
                            <th class="text-end">Commission</th>
                            <th class="text-end">Total a regler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($amounts_by_operator)): ?>
                            <?php foreach ($amounts_by_operator as $row): ?>
                                <tr>
                                    <td><?= esc($row['operator_name'] ?? '-') ?></td>
                                    <td class="text-end"><?= number_format((int) ($row['total_commission'] ?? 0), 0, ',', ' ') ?> Ar</td>
                                    <td class="text-end"><?= number_format((int) ($row['total_to_settle'] ?? 0), 0, ',', ' ') ?> Ar</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" class="text-center text-muted py-4">Aucune commission externe.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <strong>Situation inter-operateurs</strong>
            </div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Operateur</th>
                            <th class="text-end">Montants transferes</th>
                            <th class="text-end">Commissions</th>
                            <th class="text-end">Total a regler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($settlement_by_operator)): ?>
                            <?php foreach ($settlement_by_operator as $row): ?>
                                <tr>
                                    <td><?= esc($row['operator_name'] ?? '-') ?></td>
                                    <td class="text-end"><?= number_format((int) ($row['total_amount'] ?? 0), 0, ',', ' ') ?> Ar</td>
                                    <td class="text-end"><?= number_format((int) ($row['total_commission'] ?? 0), 0, ',', ' ') ?> Ar</td>
                                    <td class="text-end fw-bold"><?= number_format((int) ($row['total_to_settle'] ?? 0), 0, ',', ' ') ?> Ar</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center text-muted py-4">Aucune situation inter-operateur.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <strong>Resume des frais</strong>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted">Frais de retrait</div>
                            <div class="h5 mb-0"><?= number_format((int) ($withdrawal_fees ?? 0), 0, ',', ' ') ?> Ar</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted">Frais internes de transfert</div>
                            <div class="h5 mb-0"><?= number_format((int) ($internal_transfer_fees ?? 0), 0, ',', ' ') ?> Ar</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted">Frais externes collectes</div>
                            <div class="h5 mb-0"><?= number_format((int) ($external_transfer_base_fees ?? 0), 0, ',', ' ') ?> Ar</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="<?= base_url('/admin/dashboard') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
</div>

<?= $this->endSection() ?>
<?= $this->include('layouts/footer') ?>
