<?= $this->extend('layouts/header') ?>

<?= $this->section('content') ?>

<div class="row mb-4">
    <div class="col-md-12">
        <h1 class="mb-2">
            <i class="fas fa-arrow-up text-warning"></i> Effectuer un retrait
        </h1>
        <p class="text-muted">Retirez de l'argent de votre compte Mobile Money</p>
    </div>
</div>

<div class="row">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow">
            <div class="card-body p-4">
                <form action="<?= base_url('/withdraw') ?>" method="post" class="needs-validation" novalidate>
                    <?= csrf_field() ?>

                    <div class="alert alert-info mb-4">
                        <i class="fas fa-wallet"></i>
                        <strong>Solde disponible:</strong>
                        <?= number_format($balance ?? 0, 0, ',', ' ') ?> Ar
                    </div>

                    <div class="mb-4">
                        <label for="amount" class="form-label">
                            <i class="fas fa-money-bill"></i> Montant a retirer (Ar)
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-currency-circle"></i></span>
                            <input type="number" class="form-control" id="amount" name="amount" placeholder="Entrez le montant" min="100" max="<?= $balance ?? 0 ?>" required>
                        </div>
                        <small class="form-text text-muted d-block mt-2">
                            <i class="fas fa-info-circle"></i>
                            Montant minimum: 100 Ar | Maximum: <?= number_format($balance ?? 0, 0, ',', ' ') ?> Ar
                        </small>
                        <div class="invalid-feedback">Veuillez entrer un montant valide</div>
                    </div>

                    <div class="mb-4">
                        <label for="beneficiary_type" class="form-label">
                            <i class="fas fa-user-check"></i> Type de beneficiaire
                        </label>
                        <select class="form-select" id="beneficiary_type" name="beneficiary_type" required>
                            <option value="">-- Selectionnez un type --</option>
                            <option value="agent">Agent de retrait</option>
                            <option value="bank">Compte bancaire</option>
                            <option value="airtime">Recharge telephonique</option>
                        </select>
                        <div class="invalid-feedback">Veuillez selectionner un type de beneficiaire</div>
                    </div>

                    <div class="mb-4">
                        <label for="beneficiary" class="form-label">
                            <i class="fas fa-phone"></i> Numero/Compte du beneficiaire
                        </label>
                        <input type="text" class="form-control" id="beneficiary" name="beneficiary" placeholder="Numero du beneficiaire" required>
                        <div class="invalid-feedback">Veuillez entrer un numero valide</div>
                    </div>

                    <div class="alert alert-light border mb-4">
                        <h6 class="mb-3"><i class="fas fa-file-alt"></i> Recapitulatif</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Montant demande:</span>
                            <strong id="amount_display">0 Ar</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Frais de retrait:</span>
                            <strong id="fee_display" class="text-danger">0 Ar</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Total debite:</strong>
                            <strong id="total_display" class="text-danger">0 Ar</strong>
                        </div>
                        <small class="text-muted d-block mt-2">
                            <i class="fas fa-info-circle"></i>
                            Les frais sont ajoutes au montant demande et debites du solde
                        </small>
                    </div>

                    <div id="fee_warning" class="alert alert-warning d-none mb-4">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Attention!</strong> Des frais s'appliquent a ce retrait.
                    </div>

                    <div class="d-grid gap-2 mb-3">
                        <button type="submit" class="btn btn-warning btn-lg">
                            <i class="fas fa-check-circle"></i> Confirmer le retrait
                        </button>
                    </div>
                    <a href="<?= base_url('/dashboard') ?>" class="btn btn-secondary w-100">
                        <i class="fas fa-arrow-left"></i> Retour au tableau de bord
                    </a>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-lg-6">
        <div class="card bg-light mb-3">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0"><i class="fas fa-percentage"></i> Bareme des frais</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-borderless">
                        <thead class="table-light">
                            <tr><th>Montant</th><th>Frais</th></tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($withdrawal_fees)): ?>
                                <?php foreach ($withdrawal_fees as $fee): ?>
                                    <tr>
                                        <td>
                                            <?= number_format($fee['min_amount'] ?? 0, 0, ',', ' ') ?> - <?= number_format($fee['max_amount'] ?? 0, 0, ',', ' ') ?> Ar
                                        </td>
                                        <td><strong><?= number_format($fee['fee'] ?? 0, 0, ',', ' ') ?> Ar</strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="2" class="text-center text-muted">Aucun bareme de retrait configure.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card bg-info text-white">
            <div class="card-body">
                <h6 class="card-title"><i class="fas fa-phone"></i> Support client</h6>
                <p class="small mb-0">Pour toute question sur les frais ou le processus de retrait, contactez-nous au <strong>+237 XXX XXX XXX</strong></p>
            </div>
        </div>
    </div>
</div>

<script>
    const fees = <?= json_encode(array_values($withdrawal_fees ?? []), JSON_UNESCAPED_UNICODE) ?>;

    function getFee(amount) {
        for (const rule of fees) {
            if (amount >= Number(rule.min_amount) && amount <= Number(rule.max_amount)) {
                return Number(rule.fee);
            }
        }
        return 0;
    }

    const amountInput = document.getElementById('amount');
    const amountDisplay = document.getElementById('amount_display');
    const feeDisplay = document.getElementById('fee_display');
    const totalDisplay = document.getElementById('total_display');
    const feeWarning = document.getElementById('fee_warning');

    amountInput.addEventListener('input', function() {
        const amount = parseInt(this.value, 10) || 0;
        const fee = getFee(amount);
        const total = amount + fee;
        const formatter = new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'MGA',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        });

        amountDisplay.textContent = formatter.format(amount).replace('MGA', 'Ar');
        feeDisplay.textContent = formatter.format(fee).replace('MGA', 'Ar');
        totalDisplay.textContent = formatter.format(total).replace('MGA', 'Ar');
        feeWarning.classList.toggle('d-none', fee === 0);
    });
</script>

<?= $this->endSection() ?>
<?= $this->include('layouts/footer') ?>
