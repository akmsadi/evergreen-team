<?= $this->extend('admin/template/default') ?>

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
                <h2 class="h4 mb-1">Player Accounts</h2>
                <p class="text-secondary mb-0">Review total recorded expenses for each player from the accounts overview.</p>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle admin-table">
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
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($playerOverview as $entry): ?>
                        <tr>
                            <td><?= esc($entry['player']['name']) ?></td>
                            <td><?= esc($entry['player']['phone']) ?></td>
                            <td>
                                <?php $playerStatus = $entry['player']['status'] ?? ''; ?>
                                <?php if ($playerStatus === 'approved'): ?>
                                    <span class="badge-soft-success">Approved</span>
                                <?php elseif ($playerStatus === 'guest'): ?>
                                    <span class="badge-soft-info">Guest</span>
                                <?php elseif ($playerStatus === 'pending'): ?>
                                    <span class="badge-soft-warning">Pending</span>
                                <?php elseif ($playerStatus === 'rejected'): ?>
                                    <span class="badge-soft-danger">Rejected</span>
                                <?php else: ?>
                                    <span class="text-capitalize"><?= esc($playerStatus) ?></span>
                                <?php endif; ?>
                            </td>
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
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="panel mt-6">
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
                <table class="table table-hover align-middle admin-table">
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
</div>
<?= $this->endSection() ?>