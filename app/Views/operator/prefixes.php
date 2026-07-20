<?= $this->extend('layouts/header') ?>

<?= $this->section('content') ?>

<div class="row mb-4">
    <div class="col-md-8">
        <h1 class="mb-2"><i class="fas fa-phone text-primary"></i> Gestion des prefixes</h1>
        <p class="text-muted">Ajoutez ou supprimez des prefixes par operateur.</p>
    </div>
    <div class="col-md-4 text-md-end">
        <span class="badge bg-primary fs-6 p-2">Total prefixes: <?= esc((string) ($total_prefixes ?? 0)) ?></span>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <strong><i class="fas fa-plus"></i> Ajouter un prefixe</strong>
    </div>
    <div class="card-body">
        <form action="<?= base_url('/admin/prefixes/add') ?>" method="post" class="row g-3">
            <?= csrf_field() ?>
            <div class="col-md-6">
                <label class="form-label" for="operator_id">Operateur</label>
                <select class="form-select" id="operator_id" name="operator_id" required>
                    <?php foreach (($operators ?? []) as $operator): ?>
                        <option value="<?= esc((string) ($operator['id'] ?? 0)) ?>">
                            <?= esc($operator['name'] ?? '') ?><?= (int) ($operator['is_own_operator'] ?? 0) === 1 ? ' (Interne)' : ' (Externe)' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="prefix">Prefixe</label>
                <input type="text" class="form-control" id="prefix" name="prefix" pattern="^\d{3}$" maxlength="3" required>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Ajouter</button>
            </div>
        </form>
    </div>
</div>

<?php if (!empty($operators)): ?>
    <?php foreach ($operators as $operator): ?>
        <?php $rows = $grouped_prefixes[$operator['id']] ?? []; ?>
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div>
                    <strong><?= esc($operator['name'] ?? '') ?></strong>
                    <span class="badge <?= (int) ($operator['is_own_operator'] ?? 0) === 1 ? 'bg-success' : 'bg-secondary' ?>">
                        <?= (int) ($operator['is_own_operator'] ?? 0) === 1 ? 'Interne' : 'Externe' ?>
                    </span>
                </div>
                <span class="text-muted"><?= count($rows) ?> prefixe(s)</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Prefixe</th>
                            <th>Exemple</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($rows)): ?>
                            <?php foreach ($rows as $prefix): ?>
                                <tr>
                                    <td><span class="badge bg-primary"><?= esc($prefix['prefix'] ?? '') ?></span></td>
                                    <td><code><?= esc(($prefix['prefix'] ?? '') . '1234567') ?></code></td>
                                    <td class="d-flex gap-2 flex-wrap">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-warning js-edit-prefix"
                                            data-prefix-id="<?= esc((string) ($prefix['id'] ?? 0)) ?>"
                                            data-prefix-value="<?= esc($prefix['prefix'] ?? '') ?>"
                                            data-operator-id="<?= esc((string) ($operator['id'] ?? 0)) ?>"
                                        >
                                            Modifier
                                        </button>
                                        <?php if ((int) ($operator['is_own_operator'] ?? 0) === 1 && ($own_prefix_count ?? 0) <= 1): ?>
                                            <button type="button" class="btn btn-sm btn-outline-danger" disabled>Dernier prefixe interne</button>
                                        <?php else: ?>
                                            <form action="<?= base_url('/admin/prefixes/delete/' . ($prefix['id'] ?? 0)) ?>" method="post" onsubmit="return confirm('Supprimer ce prefixe ?');">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">Aucun prefixe pour cet operateur.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<div class="card shadow-sm d-none" id="editPrefixPanel">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <strong>Modifier un prefixe</strong>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="cancelEditPrefixBtn">Fermer</button>
        </div>
        <form method="post" id="editPrefixForm" class="row g-3">
            <?= csrf_field() ?>
            <div class="col-md-4">
                <label class="form-label" for="editPrefixValue">Prefixe</label>
                <input type="text" class="form-control" id="editPrefixValue" name="prefix" pattern="^\d{3}$" maxlength="3" required>
            </div>
            <div class="col-md-5">
                <label class="form-label" for="editOperatorValue">Operateur</label>
                <select class="form-select" id="editOperatorValue" name="operator_id" required>
                    <?php foreach (($operators ?? []) as $operator): ?>
                        <option value="<?= esc((string) ($operator['id'] ?? 0)) ?>">
                            <?= esc($operator['name'] ?? '') ?><?= (int) ($operator['is_own_operator'] ?? 0) === 1 ? ' (Interne)' : ' (Externe)' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-warning w-100">Mettre a jour</button>
            </div>
        </form>
    </div>
</div>

<div class="mt-4">
    <a href="<?= base_url('/admin/dashboard') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
</div>

<script>
    const editPrefixPanel = document.getElementById('editPrefixPanel');
    const editPrefixForm = document.getElementById('editPrefixForm');
    const editPrefixValue = document.getElementById('editPrefixValue');
    const editOperatorValue = document.getElementById('editOperatorValue');

    document.querySelectorAll('.js-edit-prefix').forEach((button) => {
        button.addEventListener('click', () => {
            editPrefixForm.action = '<?= base_url('/admin/prefixes/update/') ?>' + button.dataset.prefixId;
            editPrefixValue.value = button.dataset.prefixValue || '';
            editOperatorValue.value = button.dataset.operatorId || '';
            editPrefixPanel.classList.remove('d-none');
            editPrefixPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    document.getElementById('cancelEditPrefixBtn')?.addEventListener('click', () => {
        editPrefixPanel.classList.add('d-none');
    });
</script>

<?= $this->endSection() ?>
<?= $this->include('layouts/footer') ?>
