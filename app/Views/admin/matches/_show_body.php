<?php $matchContributorErrors = session()->getFlashdata('match_contributor_errors') ?? []; ?>
<?php $selectedMatchContributorIds = array_map('intval', (array) old('match_contributor_ids', $matchContributorIds ?? [])); ?>
<?php
$tossWinnerLabel = match ((string) ($match['toss_winner'] ?? '')) {
    'team_a' => (string) ($match['team_name'] ?? 'Team A'),
    'team_b' => (string) ($match['opponent_name'] ?? 'Team B'),
    default => trim((string) ($match['toss_winner'] ?? '')) !== '' ? (string) $match['toss_winner'] : '-',
};
$tossDecisionLabel = match ((string) ($match['toss_decision'] ?? '')) {
    'bat' => 'Bat first',
    'bowl' => 'Bowl first',
    default => '-',
};
?>

<div class="card-surface mb-6">
    <h2 class="h4 fw-bold mb-3">Match Details</h2>
    <div class="row g-3 mb-6">
        <div class="col-md-4 col-xl-2">
            <div class="border rounded-3 h-100 p-3">
                <div class="text-secondary small text-uppercase mb-1">Type</div>
                <div class="fw-semibold"><?= esc(ucwords(str_replace('_', ' ', (string) ($match['match_type'] ?? '-')))) ?></div>
            </div>
        </div>
        <div class="col-md-4 col-xl-2">
            <div class="border rounded-3 h-100 p-3">
                <div class="text-secondary small text-uppercase mb-1">Overs</div>
                <div class="fw-semibold"><?= esc((string) ($match['format_overs'] ?? '-')) ?></div>
            </div>
        </div>
        <div class="col-md-4 col-xl-2">
            <div class="border rounded-3 h-100 p-3">
                <div class="text-secondary small text-uppercase mb-1">Venue</div>
                <div class="fw-semibold"><?= esc($match['venue'] ?: '-') ?></div>
            </div>
        </div>
        <div class="col-md-4 col-xl-2">
            <div class="border rounded-3 h-100 p-3">
                <div class="text-secondary small text-uppercase mb-1">Scheduled</div>
                <div class="fw-semibold"><?= esc(getFormattedDate($match['scheduled_at'] ?? null, '-')) ?></div>
            </div>
        </div>
        <div class="col-md-4 col-xl-2">
            <div class="border rounded-3 h-100 p-3">
                <div class="text-secondary small text-uppercase mb-1">Toss Winner</div>
                <div class="fw-semibold"><?= esc($tossWinnerLabel) ?></div>
            </div>
        </div>
        <div class="col-md-4 col-xl-2">
            <div class="border rounded-3 h-100 p-3">
                <div class="text-secondary small text-uppercase mb-1">Decision</div>
                <div class="fw-semibold"><?= esc($tossDecisionLabel) ?></div>
            </div>
        </div>
    </div>

    <h2 class="h4 fw-bold mb-3">Participants</h2>
    <div class="row g-3">
        <?php foreach ($contributorGroups as $group): ?>
            <div class="col-md-6">
                <div class="border rounded-3 h-100 p-3">
                    <div class="fw-semibold mb-3"><?= esc($group['label']) ?></div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Player</th>
                                    <th>Tags</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($group['participants'] as $participant): ?>
                                    <tr>
                                        <td><?= esc($participant['name']) ?></td>
                                        <td>
                                            <?php if ((int) $participant['is_captain'] === 1): ?>
                                                <span class="badge rounded-pill text-bg-dark">Captain</span>
                                            <?php endif; ?>
                                            <?php if ((int) $participant['is_wicketkeeper'] === 1): ?>
                                                <span class="badge rounded-pill text-bg-secondary">WK</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?= view('admin/matches/_scoreboard_report', ['match' => $match, 'scoreboard' => $scoreboard]) ?>

<div class="card-surface mb-6">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-3">
        <div>
            <h2 class="h4 fw-bold mb-2">Match Contributors</h2>
            <p class="text-secondary mb-0">Select the players who share every expense recorded for this match.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="small text-secondary" data-contributor-selected-count><?= esc((string) count($selectedMatchContributorIds)) ?> selected</div>
            <button type="button" class="btn btn-light border btn-sm" data-check-all-contributors>Check All</button>
        </div>
    </div>
    <form action="<?= site_url('/admin/matches/' . $match['id'] . '/contributors') ?>" method="post" data-match-contributors-form>
        <?= csrf_field() ?>
        <div class="row g-3">
            <?php foreach ($contributorGroups as $group): ?>
                <div class="col-md-6">
                    <div class="contributor-group h-100">
                        <div class="fw-semibold mb-2"><?= esc($group['label']) ?></div>
                        <div class="row g-2">
                            <?php foreach ($group['participants'] as $participant): ?>
                                <div class="col-12">
                                    <div class="form-check border rounded-3 px-3 py-2">
                                        <input class="form-check-input" type="checkbox" id="match_contributor_<?= esc((string) $participant['player_id']) ?>" name="match_contributor_ids[]" value="<?= esc((string) $participant['player_id']) ?>" <?= in_array((int) $participant['player_id'], $selectedMatchContributorIds, true) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="match_contributor_<?= esc((string) $participant['player_id']) ?>">
                                            <?= esc($participant['name']) ?>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="col-12">
                <?php if (isset($matchContributorErrors['match_contributor_ids'])): ?>
                    <div class="text-danger small mb-3"><?= esc($matchContributorErrors['match_contributor_ids']) ?></div>
                <?php endif; ?>
                <button type="submit" class="btn btn-evergreen">Save Contributors</button>
            </div>
        </div>
    </form>
</div>

<script>
    (() => {
        const contributorsForm = document.querySelector('[data-match-contributors-form]');

        if (!contributorsForm) {
            return;
        }

        const contributorCheckboxes = Array.from(contributorsForm.querySelectorAll('input[name="match_contributor_ids[]"]'));
        const selectedCount = document.querySelector('[data-contributor-selected-count]');
        const checkAllButton = document.querySelector('[data-check-all-contributors]');

        const syncSelectedCount = () => {
            if (!selectedCount) {
                return;
            }

            const checkedTotal = contributorCheckboxes.filter((checkbox) => checkbox.checked).length;
            selectedCount.textContent = `${checkedTotal} selected`;
        };

        checkAllButton?.addEventListener('click', () => {
            contributorCheckboxes.forEach((checkbox) => {
                checkbox.checked = true;
            });

            syncSelectedCount();
        });

        contributorCheckboxes.forEach((checkbox) => {
            checkbox.addEventListener('change', syncSelectedCount);
        });
    })();
</script>

<div class="card-surface mb-6">
    <h2 class="h4 fw-bold mb-3">Add Expense</h2>
    <?php if (($matchContributorIds ?? []) === []): ?>
        <div class="alert alert-warning">Set match contributors before adding expenses.</div>
    <?php endif; ?>
    <form action="<?= site_url('/admin/matches/' . $match['id'] . '/expenses') ?>" method="post">
        <?= csrf_field() ?>
        <div class="row g-3">
            <div class="col-md-7">
                <label for="title" class="form-label fw-semibold">Expense Title</label>
                <input type="text" class="form-control<?= isset($expenseErrors['expense_title']) ? ' is-invalid' : '' ?>" id="title" name="expense_title" value="<?= esc(old('expense_title')) ?>">
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
                <textarea class="form-control" id="expense_notes" name="expense_notes" rows="3"><?= esc(old('expense_notes')) ?></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-evergreen">Add Expense</button>
            </div>
        </div>
    </form>
</div>

<div class="card-surface">
    <h2 class="h4 fw-bold mb-3">Expense Ledger</h2>
    <?php if ($expenses === []): ?>
        <p class="text-secondary mb-0">No expenses added yet for this match.</p>
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
                        <tr>
                            <td>
                                <div class="fw-semibold"><?= esc($expense['title']) ?></div>
                                <?php if (! empty($expense['notes'])): ?>
                                    <div class="text-secondary small"><?= esc($expense['notes']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td><?= esc(number_format((float) $expense['amount'], 2)) ?></td>
                            <td>
                                <?= esc($expense['contributors'] === [] ? 'No match contributors' : 'Shared across ' . count($expense['contributors']) . ' match contributors') ?>
                                <details class="mt-2" <?= $expenseEditId === (int) $expense['id'] ? 'open' : '' ?>>
                                    <summary class="small text-success" role="button">Edit</summary>
                                    <div class="mt-3 d-flex gap-2 align-items-start">
                                        <form action="<?= site_url('/admin/matches/' . $match['id'] . '/expenses/' . $expense['id'] . '/update') ?>" method="post" class="flex-grow-1">
                                            <?= csrf_field() ?>
                                            <div class="row g-2">
                                                <div class="col-12">
                                                    <input type="text" class="form-control form-control-sm<?= $expenseEditId === (int) $expense['id'] && isset($expenseUpdateErrors['expense_title']) ? ' is-invalid' : '' ?>" name="expense_title" value="<?= esc($expenseEditId === (int) $expense['id'] ? old('expense_title', $expense['title']) : $expense['title']) ?>" placeholder="Expense title">
                                                    <?php if ($expenseEditId === (int) $expense['id'] && isset($expenseUpdateErrors['expense_title'])): ?>
                                                        <div class="invalid-feedback"><?= esc($expenseUpdateErrors['expense_title']) ?></div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-12">
                                                    <input type="number" step="0.01" min="0.01" class="form-control form-control-sm<?= $expenseEditId === (int) $expense['id'] && isset($expenseUpdateErrors['expense_amount']) ? ' is-invalid' : '' ?>" name="expense_amount" value="<?= esc($expenseEditId === (int) $expense['id'] ? old('expense_amount', $expense['amount']) : $expense['amount']) ?>" placeholder="Amount">
                                                    <?php if ($expenseEditId === (int) $expense['id'] && isset($expenseUpdateErrors['expense_amount'])): ?>
                                                        <div class="invalid-feedback"><?= esc($expenseUpdateErrors['expense_amount']) ?></div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-12">
                                                    <textarea class="form-control form-control-sm" name="expense_notes" rows="2" placeholder="Notes"><?= esc($expenseEditId === (int) $expense['id'] ? old('expense_notes', $expense['notes']) : $expense['notes']) ?></textarea>
                                                </div>
                                                <div class="col-12">
                                                    <button type="submit" class="btn btn-sm btn-evergreen">Save</button>
                                                </div>
                                            </div>
                                        </form>
                                        <form action="<?= site_url('/admin/matches/' . $match['id'] . '/expenses/' . $expense['id'] . '/delete') ?>" method="post" onsubmit="return confirm('Delete this expense?');">
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