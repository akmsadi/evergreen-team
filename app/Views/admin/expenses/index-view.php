<?= $this->extend('admin/template/default') ?>

<?= $this->section('content') ?>
<div class="content-card">
    <div class="panel">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-6">
            <div>
                <h1 class="h2 fw-bold mb-2">General Expenses</h1>
                <p class="text-secondary mb-0">Record shared costs not tied to a specific match — equipment, accessories, and other club expenses.</p>
            </div>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <div class="card-surface mb-6">
            <h2 class="h4 fw-bold mb-3">Add Expense</h2>
            <form action="<?= site_url('/admin/expenses') ?>" method="post">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-7">
                        <label for="expense_title" class="form-label fw-semibold">Expense Title</label>
                        <input type="text" class="form-control<?= isset($expenseErrors['expense_title']) ? ' is-invalid' : '' ?>" id="expense_title" name="expense_title" value="<?= esc(old('expense_title')) ?>">
                        <?php if (isset($expenseErrors['expense_title'])): ?>
                            <div class="invalid-feedback"><?= esc($expenseErrors['expense_title']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-5">
                        <label for="expense_amount" class="form-label fw-semibold">Amount</label>
                        <input type="number" step="0.01" min="0.01" class="form-control<?= isset($expenseErrors['expense_amount']) ? ' is-invalid' : '' ?>" id="expense_amount" name="expense_amount" value="<?= esc(old('expense_amount')) ?>">
                        <?php if (isset($expenseErrors['expense_amount'])): ?>
                            <div class="invalid-feedback"><?= esc($expenseErrors['expense_amount']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-12">
                        <label for="expense_notes" class="form-label fw-semibold">Notes</label>
                        <textarea class="form-control" id="expense_notes" name="expense_notes" rows="2"><?= esc(old('expense_notes')) ?></textarea>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <label class="form-label fw-semibold mb-0">Contributors</label>
                            <div class="small text-secondary" data-add-selected-count>0 selected</div>
                            <button type="button" class="btn btn-light border btn-sm" data-check-all-add>Check All</button>
                        </div>
                        <?php if (isset($expenseErrors['contributor_ids'])): ?>
                            <div class="text-danger small mb-2"><?= esc($expenseErrors['contributor_ids']) ?></div>
                        <?php endif; ?>
                        <?php $oldContributorIds = array_map('intval', (array) old('contributor_ids', [])); ?>
                        <div class="row g-2" data-add-contributors>
                            <?php foreach ($players as $player): ?>
                                <div class="col-md-4 col-lg-3">
                                    <div class="form-check border rounded-3 px-3 py-2">
                                        <input class="form-check-input" type="checkbox"
                                            id="contributor_<?= esc((string) $player['id']) ?>"
                                            name="contributor_ids[]"
                                            value="<?= esc((string) $player['id']) ?>"
                                            <?= in_array((int) $player['id'], $oldContributorIds, true) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="contributor_<?= esc((string) $player['id']) ?>">
                                            <?= esc($player['name']) ?>
                                            <?php if (($player['status'] ?? '') === 'pending'): ?>
                                                <span class="badge text-bg-warning ms-1" style="font-size: 0.65em;">Pending</span>
                                            <?php endif; ?>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Add Expense</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-surface">
            <h2 class="h4 fw-bold mb-3">Expense Ledger</h2>
            <?php if ($expenses === []): ?>
                <p class="text-secondary mb-0">No general expenses recorded yet.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Expense</th>
                                <th>Amount</th>
                                <th>Split</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($expenses as $expense): ?>
                                <?php
                                $currentContributorIds = array_map(
                                    static fn(array $c): int => (int) $c['player_id'],
                                    $expense['contributors']
                                );
                                $isEditing = $expenseEditId === (int) $expense['id'];
                                $editContributorIds = $isEditing
                                    ? array_map('intval', (array) old('contributor_ids', $currentContributorIds))
                                    : $currentContributorIds;
                                ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold"><?= esc($expense['title']) ?></div>
                                        <?php if (!empty($expense['notes'])): ?>
                                            <div class="text-secondary small"><?= esc($expense['notes']) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc(number_format((float) $expense['amount'], 2)) ?></td>
                                    <td>
                                        <?= esc($expense['contributors'] === []
                                            ? 'No contributors'
                                            : 'Shared across ' . count($expense['contributors']) . ' contributor' . (count($expense['contributors']) === 1 ? '' : 's')
                                        ) ?>
                                        <details class="mt-2" <?= $isEditing ? 'open' : '' ?>>
                                            <summary class="small text-success" role="button">Edit</summary>
                                            <div class="mt-3 d-flex gap-2 align-items-start">
                                                <form action="<?= site_url('/admin/expenses/' . $expense['id'] . '/update') ?>" method="post" class="flex-grow-1">
                                                    <?= csrf_field() ?>
                                                    <div class="row g-2">
                                                        <div class="col-12">
                                                            <input type="text"
                                                                class="form-control form-control-sm<?= $isEditing && isset($expenseUpdateErrors['expense_title']) ? ' is-invalid' : '' ?>"
                                                                name="expense_title"
                                                                value="<?= esc($isEditing ? old('expense_title', $expense['title']) : $expense['title']) ?>"
                                                                placeholder="Expense title">
                                                            <?php if ($isEditing && isset($expenseUpdateErrors['expense_title'])): ?>
                                                                <div class="invalid-feedback"><?= esc($expenseUpdateErrors['expense_title']) ?></div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="col-12">
                                                            <input type="number" step="0.01" min="0.01"
                                                                class="form-control form-control-sm<?= $isEditing && isset($expenseUpdateErrors['expense_amount']) ? ' is-invalid' : '' ?>"
                                                                name="expense_amount"
                                                                value="<?= esc($isEditing ? old('expense_amount', $expense['amount']) : $expense['amount']) ?>"
                                                                placeholder="Amount">
                                                            <?php if ($isEditing && isset($expenseUpdateErrors['expense_amount'])): ?>
                                                                <div class="invalid-feedback"><?= esc($expenseUpdateErrors['expense_amount']) ?></div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="col-12">
                                                            <textarea class="form-control form-control-sm" name="expense_notes" rows="2" placeholder="Notes"><?= esc($isEditing ? old('expense_notes', $expense['notes']) : $expense['notes']) ?></textarea>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="fw-semibold small mb-2">Contributors</div>
                                                            <?php if ($isEditing && isset($expenseUpdateErrors['contributor_ids'])): ?>
                                                                <div class="text-danger small mb-2"><?= esc($expenseUpdateErrors['contributor_ids']) ?></div>
                                                            <?php endif; ?>
                                                            <div class="row g-1">
                                                                <?php foreach ($players as $player): ?>
                                                                    <div class="col-md-6">
                                                                        <div class="form-check border rounded-3 px-2 py-1">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="edit_contributor_<?= esc((string) $expense['id']) ?>_<?= esc((string) $player['id']) ?>"
                                                                                name="contributor_ids[]"
                                                                                value="<?= esc((string) $player['id']) ?>"
                                                                                <?= in_array((int) $player['id'], $editContributorIds, true) ? 'checked' : '' ?>>
                                                                            <label class="form-check-label small" for="edit_contributor_<?= esc((string) $expense['id']) ?>_<?= esc((string) $player['id']) ?>">
                                                                                <?= esc($player['name']) ?>
                                                                                <?php if (($player['status'] ?? '') === 'pending'): ?>
                                                                                    <span class="badge text-bg-warning ms-1" style="font-size: 0.6em;">Pending</span>
                                                                                <?php endif; ?>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                                        </div>
                                                    </div>
                                                </form>
                                                <form action="<?= site_url('/admin/expenses/' . $expense['id'] . '/delete') ?>" method="post" onsubmit="return confirm('Delete this expense?');">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                                </form>
                                            </div>
                                        </details>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
(() => {
    const addGrid = document.querySelector('[data-add-contributors]');
    if (addGrid) {
        const checkboxes = Array.from(addGrid.querySelectorAll('input[type="checkbox"]'));
        const countEl = document.querySelector('[data-add-selected-count]');

        const sync = () => {
            if (countEl) countEl.textContent = checkboxes.filter(c => c.checked).length + ' selected';
        };

        sync();
        checkboxes.forEach(c => c.addEventListener('change', sync));

        document.querySelector('[data-check-all-add]')?.addEventListener('click', () => {
            checkboxes.forEach(c => { c.checked = true; });
            sync();
        });
    }
})();
</script>
<?= $this->endSection() ?>
