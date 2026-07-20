<?= $this->extend('layouts/header') ?>

<?= $this->section('content') ?>

<div class="row mb-4">
    <div class="col-md-8">
        <h1 class="mb-2">
            <i class="fas fa-phone text-primary"></i> Gestion des préfixes
        </h1>
        <p class="text-muted">Gérez les préfixes téléphoniques autorisés sur la plateforme</p>
    </div>
    <div class="col-md-4 text-md-end">
        <button class="btn btn-success" data-bs-toggle="collapse" data-bs-target="#addPrefixForm">
            <i class="fas fa-plus-circle"></i> Ajouter un préfixe
        </button>
    </div>
</div>

<!-- Formulaire d'ajout de préfixe (formulaire collapse) -->
<div class="collapse mb-4" id="addPrefixForm">
    <div class="card bg-light">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <i class="fas fa-plus"></i> Ajouter un nouveau préfixe
            </h5>
            <form action="<?= base_url('/admin/prefixes/add') ?>" method="post" class="needs-validation" novalidate>
                <?= csrf_field() ?>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="prefix" class="form-label">
                            <i class="fas fa-barcode"></i> Préfixe (ex: 033)
                        </label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="prefix" 
                            name="prefix" 
                            placeholder="Entrez le préfixe (ex: 033)"
                            pattern="^0\d{2}$"
                            maxlength="3"
                            required
                        >
                        <small class="form-text text-muted">
                            Format: 3 chiffres commençant par 0 (ex: 033, 034, 037)
                        </small>
                        <div class="invalid-feedback">
                            Veuillez entrer un préfixe valide (ex: 033)
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Ajouter le préfixe
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-toggle="collapse" data-bs-target="#addPrefixForm">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Statistiques des préfixes -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card bg-light">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <div>
                            <h6 class="text-muted">Total préfixes</h6>
                            <h3 class="text-primary"><?= $total_prefixes ?? 0 ?></h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div>
                            <h6 class="text-muted">Actifs</h6>
                            <h3 class="text-success"><?= $active_prefixes ?? 0 ?></h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div>
                            <h6 class="text-muted">Exemples V1</h6>
                            <h3 class="text-info">033 / 037</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tableau des préfixes -->
<div class="row">
    <div class="col-md-12">
        <?php if (!empty($prefixes)): ?>
            <div class="card shadow">
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 10%;">
                                    <i class="fas fa-hashtag"></i> ID
                                </th>
                                <th style="width: 20%;">
                                    <i class="fas fa-barcode"></i> Préfixe
                                </th>
                                <th style="width: 35%;">
                                    <i class="fas fa-phone"></i> Exemples
                                </th>
                                <th style="width: 35%;">
                                    <i class="fas fa-cogs"></i> Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($prefixes as $prefix): ?>
                                <tr>
                                    <!-- ID -->
                                    <td>
                                        <small class="text-muted"><?= $prefix['id'] ?? '-' ?></small>
                                    </td>

                                    <!-- Préfixe -->
                                    <td>
                                        <span class="badge bg-primary" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                            <?= $prefix['prefix'] ?? '-' ?>
                                        </span>
                                    </td>

                                    <!-- Exemples de numéros -->
                                    <td>
                                        <small class="font-monospace">
                                            <?= $prefix['prefix'] ?>1234567
                                        </small>
                                    </td>

                                    <!-- Actions -->
                                    <td>
                                        <!-- Bouton Modifier -->
                                        <button 
                                            class="btn btn-sm btn-outline-warning"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editModal<?= $prefix['id'] ?? '' ?>"
                                            title="Modifier"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <!-- Bouton Supprimer -->
                                        <form 
                                            action="<?= base_url('/admin/prefixes/delete/' . ($prefix['id'] ?? '')) ?>" 
                                            method="post"
                                            style="display: inline;"
                                            onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce préfixe?');"
                                        >
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Modal de modification -->
                                <div class="modal fade" id="editModal<?= $prefix['id'] ?? '' ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">
                                                    <i class="fas fa-edit"></i> Modifier le préfixe
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="<?= base_url('/admin/prefixes/update/' . ($prefix['id'] ?? '')) ?>" method="post">
                                                <?= csrf_field() ?>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="prefixEdit<?= $prefix['id'] ?? '' ?>" class="form-label">Préfixe</label>
                                                        <input 
                                                            type="text" 
                                                            class="form-control" 
                                                            id="prefixEdit<?= $prefix['id'] ?? '' ?>"
                                                            name="prefix"
                                                            value="<?= $prefix['prefix'] ?? '' ?>"
                                                            pattern="^0\d{2}$"
                                                            maxlength="3"
                                                            required
                                                        >
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        Fermer
                                                    </button>
                                                    <button type="submit" class="btn btn-warning">
                                                        <i class="fas fa-save"></i> Mettre à jour
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-circle"></i>
                <strong>Aucun préfixe</strong><br>
                Aucun préfixe n'a été ajouté pour le moment. 
                <button class="btn btn-sm btn-warning" data-bs-toggle="collapse" data-bs-target="#addPrefixForm">
                    Ajouter un préfixe maintenant
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Informations supplémentaires -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card bg-light">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-lightbulb"></i> À propos des préfixes
                </h6>
            </div>
            <div class="card-body small">
                <p>
                    Les préfixes téléphoniques définissent les numéros autorisés à utiliser le service.
                    Ils sont utilisés à la connexion automatique des clients par numéro.
                </p>
                <p class="mb-0">
                    <strong>Exemples courants:</strong>
                    <ul class="small mb-0">
                        <li>033</li>
                        <li>037</li>
                    </ul>
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card bg-light">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle"></i> Conseils de gestion
                </h6>
            </div>
            <div class="card-body small">
                <ul class="list-unstyled mb-0">
                    <li><i class="fas fa-check-circle text-success"></i> Vérifiez avant d'ajouter un préfixe</li>
                    <li><i class="fas fa-check-circle text-success"></i> Les modifications sont immédiates</li>
                    <li><i class="fas fa-check-circle text-success"></i> Les suppressions affectent les nouveaux comptes</li>
                    <li><i class="fas fa-check-circle text-success"></i> Documentez les changements</li>
                </ul>
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
