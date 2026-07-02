<?= $this->extend('admin/template/default') ?>

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
            <div class="summary-card p-4 h-100">
                <div class="summary-label">Total Expenses</div>
                <div class="summary-value"><?= esc(number_format($totalOwed, 2)) ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-card p-4 h-100">
                <div class="summary-label">Deposited</div>
                <div class="summary-value"><?= esc(number_format($totalDeposited, 2)) ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-card p-4 h-100">
                <div class="summary-label">Net Balance</div>
                <div class="summary-value"><?= esc(number_format($totalBalance, 2)) ?></div>
            </div>
        </div>
    </div>

    <div class="panel">
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
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($playerOverview as $entry): ?>
                        <?php $bal = (float) $entry['balance']; ?>
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
                            <td class="text-end <?= $bal > 0 ? 'text-success' : ($bal < 0 ? 'text-danger' : 'text-secondary') ?>">
                                <?= esc(number_format($bal, 2)) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan=" 4" class="text-end">Total</th>
                        <th class="text-end"><?= esc(number_format($totalDeposited, 2)) ?></th>
                        <th class="text-end"><?= esc(number_format($totalOwed, 2)) ?></th>
                        <th class="text-end"><?= esc(number_format($totalBalance, 2)) ?></th>
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
        <?php if ($matchExpenseSummary === [] && $generalExpenses === []): ?>
            <p class="text-secondary mb-0">No expenses recorded yet.</p>
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
            <?php if ($generalExpenses !== []): ?>
                <?php
                $totalGeneralExpense = array_sum(array_map(static fn($e) => (float) ($e['amount'] ?? 0), $generalExpenses));
                ?>
                <h3 class="h6 fw-semibold mt-4 mb-2 text-secondary">General Expenses</h3>
                <div class="table-responsive">
                    <table class="table table-hover align-middle admin-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Description</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($generalExpenses as $ge): ?>
                                <tr>
                                    <td><?= esc($ge['title'] ?? '—') ?></td>
                                    <td><?= esc($ge['notes'] ?? '—') ?></td>
                                    <td class="text-end"><?= esc(number_format((float) ($ge['amount'] ?? 0), 2)) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" class="text-end">Total</th>
                                <th class="text-end"><?= esc(number_format($totalGeneralExpense, 2)) ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>