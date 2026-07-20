<?= $this->extend('layouts/header') ?>

<?= $this->section('content') ?>

<div class="row mb-4">
    <div class="col-md-12">
        <h1 class="mb-2">
            <i class="fas fa-exchange-alt text-info"></i> Effectuer un transfert
        </h1>
        <p class="text-muted">Transferez de l'argent a un autre utilisateur Mobile Money</p>
    </div>
</div>

<div class="row">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow">
            <div class="card-body p-4">
                <form action="<?= base_url('/transfer') ?>" method="post" class="needs-validation" novalidate>
                    <?= csrf_field() ?>

                    <div class="alert alert-info mb-4">
                        <i class="fas fa-wallet"></i>
                        <strong>Solde disponible:</strong>
                        <?= number_format($balance ?? 0, 0, ',', ' ') ?> Ar
                    </div>

                    <div class="mb-4">
                        <label for="receiver_phone" class="form-label">
                            <i class="fas fa-user-tie"></i> Numero du destinataire
                        </label>
                        <input type="tel" class="form-control" id="receiver_phone" name="receiver_phone" placeholder="Numero du destinataire (ex: 0331234567)" pattern="^03[3-7][0-9]{7}$" required>
                        <small class="form-text text-muted d-block mt-2"><i class="fas fa-info-circle"></i> Format accepte: 033XXXXXXX ou 037XXXXXXX</small>
                        <div class="invalid-feedback">Veuillez entrer un numero valide</div>
                    </div>

                    <div class="mb-4">
                        <label for="amount" class="form-label">
                            <i class="fas fa-money-bill"></i> Montant a transferer (Ar)
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-currency-circle"></i></span>
                            <input type="number" class="form-control" id="amount" name="amount" placeholder="Entrez le montant" min="100" max="<?= $balance ?? 0 ?>" required>
                        </div>
                        <small class="form-text text-muted d-block mt-2"><i class="fas fa-info-circle"></i> Montant minimum: 100 Ar | Maximum: <?= number_format($balance ?? 0, 0, ',', ' ') ?> Ar</small>
                        <div class="invalid-feedback">Veuillez entrer un montant valide</div>
                    </div>

                    <div class="mb-4">
                        <label for="transfer_reason" class="form-label">
                            <i class="fas fa-comment-dots"></i> Motif du transfert (optionnel)
                        </label>
                        <select class="form-select" id="transfer_reason" name="transfer_reason">
                            <option value="">-- Selectionnez un motif --</option>
                            <option value="remittance">Envoi d'argent</option>
                            <option value="payment">Paiement</option>
                            <option value="loan">Pret</option>
                            <option value="gift">Cadeau</option>
                            <option value="other">Autre</option>
                        </select>
                    </div>

                    <div class="alert alert-light border mb-4">
                        <h6 class="mb-3"><i class="fas fa-file-alt"></i> Recapitulatif</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Montant a transferer:</span>
                            <strong id="amount_display">0 Ar</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Frais de transfert:</span>
                            <strong id="fee_display" class="text-danger">0 Ar</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <strong>Total debite:</strong>
                            <strong id="total_display" class="text-danger">0 Ar</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <strong>Destinataire recevra:</strong>
                            <strong id="receiver_display" class="text-success">0 Ar</strong>
                        </div>
                    </div>

                    <div class="mb-4 form-check">
                        <input class="form-check-input" type="checkbox" id="confirm" name="confirm" required>
                        <label class="form-check-label" for="confirm">
                            <i class="fas fa-lock"></i>
                            Je confirme vouloir transferer ce montant au destinataire indique
                        </label>
                        <div class="invalid-feedback">Veuillez confirmer le transfert</div>
                    </div>

                    <div class="d-grid gap-2 mb-3">
                        <button type="submit" class="btn btn-info btn-lg">
                            <i class="fas fa-check-circle"></i> Confirmer le transfert
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
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-percentage"></i> Bareme des frais de transfert</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-borderless">
                        <thead class="table-light">
                            <tr><th>Montant</th><th>Frais</th></tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($transfer_fees)): ?>
                                <?php foreach ($transfer_fees as $fee): ?>
                                    <tr>
                                        <td>
                                            <?= number_format($fee['min_amount'] ?? 0, 0, ',', ' ') ?> - <?= number_format($fee['max_amount'] ?? 0, 0, ',', ' ') ?> Ar
                                        </td>
                                        <td><strong><?= number_format($fee['fee'] ?? 0, 0, ',', ' ') ?> Ar</strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="2" class="text-center text-muted">Aucun bareme de transfert configure.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card bg-warning">
            <div class="card-body">
                <h6 class="card-title"><i class="fas fa-shield-alt"></i> Securite</h6>
                <ul class="small mb-0">
                    <li><i class="fas fa-check-circle text-success"></i> Transferts securises</li>
                    <li><i class="fas fa-check-circle text-success"></i> Validation du numero</li>
                    <li><i class="fas fa-check-circle text-success"></i> Historique trace</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    const fees = <?= json_encode(array_values($transfer_fees ?? []), JSON_UNESCAPED_UNICODE) ?>;

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
    const receiverDisplay = document.getElementById('receiver_display');

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
        receiverDisplay.textContent = formatter.format(amount).replace('MGA', 'Ar');
    });
</script>

<?= $this->endSection() ?>
<?= $this->include('layouts/footer') ?>
