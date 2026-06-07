<?= $this->extend('admin/default') ?>

<?= $this->section('title') ?>Accounts<?= $this->endSection() ?>

<?= $this->section('admin_nav') ?>accounts<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style {csp-style-nonce}>
    .account-summary-card {
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 1.25rem;
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
    }

    .account-summary-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at top right, rgba(255, 255, 255, 0.45), transparent 42%);
        pointer-events: none;
    }

    .account-summary-card__label {
        color: rgba(15, 23, 42, 0.72);
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .account-summary-card__value {
        color: #0f172a;
        font-size: clamp(2rem, 4vw, 2.75rem);
        font-weight: 700;
        line-height: 1;
        margin-top: 0.5rem;
    }

    .account-summary-card__note {
        color: rgba(15, 23, 42, 0.72);
        font-size: 0.9rem;
        margin-top: 0.75rem;
    }

    .account-summary-card--deposited {
        background: linear-gradient(145deg, #eefbf3 0%, #dff5e8 58%, #d3eddd 100%);
    }

    .account-summary-card--owed {
        background: linear-gradient(145deg, #fff8e7 0%, #ffefc7 58%, #fde5a0 100%);
    }

    .account-summary-card--balance {
        background: linear-gradient(145deg, #eff6ff 0%, #dbeafe 58%, #bfdbfe 100%);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php $depositErrors = session()->getFlashdata('deposit_errors') ?? []; ?>
<?php $depositUpdateErrors = session()->getFlashdata('deposit_update_errors') ?? []; ?>
<?php $depositModal = session()->getFlashdata('deposit_modal') ?? null; ?>
<?php $depositEditId = (int) (session()->getFlashdata('deposit_edit_id') ?? 0); ?>
<?php $totalDeposited = array_sum(array_map(static fn(array $entry): float => (float) ($entry['deposited'] ?? 0), $playerOverview)); ?>
<?php $totalOwed = array_sum(array_map(static fn(array $entry): float => (float) ($entry['owed'] ?? 0), $playerOverview)); ?>
<?php $totalBalance = array_sum(array_map(static fn(array $entry): float => (float) ($entry['balance'] ?? 0), $playerOverview)); ?>
<?php $totalMatchExpenseItems = array_sum(array_map(static fn(array $entry): int => (int) ($entry['expense_count'] ?? 0), $matchExpenseSummary)); ?>
<?php $totalMatchExpenseContributors = array_sum(array_map(static fn(array $entry): int => (int) ($entry['contributor_count'] ?? 0), $matchExpenseSummary)); ?>
<?php $totalMatchExpenses = array_sum(array_map(static fn(array $entry): float => (float) ($entry['total_expense'] ?? 0), $matchExpenseSummary)); ?>
<?php $totalLedgerDeposits = array_sum(array_map(static fn(array $entry): float => (float) ($entry['amount'] ?? 0), $deposits)); ?>

<div class="content-card">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="h2 mb-2">Accounts</h1>
            <p class="text-secondary mb-0">Manage player balances, wallet deposits, assigned expenses, and payment status from one place.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createDepositModal">Add Deposit</button>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="summary-card account-summary-card account-summary-card--owed p-4 h-100">
                <div class="account-summary-card__label">Total Expenses</div>
                <div class="account-summary-card__value"><?= esc(number_format($totalOwed, 2)) ?></div>
                <div class="account-summary-card__note">Total expenses from matches.</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-card account-summary-card account-summary-card--deposited p-4 h-100">
                <div class="account-summary-card__label">Deposited</div>
                <div class="account-summary-card__value"><?= esc(number_format($totalDeposited, 2)) ?></div>
                <div class="account-summary-card__note">Total player deposits recorded.</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-card account-summary-card account-summary-card--balance p-4 h-100">
                <div class="account-summary-card__label">Net Balance</div>
                <div class="account-summary-card__value"><?= esc(number_format($totalBalance, 2)) ?></div>
                <div class="account-summary-card__note">Positive means overpaid, negative means still outstanding.</div>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
            <div>
                <h2 class="h4 mb-1">Match Expenses</h2>
                <p class="text-secondary mb-0">Review total recorded expenses for each match from the accounts overview.</p>
            </div>
        </div>
        <?php if ($matchExpenseSummary === []): ?>
            <p class="text-secondary mb-0">No match expenses recorded yet.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle admin-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Match</th>
                            <th>Venue</th>
                            <th>Status</th>
                            <th class="text-end">Expense Items</th>
                            <th class="text-end">Contributors</th>
                            <th class="text-end">Total Expense</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($matchExpenseSummary as $matchExpense): ?>
                            <tr>
                                <td><?= esc(getFormattedDate($matchExpense['scheduled_at'] ?? null, '-')) ?></td>
                                <td>
                                    <div class="fw-semibold"><?= esc($matchExpense['team_name']) ?></div>
                                    <div class="text-secondary small">vs <?= esc($matchExpense['opponent_name']) ?></div>
                                </td>
                                <td><?= esc($matchExpense['venue'] ?: '-') ?></td>
                                <td class="text-capitalize"><?= esc($matchExpense['match_status']) ?></td>
                                <td class="text-end"><?= esc((string) $matchExpense['expense_count']) ?></td>
                                <td class="text-end"><?= esc((string) $matchExpense['contributor_count']) ?></td>
                                <td class="text-end"><?= esc(number_format((float) $matchExpense['total_expense'], 2)) ?></td>
                                <td class="text-end">
                                    <a href="<?= site_url('/admin/matches/' . $matchExpense['match_id']) ?>" class="btn btn-sm btn-primary">Open Match</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-end">Total</th>
                            <th class="text-end"><?= esc((string) $totalMatchExpenseItems) ?></th>
                            <th class="text-end"><?= esc((string) $totalMatchExpenseContributors) ?></th>
                            <th class="text-end"><?= esc(number_format($totalMatchExpenses, 2)) ?></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <div class="panel mt-6">
        <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
            <div>
                <h2 class="h4 mb-1">Deposit Ledger</h2>
                <p class="text-secondary mb-0">All player wallet deposits are managed here.</p>
            </div>
        </div>
        <?php if ($deposits === []): ?>
            <p class="text-secondary mb-0">No player deposits recorded yet.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle admin-table">
                    <thead>
                        <tr>
                            <th>Player</th>
                            <th>Amount</th>
                            <th>Note</th>
                            <th>Created</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($deposits as $deposit): ?>
                            <tr>
                                <td><?= esc($deposit['name']) ?></td>
                                <td><?= esc(number_format((float) $deposit['amount'], 2)) ?></td>
                                <td><?= esc($deposit['notes'] ?: '-') ?></td>
                                <td><?= esc(getFormattedDate($deposit['created_at'] ?? null, '-')) ?></td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-primary btn-icon" data-bs-toggle="modal" data-bs-target="#editDepositModal<?= esc((string) $deposit['id']) ?>" aria-label="Edit deposit" title="Edit deposit">
                                            <i class="ti ti-pencil"></i>
                                        </button>
                                        <form action="<?= site_url('/admin/players/deposits/' . $deposit['id'] . '/delete') ?>" method="post" onsubmit="return confirm('Delete this deposit?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger btn-icon" aria-label="Delete deposit" title="Delete deposit">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="text-end">Total</th>
                            <th><?= esc(number_format($totalLedgerDeposits, 2)) ?></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <div class="panel mt-6">
        <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
            <div>
                <h2 class="h4 mb-1">Player Accounts</h2>
                <p class="text-secondary mb-0">Review total recorded expenses for each player from the accounts overview.</p>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle admin-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Matches</th>
                        <th class="text-end">Deposited</th>
                        <th class="text-end">Owed</th>
                        <th class="text-end">Balance</th>
                        <th>Payment Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($playerOverview as $entry): ?>
                        <tr>
                            <td><?= esc($entry['player']['name']) ?></td>
                            <td><?= esc($entry['player']['phone']) ?></td>
                            <td class="text-capitalize"><?= esc($entry['player']['status']) ?></td>
                            <td><?= esc((string) $entry['match_count']) ?></td>
                            <td class="text-end"><?= esc(number_format((float) $entry['deposited'], 2)) ?></td>
                            <td class="text-end"><?= esc(number_format((float) $entry['owed'], 2)) ?></td>
                            <td class="text-end"><?= esc(number_format((float) $entry['balance'], 2)) ?></td>
                            <td>
                                <?php if ($entry['status'] === 'overpaid'): ?>
                                    <span class="badge-soft-success">Overpaid</span>
                                <?php elseif ($entry['status'] === 'underpaid'): ?>
                                    <span class="badge-soft-danger">Underpaid</span>
                                <?php else: ?>
                                    <span class="badge-soft-warning">Settled</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-2">
                                    <a href="<?= site_url('/admin/players/' . $entry['player']['id'] . '/edit') ?>" class="btn btn-sm btn-primary">Edit</a>
                                    <?php if (($entry['player']['status'] ?? '') === 'approved'): ?>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-evergreen"
                                            data-bs-toggle="modal"
                                            data-bs-target="#createDepositModal"
                                            data-player-id="<?= esc((string) $entry['player']['id']) ?>"
                                            data-player-name="<?= esc($entry['player']['name']) ?>">Deposit</button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-end">Total</th>
                        <th class="text-end"><?= esc(number_format($totalDeposited, 2)) ?></th>
                        <th class="text-end"><?= esc(number_format($totalOwed, 2)) ?></th>
                        <th class="text-end"><?= esc(number_format($totalBalance, 2)) ?></th>
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="createDepositModal" tabindex="-1" aria-labelledby="createDepositModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('/admin/players/deposits') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="createDepositModalLabel">Add Player Deposit</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="create_deposit_player_id" class="form-label fw-semibold">Player</label>
                        <select class="form-select<?= isset($depositErrors['deposit_player_id']) ? ' is-invalid' : '' ?>" id="create_deposit_player_id" name="deposit_player_id">
                            <option value="">Select player</option>
                            <?php foreach ($approvedPlayers as $player): ?>
                                <option value="<?= esc((string) $player['id']) ?>" <?= old('deposit_player_id') == $player['id'] ? 'selected' : '' ?>><?= esc($player['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($depositErrors['deposit_player_id'])): ?>
                            <div class="invalid-feedback"><?= esc($depositErrors['deposit_player_id']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="create_deposit_amount" class="form-label fw-semibold">Amount</label>
                        <input type="number" step="0.01" min="0.01" class="form-control<?= isset($depositErrors['deposit_amount']) ? ' is-invalid' : '' ?>" id="create_deposit_amount" name="deposit_amount" value="<?= esc(old('deposit_amount')) ?>">
                        <?php if (isset($depositErrors['deposit_amount'])): ?>
                            <div class="invalid-feedback"><?= esc($depositErrors['deposit_amount']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label for="create_deposit_notes" class="form-label fw-semibold">Ledger Note</label>
                        <textarea class="form-control" id="create_deposit_notes" name="deposit_notes" rows="3"><?= esc(old('deposit_notes')) ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-evergreen">Save Deposit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php foreach ($deposits as $deposit): ?>
    <div class="modal fade" id="editDepositModal<?= esc((string) $deposit['id']) ?>" tabindex="-1" aria-labelledby="editDepositModalLabel<?= esc((string) $deposit['id']) ?>" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="<?= site_url('/admin/players/deposits/' . $deposit['id'] . '/update') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="modal-header">
                        <h2 class="modal-title fs-5" id="editDepositModalLabel<?= esc((string) $deposit['id']) ?>">Edit Deposit</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_deposit_player_id_<?= esc((string) $deposit['id']) ?>" class="form-label fw-semibold">Player</label>
                            <select class="form-select<?= $depositEditId === (int) $deposit['id'] && isset($depositUpdateErrors['deposit_player_id']) ? ' is-invalid' : '' ?>" id="edit_deposit_player_id_<?= esc((string) $deposit['id']) ?>" name="deposit_player_id">
                                <?php foreach ($players as $player): ?>
                                    <?php $selectedPlayerId = $depositEditId === (int) $deposit['id'] ? old('deposit_player_id', $deposit['player_id']) : $deposit['player_id']; ?>
                                    <option value="<?= esc((string) $player['id']) ?>" <?= (string) $selectedPlayerId === (string) $player['id'] ? 'selected' : '' ?>><?= esc($player['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if ($depositEditId === (int) $deposit['id'] && isset($depositUpdateErrors['deposit_player_id'])): ?>
                                <div class="invalid-feedback"><?= esc($depositUpdateErrors['deposit_player_id']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="edit_deposit_amount_<?= esc((string) $deposit['id']) ?>" class="form-label fw-semibold">Amount</label>
                            <input type="number" step="0.01" min="0.01" class="form-control<?= $depositEditId === (int) $deposit['id'] && isset($depositUpdateErrors['deposit_amount']) ? ' is-invalid' : '' ?>" id="edit_deposit_amount_<?= esc((string) $deposit['id']) ?>" name="deposit_amount" value="<?= esc($depositEditId === (int) $deposit['id'] ? old('deposit_amount', $deposit['amount']) : $deposit['amount']) ?>">
                            <?php if ($depositEditId === (int) $deposit['id'] && isset($depositUpdateErrors['deposit_amount'])): ?>
                                <div class="invalid-feedback"><?= esc($depositUpdateErrors['deposit_amount']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label for="edit_deposit_notes_<?= esc((string) $deposit['id']) ?>" class="form-label fw-semibold">Ledger Note</label>
                            <textarea class="form-control" id="edit_deposit_notes_<?= esc((string) $deposit['id']) ?>" name="deposit_notes" rows="3"><?= esc($depositEditId === (int) $deposit['id'] ? old('deposit_notes', $deposit['notes']) : $deposit['notes']) ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-evergreen">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const createDepositModal = document.getElementById('createDepositModal');

        if (createDepositModal) {
            createDepositModal.addEventListener('show.bs.modal', function(event) {
                const trigger = event.relatedTarget;
                const playerIdField = document.getElementById('create_deposit_player_id');

                if (!trigger || !playerIdField) {
                    return;
                }

                const playerId = trigger.getAttribute('data-player-id');

                if (playerId) {
                    playerIdField.value = playerId;
                }
            });
        }

        <?php if ($depositModal === 'create'): ?>
            bootstrap.Modal.getOrCreateInstance(document.getElementById('createDepositModal')).show();
        <?php elseif ($depositModal === 'edit' && $depositEditId > 0): ?>
            bootstrap.Modal.getOrCreateInstance(document.getElementById('editDepositModal<?= esc((string) $depositEditId) ?>')).show();
        <?php endif; ?>
    });
</script>
<?= $this->endSection() ?>