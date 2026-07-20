<?= $this->extend('layouts/header') ?>

<?= $this->section('content') ?>

<div class="row mb-4">
    <div class="col-md-8">
        <h1 class="mb-2">
            <i class="fas fa-phone text-primary"></i> Gestion des prefixes
        </h1>
        <p class="text-muted">Gerez les prefixes telephoniques autorises sur la plateforme</p>
    </div>
    <div class="col-md-4 text-md-end">
        <button type="button" class="btn btn-success" id="toggleAddPrefixBtn">
            <i class="fas fa-plus-circle"></i> Ajouter un prefixe
        </button>
    </div>
</div>

<div class="card bg-light mb-4 d-none" id="addPrefixForm">
    <div class="card-body">
        <h5 class="card-title mb-3">
            <i class="fas fa-plus"></i> Ajouter un nouveau prefixe
        </h5>
        <form action="<?= base_url('/admin/prefixes/add') ?>" method="post" class="needs-validation" novalidate>
            <?= csrf_field() ?>

            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="prefix" class="form-label">
                        <i class="fas fa-barcode"></i> Prefixe (ex: 033)
                    </label>
                    <input
                        type="text"
                        class="form-control"
                        id="prefix"
                        name="prefix"
                        placeholder="Entrez le prefixe (ex: 033)"
                        pattern="^0\d{2}$"
                        maxlength="3"
                        required
                    >
                    <small class="form-text text-muted">
                        Format: 3 chiffres commencant par 0 (ex: 033, 034, 037)
                    </small>
                    <div class="invalid-feedback">
                        Veuillez entrer un prefixe valide (ex: 033)
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Ajouter le prefixe
                </button>
                <button type="button" class="btn btn-secondary" id="cancelAddPrefixBtn">
                    Annuler
                </button>
            </div>
        </form>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card bg-light">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <div>
                            <h6 class="text-muted">Total prefixes</h6>
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

<div class="row">
    <div class="col-md-12">
        <?php if (!empty($prefixes)): ?>
            <div class="card shadow">
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 20%;">
                                    <i class="fas fa-barcode"></i> Prefixe
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
                                    <!-- <td>
                                        <small class="text-muted"><?= $prefix['id'] ?? '-' ?></small>
                                    </td> -->
                                    <td>
                                        <span class="badge bg-primary" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                            <?= $prefix['prefix'] ?? '-' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small class="font-monospace">
                                            <?= $prefix['prefix'] ?>1234567
                                        </small>
                                    </td>
                                    <td>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-warning js-edit-prefix"
                                            data-prefix-id="<?= $prefix['id'] ?? '' ?>"
                                            data-prefix-value="<?= esc($prefix['prefix'] ?? '') ?>"
                                            title="Modifier"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </button>

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
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-circle"></i>
                <strong>Aucun prefixe</strong><br>
                Aucun prefixe n'a ete ajoute pour le moment.
                <button type="button" class="btn btn-sm btn-warning" id="emptyAddPrefixBtn">
                    Ajouter un prefixe maintenant
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="card shadow mt-4 d-none" id="editPrefixPanel">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">
                <i class="fas fa-edit"></i> Modifier le prefixe
            </h5>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="cancelEditPrefixBtn">
                Fermer
            </button>
        </div>
        <form method="post" id="editPrefixForm">
            <?= csrf_field() ?>
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="editPrefixValue" class="form-label">Prefixe</label>
                    <input
                        type="text"
                        class="form-control"
                        id="editPrefixValue"
                        name="prefix"
                        pattern="^0\d{2}$"
                        maxlength="3"
                        required
                    >
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-warning w-100">
                        <i class="fas fa-save"></i> Mettre a jour
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card bg-light">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-lightbulb"></i> A propos des prefixes
                </h6>
            </div>
            <div class="card-body small">
                <p>
                    Les prefixes telephoniques definissent les numeros autorises a utiliser le service.
                    Ils sont utilises a la connexion automatique des clients par numero.
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
                    <li><i class="fas fa-check-circle text-success"></i> Verifiez avant d'ajouter un prefixe</li>
                    <li><i class="fas fa-check-circle text-success"></i> Les modifications sont immediates</li>
                    <li><i class="fas fa-check-circle text-success"></i> Les suppressions affectent les nouveaux comptes</li>
                    <li><i class="fas fa-check-circle text-success"></i> Documentez les changements</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <a href="<?= base_url('/admin/dashboard') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour au tableau de bord
        </a>
    </div>
</div>

<script>
    const addPrefixForm = document.getElementById('addPrefixForm');
    const editPrefixPanel = document.getElementById('editPrefixPanel');
    const editPrefixForm = document.getElementById('editPrefixForm');
    const editPrefixValue = document.getElementById('editPrefixValue');

    function showAddPrefixForm() {
        addPrefixForm.classList.remove('d-none');
        addPrefixForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function hideAddPrefixForm() {
        addPrefixForm.classList.add('d-none');
    }

    function showEditPrefixForm(id, value) {
        editPrefixForm.action = '<?= base_url('/admin/prefixes/update/') ?>' + id;
        editPrefixValue.value = value || '';
        editPrefixPanel.classList.remove('d-none');
        editPrefixPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
        editPrefixValue.focus();
    }

    function hideEditPrefixForm() {
        editPrefixPanel.classList.add('d-none');
    }

    document.getElementById('toggleAddPrefixBtn')?.addEventListener('click', showAddPrefixForm);
    document.getElementById('emptyAddPrefixBtn')?.addEventListener('click', showAddPrefixForm);
    document.getElementById('cancelAddPrefixBtn')?.addEventListener('click', hideAddPrefixForm);
    document.getElementById('cancelEditPrefixBtn')?.addEventListener('click', hideEditPrefixForm);

    document.querySelectorAll('.js-edit-prefix').forEach((button) => {
        button.addEventListener('click', () => {
            showEditPrefixForm(button.dataset.prefixId, button.dataset.prefixValue);
        });
    });
</script>

<?= $this->endSection() ?>
<?= $this->include('layouts/footer') ?>
