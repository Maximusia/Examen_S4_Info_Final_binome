<?= $this->extend('layouts/header') ?>

<?= $this->section('content') ?>

<div class="row mb-4">
    <div class="col-md-12">
        <h1 class="mb-2"><i class="fas fa-history text-primary"></i> Historique des transactions</h1>
        <p class="text-muted">Consultez vos depots, retraits et transferts simples ou multiples.</p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <div class="text-muted">Total transactions</div>
                <div class="h3 mb-0"><?= esc((string) ($total_transactions ?? 0)) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <div class="text-muted">Montant total</div>
                <div class="h3 mb-0 text-success"><?= number_format((int) ($total_amount ?? 0), 0, ',', ' ') ?> Ar</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <div class="text-muted">Frais payes</div>
                <div class="h3 mb-0 text-danger"><?= number_format((int) ($total_fees ?? 0), 0, ',', ' ') ?> Ar</div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Operation</th>
                    <th>Groupe</th>
                    <th>Destinataire</th>
                    <th>Operateur</th>
                    <th>Part</th>
                    <th>Promo %</th>
                    <th>Remise</th>
                    <th>Epargne %</th>
                    <th>Epargne</th>
                    <th>Frais transfert</th>
                    <th>Commission</th>
                    <th>Frais retrait</th>
                    <th>Total ligne</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($transactions)): ?>
                    <?php foreach ($transactions as $transaction): ?>
                        <?php
                            $operation = (string) ($transaction['operation_type'] ?? '-');
                            $isExternal = (int) ($transaction['is_external'] ?? 0) === 1;
                            $status = (string) ($transaction['status'] ?? 'completed');
                            $lineTotal = (int) ($transaction['amount'] ?? 0) + (int) ($transaction['included_withdrawal_fee'] ?? 0) + (int) ($transaction['fee'] ?? 0);
                            $promoPercent = (float) ($transaction['promo_percent'] ?? 0);
                            $promoAmount = (int) ($transaction['promo_amount'] ?? 0);
                            $savingsPercent = (int) ($transaction['savings_percent'] ?? 0);
                            $savingsAmount = (int) ($transaction['savings_amount'] ?? 0);
                        ?>
                        <tr>
                            <td><?= esc(date('d/m/Y H:i:s', strtotime((string) ($transaction['created_at'] ?? 'now')))) ?></td>
                            <td>
                                <span class="badge <?= $operation === 'Depot' ? 'bg-success' : ($operation === 'Retrait' ? 'bg-warning text-dark' : 'bg-info') ?>">
                                    <?= esc($operation) ?>
                                </span>
                            </td>
                            <td><code><?= esc((string) ($transaction['batch_reference'] ?? '-')) ?></code></td>
                            <td><?= esc((string) ($transaction['receiver_phone'] ?? '-')) ?></td>
                            <td>
                                <span class="badge <?= $isExternal ? 'bg-danger' : 'bg-secondary' ?>">
                                    <?= esc((string) ($transaction['receiver_operator_name'] ?? ($isExternal ? 'Externe' : 'Interne'))) ?>
                                </span>
                            </td>
                            <td><?= number_format((int) ($transaction['amount'] ?? 0), 0, ',', ' ') ?> Ar</td>
                            <td><?= rtrim(rtrim(number_format($promoPercent, 2, ',', ' '), '0'), ',') ?> %</td>
                            <td><?= number_format($promoAmount, 0, ',', ' ') ?> Ar</td>
                            <td><?= esc((string) $savingsPercent) ?> %</td>
                            <td><?= number_format($savingsAmount, 0, ',', ' ') ?> Ar</td>
                            <td><?= number_format((int) ($transaction['base_fee'] ?? 0), 0, ',', ' ') ?> Ar</td>
                            <td><?= number_format((int) ($transaction['external_commission'] ?? 0), 0, ',', ' ') ?> Ar</td>
                            <td><?= number_format((int) ($transaction['included_withdrawal_fee'] ?? 0), 0, ',', ' ') ?> Ar</td>
                            <td><?= number_format($lineTotal, 0, ',', ' ') ?> Ar</td>
                            <td>
                                <span class="badge <?= $status === 'completed' ? 'bg-success' : 'bg-danger' ?>">
                                    <?= esc(ucfirst($status)) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="15" class="text-center text-muted py-4">Aucune transaction trouvee.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    <a href="<?= base_url('/dashboard') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour au tableau de bord
    </a>
</div>

<?= $this->endSection() ?>
<?= $this->include('layouts/footer') ?>
