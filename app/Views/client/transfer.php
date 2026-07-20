<?= $this->extend('layouts/header') ?>

<?= $this->section('content') ?>

<div class="row mb-4">
    <div class="col-md-12">
        <h1 class="mb-2">
            <i class="fas fa-exchange-alt text-info"></i> Effectuer un transfert
        </h1>
        <p class="text-muted">Transférez de l'argent à un autre utilisateur Mobile Money</p>
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
                            <i class="fas fa-user-tie"></i> Numéro du destinataire
                        </label>
                        <input type="tel" class="form-control" id="receiver_phone" name="receiver_phone" placeholder="Numéro du destinataire (ex: 0331234567)" pattern="^03[3-7][0-9]{7}$" required>
                        <small class="form-text text-muted d-block mt-2"><i class="fas fa-info-circle"></i> Format accepté: 033XXXXXXX ou 037XXXXXXX</small>
                        <div class="invalid-feedback">Veuillez entrer un numéro valide</div>
                    </div>

                    <div class="mb-4">
                        <label for="amount" class="form-label">
                            <i class="fas fa-money-bill"></i> Montant à transférer (Ar)
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
                            <option value="">-- Sélectionnez un motif --</option>
                            <option value="remittance">Envoi d'argent</option>
                            <option value="payment">Paiement</option>
                            <option value="loan">Prêt</option>
                            <option value="gift">Cadeau</option>
                            <option value="other">Autre</option>
                        </select>
                    </div>

                    <div class="alert alert-light border mb-4">
                        <h6 class="mb-3"><i class="fas fa-file-alt"></i> Récapitulatif</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Montant à transférer:</span>
                            <strong id="amount_display">0 Ar</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Frais de transfert:</span>
                            <strong id="fee_display" class="text-danger">0 Ar</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <strong>Total débité:</strong>
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
                            Je confirme vouloir transférer ce montant au destinataire indiqué
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
                <h6 class="mb-0"><i class="fas fa-percentage"></i> Barème des frais de transfert</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-borderless">
                        <thead class="table-light"><tr><th>Montant</th><th>Frais</th></tr></thead>
                        <tbody>
                            <tr><td>100 - 1 000 Ar</td><td><strong>50 Ar</strong></td></tr>
                            <tr><td>1 001 - 5 000 Ar</td><td><strong>50 Ar</strong></td></tr>
                            <tr><td>5 001 - 10 000 Ar</td><td><strong>100 Ar</strong></td></tr>
                            <tr><td>10 001 - 25 000 Ar</td><td><strong>200 Ar</strong></td></tr>
                            <tr><td>25 001 - 50 000 Ar</td><td><strong>400 Ar</strong></td></tr>
                            <tr><td>50 001 - 100 000 Ar</td><td><strong>800 Ar</strong></td></tr>
                            <tr><td>100 001 - 250 000 Ar</td><td><strong>1 500 Ar</strong></td></tr>
                            <tr><td>250 001 - 500 000 Ar</td><td><strong>1 500 Ar</strong></td></tr>
                            <tr><td>500 001 - 1 000 000 Ar</td><td><strong>2 500 Ar</strong></td></tr>
                            <tr><td>1 000 001+ Ar</td><td><strong>3 000 Ar</strong></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card bg-warning">
            <div class="card-body">
                <h6 class="card-title"><i class="fas fa-shield-alt"></i> Sécurité</h6>
                <ul class="small mb-0">
                    <li><i class="fas fa-check-circle text-success"></i> Transferts sécurisés</li>
                    <li><i class="fas fa-check-circle text-success"></i> Validation du numéro</li>
                    <li><i class="fas fa-check-circle text-success"></i> Historique tracé</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    const fees = [
        { min: 100, max: 1000, fee: 50 },
        { min: 1001, max: 5000, fee: 50 },
        { min: 5001, max: 10000, fee: 100 },
        { min: 10001, max: 25000, fee: 200 },
        { min: 25001, max: 50000, fee: 400 },
        { min: 50001, max: 100000, fee: 800 },
        { min: 100001, max: 250000, fee: 1500 },
        { min: 250001, max: 500000, fee: 1500 },
        { min: 500001, max: 1000000, fee: 2500 },
        { min: 1000001, max: 2000000, fee: 3000 }
    ];

    function getFee(amount) {
        for (let rule of fees) {
            if (amount >= rule.min && amount <= rule.max) {
                return rule.fee;
            }
        }
        return 0;
    }

    document.getElementById('amount').addEventListener('input', function() {
        const amount = parseInt(this.value) || 0;
        const fee = getFee(amount);
        const total = amount + fee;
        const receiver = amount;
        const formatter = new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'MGA', minimumFractionDigits: 0 });
        document.getElementById('amount_display').textContent = formatter.format(amount).replace('MGA', 'Ar');
        document.getElementById('fee_display').textContent = formatter.format(fee).replace('MGA', 'Ar');
        document.getElementById('total_display').textContent = formatter.format(total).replace('MGA', 'Ar');
        document.getElementById('receiver_display').textContent = formatter.format(receiver).replace('MGA', 'Ar');
    });
</script>

<?= $this->endSection() ?>
<?= $this->include('layouts/footer') ?>
