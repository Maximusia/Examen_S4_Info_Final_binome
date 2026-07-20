<?= $this->extend('layouts/header') ?>

<?= $this->section('content') ?>

<?php
$ownPrefixes = array_map(static fn ($row) => $row['prefix'] ?? '', $own_prefixes ?? []);
$externalPrefixes = array_map(static fn ($row) => $row['prefix'] ?? '', $external_prefixes ?? []);
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h1 class="mb-2">
            <i class="fas fa-exchange-alt text-info"></i> Effectuer un transfert multiple
        </h1>
        <p class="text-muted">Saisissez plusieurs destinataires. Le montant total sera reparti de facon egale.</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card shadow">
            <div class="card-body p-4">
                <form action="<?= base_url('/transfer') ?>" method="post" class="needs-validation" novalidate>
                    <?= csrf_field() ?>

                    <div class="alert alert-info mb-4">
                        <i class="fas fa-wallet"></i>
                        <strong>Solde disponible :</strong>
                        <?= number_format($balance ?? 0, 0, ',', ' ') ?> Ar
                    </div>

                    <div class="mb-4">
                        <label for="receiver_phones" class="form-label">
                            <i class="fas fa-users"></i> Numeros des destinataires
                        </label>
                        <textarea
                            class="form-control"
                            id="receiver_phones"
                            name="receiver_phones"
                            rows="5"
                            placeholder="0331234567, 0379876543
0311111111
0322222222"
                            required
                        ><?= old('receiver_phones', '') ?></textarea>
                        <small class="form-text text-muted d-block mt-2">
                            Saisissez les numeros separes par des virgules, des espaces ou des retours a la ligne.
                        </small>
                        <div class="invalid-feedback">Veuillez saisir au moins un numero valide.</div>
                    </div>

                    <div class="mb-4">
                        <label for="amount" class="form-label">
                            <i class="fas fa-money-bill"></i> Montant total a partager (Ar)
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-coins"></i></span>
                            <input
                                type="number"
                                class="form-control"
                                id="amount"
                                name="amount"
                                min="100"
                                max="<?= esc((string) ($balance ?? 0)) ?>"
                                placeholder="Montant total"
                                required
                            >
                        </div>
                        <small class="form-text text-muted d-block mt-2">
                            Le montant sera partage equitativement entre les destinataires detectes.
                        </small>
                        <div class="invalid-feedback">Veuillez entrer un montant valide.</div>
                    </div>

                    <div class="mb-4 form-check">
                        <input class="form-check-input" type="checkbox" id="include_withdrawal_fee" name="include_withdrawal_fee" value="1">
                        <label class="form-check-label" for="include_withdrawal_fee">
                            Inclure les frais de retrait pour chaque destinataire
                        </label>
                        <div class="form-text">
                            Si active, les frais de retrait sont ajoutes a la part de chaque destinataire.
                        </div>
                    </div>

                    <div class="mb-4 form-check">
                        <input class="form-check-input" type="checkbox" id="confirm" name="confirm" required>
                        <label class="form-check-label" for="confirm">
                            Je confirme vouloir executer ce transfert multiple
                        </label>
                        <div class="invalid-feedback">Veuillez confirmer le transfert.</div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-info btn-lg">
                            <i class="fas fa-paper-plane"></i> Confirmer le transfert
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card bg-light mb-4">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-eye"></i> Apercu</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Destinataires detectes</span>
                    <strong id="preview_count">0</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Part moyenne approx.</span>
                    <strong id="preview_share">0 Ar</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Destinataires internes</span>
                    <strong id="preview_internal">0</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Destinataires externes</span>
                    <strong id="preview_external">0</strong>
                </div>
                <hr>
                <div class="small text-muted mb-0">
                    Le montant exact est reparti cote serveur avec `intdiv()` et le reste est distribue aux premiers destinataires.
                </div>
            </div>
        </div>

        <div class="alert alert-warning d-none" id="external_warning">
            <i class="fas fa-triangle-exclamation"></i>
            Certains numeros detectes appartiennent a d'autres operateurs. Une commission externe sera appliquee.
        </div>

        <div class="card bg-light mb-4">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-percentage"></i> Bareme des frais de transfert</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-borderless mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Montant</th>
                                <th>Frais</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($transfer_fees)): ?>
                                <?php foreach ($transfer_fees as $fee): ?>
                                    <tr>
                                        <td>
                                            <?= number_format((int) ($fee['min_amount'] ?? 0), 0, ',', ' ') ?> - <?= number_format((int) ($fee['max_amount'] ?? 0), 0, ',', ' ') ?> Ar
                                        </td>
                                        <td>
                                            <strong><?= number_format((int) ($fee['fee'] ?? 0), 0, ',', ' ') ?> Ar</strong>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" class="text-center text-muted">Aucun bareme configure.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card bg-white">
            <div class="card-body">
                <h6 class="card-title"><i class="fas fa-shield-alt"></i> Informations</h6>
                <ul class="small mb-0">
                    <li>Les numeros doivent appartenir a un prefixe connu.</li>
                    <li>Les destinataires internes doivent exister dans la base locale.</li>
                    <li>Les destinataires externes peuvent etre envoyes sans compte local.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    const ownPrefixes = <?= json_encode(array_values(array_filter($ownPrefixes)), JSON_UNESCAPED_UNICODE) ?>;
    const externalPrefixes = <?= json_encode(array_values(array_filter($externalPrefixes)), JSON_UNESCAPED_UNICODE) ?>;
    const textarea = document.getElementById('receiver_phones');
    const amountInput = document.getElementById('amount');
    const previewCount = document.getElementById('preview_count');
    const previewShare = document.getElementById('preview_share');
    const previewInternal = document.getElementById('preview_internal');
    const previewExternal = document.getElementById('preview_external');
    const externalWarning = document.getElementById('external_warning');

    function normalizePhones(value) {
        return value
            .split(/[\s,;]+/)
            .map(item => item.replace(/\D+/g, ''))
            .filter(item => item.length > 0);
    }

    function uniquePhones(list) {
        return [...new Set(list)];
    }

    function detectOperatorType(phone) {
        const prefix = phone.slice(0, 3);
        if (ownPrefixes.includes(prefix)) {
            return 'own';
        }
        if (externalPrefixes.includes(prefix)) {
            return 'external';
        }
        return 'unknown';
    }

    function formatAr(value) {
        return new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'MGA',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(value).replace('MGA', 'Ar');
    }

    function refreshPreview() {
        const phones = uniquePhones(normalizePhones(textarea.value));
        const total = parseInt(amountInput.value, 10) || 0;
        const count = phones.length;
        const internalCount = phones.filter(phone => detectOperatorType(phone) === 'own').length;
        const externalCount = phones.filter(phone => detectOperatorType(phone) === 'external').length;

        previewCount.textContent = count;
        previewInternal.textContent = internalCount;
        previewExternal.textContent = externalCount;

        if (count > 0) {
            const baseShare = Math.floor(total / count);
            const remainder = total % count;
            const approxShare = remainder > 0 ? `${formatAr(baseShare)} a ${formatAr(baseShare + 1)}` : formatAr(baseShare);
            previewShare.textContent = approxShare;
        } else {
            previewShare.textContent = '0 Ar';
        }

        externalWarning.classList.toggle('d-none', externalCount === 0);
    }

    textarea.addEventListener('input', refreshPreview);
    amountInput.addEventListener('input', refreshPreview);
    refreshPreview();
</script>

<?= $this->endSection() ?>
<?= $this->include('layouts/footer') ?>
