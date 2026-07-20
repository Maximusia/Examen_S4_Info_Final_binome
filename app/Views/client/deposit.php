<?= $this->extend('layouts/header') ?>

<?= $this->section('content') ?>

<div class="row mb-4">
    <div class="col-md-12">
        <h1 class="mb-2">
            <i class="fas fa-arrow-down text-success"></i> Effectuer un dépôt
        </h1>
        <p class="text-muted">Augmentez votre solde en effectuant un dépôt</p>
    </div>
</div>

<div class="row">
    <!-- Formulaire de dépôt -->
    <div class="col-md-8 col-lg-6">
        <div class="card shadow">
            <div class="card-body p-4">
                <form action="<?= base_url('/deposit') ?>" method="post" class="needs-validation" novalidate>
                    <?= csrf_field() ?>

                    <!-- Champ Montant -->
                    <div class="mb-4">
                        <label for="amount" class="form-label">
                            <i class="fas fa-money-bill"></i> Montant à déposer (FCFA)
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-currency-circle"></i>
                            </span>
                            <input 
                                type="number" 
                                class="form-control" 
                                id="amount" 
                                name="amount" 
                                placeholder="Entrez le montant"
                                min="100"
                                max="2000000"
                                required
                            >
                        </div>
                        <small class="form-text text-muted d-block mt-2">
                            <i class="fas fa-info-circle"></i> Montant minimum: 100 FCFA
                        </small>
                        <div class="invalid-feedback">
                            Veuillez entrer un montant valide (minimum 100 FCFA)
                        </div>
                    </div>

                    <!-- Méthode de paiement -->
                    <div class="mb-4">
                        <label for="payment_method" class="form-label">
                            <i class="fas fa-credit-card"></i> Méthode de paiement
                        </label>
                        <select class="form-select" id="payment_method" name="payment_method" required>
                            <option value="">-- Sélectionnez une méthode --</option>
                            <option value="cash">Espèces</option>
                            <option value="bank_transfer">Virement bancaire</option>
                            <option value="mobile_money">Mobile Money</option>
                        </select>
                        <div class="invalid-feedback">
                            Veuillez sélectionner une méthode de paiement
                        </div>
                    </div>

                    <!-- Notes/Références -->
                    <div class="mb-4">
                        <label for="notes" class="form-label">
                            <i class="fas fa-sticky-note"></i> Notes (optionnel)
                        </label>
                        <textarea 
                            class="form-control" 
                            id="notes" 
                            name="notes" 
                            rows="3" 
                            placeholder="Entrez une note ou une référence pour ce dépôt"
                        ></textarea>
                    </div>

                    <!-- Récapitulatif -->
                    <div class="alert alert-light border mb-4">
                        <h6 class="mb-3">
                            <i class="fas fa-file-alt"></i> Récapitulatif
                        </h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Montant:</span>
                            <strong id="amount_display">0 FCFA</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Frais de dépôt:</span>
                            <strong id="fee_display" class="text-success">GRATUIT</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Total crédité:</strong>
                            <strong id="total_display" class="text-success">0 FCFA</strong>
                        </div>
                    </div>

                    <!-- Boutons -->
                    <div class="d-grid gap-2 mb-3">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-check-circle"></i> Confirmer le dépôt
                        </button>
                    </div>
                    <a href="<?= base_url('/dashboard') ?>" class="btn btn-secondary w-100">
                        <i class="fas fa-arrow-left"></i> Retour au tableau de bord
                    </a>
                </form>
            </div>
        </div>
    </div>

    <!-- Informations supplémentaires -->
    <div class="col-md-4 col-lg-6">
        <div class="card bg-light mb-3">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0">
                    <i class="fas fa-lightbulb"></i> Informations
                </h6>
            </div>
            <div class="card-body">
                <h6>Frais de dépôt</h6>
                <p class="mb-3">
                    <strong>Les dépôts sont gratuits!</strong> Aucuns frais ne sera appliqué au montant déposé.
                </p>
                
                <h6>Délai de traitement</h6>
                <p class="mb-3">
                    Les dépôts sont généralement traités instantanément. En cas de délai, contactez le support.
                </p>
                
                <h6>Limitations</h6>
                <ul class="small mb-0">
                    <li>Montant minimum: <strong>100 FCFA</strong></li>
                    <li>Montant maximum: <strong>2 000 000 FCFA</strong></li>
                    <li>Limite quotidienne: Illimitée</li>
                </ul>
            </div>
        </div>

        <div class="card bg-info text-white">
            <div class="card-body">
                <h6 class="card-title">
                    <i class="fas fa-phone"></i> Besoin d'aide?
                </h6>
                <p class="small mb-0">
                    Contactez notre équipe de support au <strong>+237 XXX XXX XXX</strong> ou envoyez un email à <strong>support@mobilemoney.cm</strong>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Script pour mettre à jour le récapitulatif en temps réel -->
<script>
    document.getElementById('amount').addEventListener('input', function() {
        const amount = parseInt(this.value) || 0;
        const fee = 0; // Frais gratuits pour dépôt
        const total = amount + fee;
        
        document.getElementById('amount_display').textContent = 
            new Intl.NumberFormat('fr-FR', {
                style: 'currency',
                currency: 'XAF',
                minimumFractionDigits: 0
            }).format(amount);
        
        document.getElementById('total_display').textContent = 
            new Intl.NumberFormat('fr-FR', {
                style: 'currency',
                currency: 'XAF',
                minimumFractionDigits: 0
            }).format(total);
    });
</script>

<?= $this->endSection() ?>
<?= $this->include('layouts/footer') ?>
