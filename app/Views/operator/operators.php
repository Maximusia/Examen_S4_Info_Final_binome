<?= $this->extend('layouts/header') ?>

<?= $this->section('content') ?>

<div class="row mb-4">
    <div class="col-md-8">
        <h1 class="mb-2"><i class="fas fa-sitemap text-primary"></i> Gestion des operateurs</h1>
        <p class="text-muted">Notre operateur principal et les operateurs externes.</p>
    </div>
    <div class="col-md-4 text-md-end">
        <span class="badge bg-primary fs-6 p-2">Total: <?= esc((string) ($total_operators ?? 0)) ?></span>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <strong><i class="fas fa-plus"></i> Ajouter un operateur externe</strong>
            </div>
            <div class="card-body">
                <form action="<?= base_url('/admin/operators/add') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label" for="name">Nom</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= esc(old('name') ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="commission_percent">Commission (%)</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="commission_percent" name="commission_percent" value="<?= esc(old('commission_percent') ?? '0') ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Creer</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <strong><i class="fas fa-user-shield"></i> Notre operateur</strong>
            </div>
            <div class="card-body">
                <?php if (!empty($own_operator)): ?>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="h5 mb-1"><?= esc($own_operator['name'] ?? '') ?></div>
                            <div class="text-muted">Interne</div>
                        </div>
                        <span class="badge bg-success"><?= number_format((float) ($own_operator['commission_percent'] ?? 0), 2, ',', ' ') ?> %</span>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning mb-0">Operateur principal introuvable.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <strong><i class="fas fa-list"></i> Operateurs externes</strong>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Nom</th>
                    <th>Type</th>
                    <th>Commission</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($external_operators)): ?>
                    <?php foreach ($external_operators as $operator): ?>
                        <tr>
                            <td><?= esc($operator['name'] ?? '') ?></td>
                            <td><span class="badge bg-secondary">Externe</span></td>
                            <td><?= number_format((float) ($operator['commission_percent'] ?? 0), 2, ',', ' ') ?> %</td>
                            <td class="d-flex gap-2">
                                <form action="<?= base_url('/admin/operators/update/' . ($operator['id'] ?? 0)) ?>" method="post" class="d-flex gap-2 flex-wrap">
                                    <?= csrf_field() ?>
                                    <input type="text" name="name" class="form-control form-control-sm" value="<?= esc($operator['name'] ?? '') ?>" style="min-width: 180px;">
                                    <input type="number" step="0.01" min="0" name="commission_percent" class="form-control form-control-sm" value="<?= esc((string) ($operator['commission_percent'] ?? '0')) ?>" style="width: 120px;">
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Modifier</button>
                                </form>
                                <form action="<?= base_url('/admin/operators/delete/' . ($operator['id'] ?? 0)) ?>" method="post" onsubmit="return confirm('Supprimer cet operateur externe ?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">Aucun operateur externe.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    <a href="<?= base_url('/admin/dashboard') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour au tableau de bord</a>
</div>

<?= $this->endSection() ?>
<?= $this->include('layouts/footer') ?>
