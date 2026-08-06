<?= $this->extend('admin/template/default') ?>

<?= $this->section('admin_nav') ?>accounts<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style {csp-style-nonce}>
    .finance-table-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .finance-table {
        border-collapse: collapse;
        white-space: nowrap;
        font-size: 0.85rem;
    }

    .finance-table th,
    .finance-table td {
        border: 1px solid #dee2e6;
        padding: 0.4rem 0.65rem;
        vertical-align: middle;
    }

    .finance-table thead th {
        background: #f1f5f9;
        font-weight: 700;
        text-align: center;
    }

    .finance-table .col-sticky {
        position: sticky;
        background: #fff;
        z-index: 2;
    }

    .finance-table thead .col-sticky {
        background: #f1f5f9;
        z-index: 3;
    }

    .finance-table .col-sn {
        left: 0;
        min-width: 2.5rem;
    }

    .finance-table .col-name {
        left: 2.5rem;
        min-width: 10rem;
    }

    .finance-table .match-header {
        background: #e8f0fe;
        text-align: center;
    }

    .finance-table .match-expense {
        background: #f8faff;
        text-align: center;
        font-weight: 600;
        color: #374151;
    }

    .finance-table .cell-share {
        text-align: right;
        color: #1e40af;
    }

    .finance-table .cell-empty {
        background: #f9fafb;
    }

    .finance-table tfoot td,
    .finance-table tfoot th {
        background: #f1f5f9;
        font-weight: 700;
    }

    .balance-negative {
        color: #dc2626;
        font-weight: 600;
    }

    .balance-positive {
        color: #16a34a;
        font-weight: 600;
    }

    .balance-zero {
        color: #6b7280;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$totalDeposited = array_sum(array_column($playerRows, 'deposited'));
$totalOwed = array_sum(array_column($playerRows, 'owed'));
$totalBalance = round($totalDeposited - $totalOwed, 2);
?>
<div class="content-card">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="h2 mb-2">Finance Overview</h1>
            <p class="text-secondary mb-0">Player contributions, expenses, and per-match cost breakdown for all approved players.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="<?= site_url('/admin/accounts') ?>" class="btn btn-light border">Accounts</a>
        </div>
    </div>

    <?php if ($playerRows === []): ?>
        <p class="text-secondary">No approved players found.</p>
    <?php elseif ($matches === [] && ($generalExpenses ?? []) === []): ?>
        <p class="text-secondary">No matches or expenses recorded yet.</p>
    <?php else: ?>
        <div class="finance-table-wrap">
            <table class="finance-table">
                <thead>
                    <tr>
                        <th class="col-sticky col-sn" rowspan="2">#</th>
                        <th class="col-sticky col-name" rowspan="2">Player Name</th>
                        <th rowspan="2">Organization</th>
                        <th rowspan="2" class="text-end">Total<br>Contribution</th>
                        <th rowspan="2" class="text-end">Total<br>Expenses</th>
                        <th rowspan="2" class="text-end">Balance</th>
                        <?php foreach (($financeColumns ?? []) as $column): ?>
                            <th class="match-header">
                                <?= esc($column['label']) ?><br>
                                <small class="fw-normal text-secondary"><?= esc($column['sub_label']) ?></small>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <?php foreach (($financeColumns ?? []) as $column): ?>
                            <th class="match-expense"><?= esc($column['header_amount_display']) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($playerRows as $i => $row): ?>
                        <tr>
                            <td class="col-sticky col-sn text-center text-secondary"><?= esc((string) ($i + 1)) ?></td>
                            <td class="col-sticky col-name fw-semibold"><?= esc($row['player']['name']) ?></td>
                            <td><?= esc($row['player']['organization'] ?? '') ?></td>
                            <td class="text-end"><?= esc(number_format($row['deposited'], 0)) ?></td>
                            <td class="text-end"><?= esc(number_format($row['owed'], 0)) ?></td>
                            <td class="text-end">
                                <?php $bal = $row['balance']; ?>
                                <span class="<?= $bal < 0 ? 'balance-negative' : ($bal > 0 ? 'balance-positive' : 'balance-zero') ?>">
                                    <?= esc(number_format($bal, 0)) ?>
                                </span>
                            </td>
                            <?php foreach (($financeColumns ?? []) as $column): ?>
                                <?php $share = $row['shares'][$column['key']] ?? null; ?>
                                <?php if ($share !== null): ?>
                                    <td class="cell-share"><?= esc(number_format($share, 0)) ?></td>
                                <?php else: ?>
                                    <td class="cell-empty"></td>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td class="col-sticky col-sn"></td>
                        <td class="col-sticky col-name text-end">Total</td>
                        <td></td>
                        <td class="text-end"><?= esc(number_format($totalDeposited, 0)) ?></td>
                        <td class="text-end"><?= esc(number_format($totalOwed, 0)) ?></td>
                        <td class="text-end">
                            <span class="<?= $totalBalance < 0 ? 'balance-negative' : ($totalBalance > 0 ? 'balance-positive' : 'balance-zero') ?>">
                                <?= esc(number_format($totalBalance, 0)) ?>
                            </span>
                        </td>
                        <?php foreach (($financeColumns ?? []) as $column): ?>
                            <td class="text-end"><?= esc($column['footer_amount_display']) ?></td>
                        <?php endforeach; ?>
                    </tr>
                </tfoot>
            </table>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>