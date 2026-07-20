<?= $this->extend('layouts/header') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-lg">
            <div class="card-body p-5">
                <!-- En-tête du formulaire -->
                <div class="text-center mb-4">
                    <i class="fas fa-mobile-alt" style="font-size: 3rem; color: #007bff;"></i>
                    <h2 class="card-title mt-3">Connexion</h2>
                    <p class="text-muted">Accédez à votre compte Mobile Money</p>
                </div>

                <!-- Formulaire de connexion -->
                <form action="<?= base_url('/auth/login') ?>" method="post" class="needs-validation" novalidate>
                    <?= csrf_field() ?>

                    <!-- Champ Numéro de téléphone -->
                    <div class="mb-3">
                        <label for="phone_number" class="form-label">
                            <i class="fas fa-phone"></i> Numéro de téléphone
                        </label>
                        <input 
                            type="tel" 
                            class="form-control" 
                            id="phone_number" 
                            name="phone_number" 
                            placeholder="Entrez votre numéro (ex: 0331234567)"
                            pattern="^03[3-7][0-9]{7}$"
                            required
                        >
                        <small class="form-text text-muted d-block mt-2">
                            Format accepté: 033XXXXXXX ou 037XXXXXXX
                        </small>
                        <div class="invalid-feedback">
                            Veuillez entrer un numéro valide (format: 03XXXXXXXX)
                        </div>
                    </div>

                    <!-- Champ Mot de passe -->
                    <div class="mb-4">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i> Mot de passe
                        </label>
                        <input 
                            type="password" 
                            class="form-control" 
                            id="password" 
                            name="password" 
                            placeholder="Entrez votre mot de passe"
                            required
                        >
                        <div class="invalid-feedback">
                            Veuillez entrer votre mot de passe
                        </div>
                    </div>

                    <!-- Bouton de connexion -->
                    <button type="submit" class="btn btn-primary w-100 btn-lg">
                        <i class="fas fa-sign-in-alt"></i> Se connecter
                    </button>
                </form>

                <!-- Informations supplémentaires -->
                <div class="mt-4 p-3 bg-light rounded">
                    <p class="small mb-0">
                        <i class="fas fa-info-circle text-info"></i>
                        <strong>Première visite?</strong> Utilisez le numéro de test: <code>0331234567</code>
                    </p>
                </div>
            </div>
        </div>

        <!-- Informations de sécurité -->
        <div class="mt-4 p-3 border-top">
            <h6 class="text-muted">
                <i class="fas fa-shield-alt"></i> Sécurité
            </h6>
            <ul class="small text-muted list-unstyled">
                <li><i class="fas fa-check-circle text-success"></i> Connexion sécurisée par mot de passe</li>
                <li><i class="fas fa-check-circle text-success"></i> Données chiffrées</li>
                <li><i class="fas fa-check-circle text-success"></i> Accès 24h/24 7j/7</li>
            </ul>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->include('layouts/footer') ?>
