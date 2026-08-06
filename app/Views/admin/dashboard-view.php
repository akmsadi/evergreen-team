<?= $this->extend('admin/template/default') ?>

<?= $this->section('styles') ?>
<style {csp-style-nonce}>
    .recent-panel-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .dashboard-table {
        --bs-table-bg: transparent;
        --bs-table-striped-bg: rgba(31, 122, 61, 0.04);
        --bs-table-hover-bg: rgba(31, 122, 61, 0.06);
        margin-bottom: 0;
    }

    .dashboard-table th {
        color: #4b5563;
        font-size: 0.78rem;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .dashboard-table td {
        vertical-align: middle;
    }

    .dashboard-table-caption {
        color: #4b5563;
        font-size: 0.85rem;
    }

    .match-chart-shell {
        height: 420px;
    }

    .match-chart {
        width: 100%;
        height: 100%;
        display: block;
    }

    .match-chart-note {
        font-size: 0.78rem;
        color: #4b5563;
        margin-top: 0.75rem;
    }

    .summary-value {
        font-weight: 400;
    }

    @media (max-width: 767.98px) {
        .match-chart-shell {
            height: 340px;
        }

        .match-chart {
            height: 100%;
        }
    }

    .leaderboard-panel {
        /* border-radius: 14px; */
        padding: 1.0rem;
        color: #f8fafc;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
        height: 100%;
    }

    .leaderboard-panel--batting {
        background: linear-gradient(160deg, #081a35 0%, #0f2a52 55%, #163e70 100%);
    }

    .leaderboard-panel--bowling {
        background: linear-gradient(160deg, #041d0d 0%, #0a3618 55%, #114a22 100%);
    }

    .leaderboard-head {
        text-align: center;
        margin-bottom: 10px;
    }

    .leaderboard-title {
        font-size: 1.5rem;
        font-weight: 800 !important;
        letter-spacing: 0.03em;
        color: #fff;
        text-transform: uppercase;
        margin-bottom: 0.2rem;
    }

    .leaderboard-subtitle {
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #ffd447;
        margin-bottom: 0;
    }

    .leaderboard-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 0.35rem;
        color: #e5edf7;
        font-size: 1.1rem;
        margin-bottom: 0;
    }

    .leaderboard-table thead th {
        background: rgba(255, 255, 255, 0.14);
        color: #dbe4f0;
        text-transform: uppercase;
        font-size: 0.88rem;
        letter-spacing: 0.05em;
        font-weight: 700 !important;
        padding: 0.55rem 0.6rem;
        border: none;
        white-space: nowrap;
    }

    .leaderboard-table thead th:first-child {
        border-radius: 6px 0 0 6px;
    }

    .leaderboard-table thead th:last-child {
        border-radius: 0 6px 6px 0;
    }

    .leaderboard-table tbody tr {
        background: rgba(255, 255, 255, 0.05);
    }

    .leaderboard-table tbody tr:nth-child(even) {
        background: rgba(255, 255, 255, 0.1);
    }

    .leaderboard-table td {
        padding: 0.5rem 0.6rem;
        vertical-align: middle;
        border: none;
        white-space: nowrap;
    }

    .leaderboard-table td:first-child {
        border-radius: 6px 0 0 6px;
    }

    .leaderboard-table td:last-child {
        border-radius: 0 6px 6px 0;
    }

    .leaderboard-table td a {
        color: #fff;
        font-weight: 600 !important;
        text-decoration: none;
    }

    .leaderboard-table td a:hover {
        text-decoration: underline;
    }

    .rank-chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 1.9rem;
        height: 1.9rem;
        border-radius: 5px;
        font-weight: 700 !important;
        font-size: 1rem;
        color: #fff;
    }

    .leaderboard-panel--batting .rank-chip {
        background: #1c6fd1;
    }

    .leaderboard-panel--bowling .rank-chip {
        background: #2f9e46;
    }

    .leaderboard-stat {
        font-weight: 800 !important;
        font-size: 1.3rem;
        color: #ffd447;
    }

    .leaderboard-empty {
        color: #dbe4f0;
        margin-bottom: 0;
        text-align: center;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php $totalMatches = array_sum(array_map(static fn(array $point): int => (int) $point['count'], $monthlyMatchCounts)); ?>
<div class="content-card">
    <div class="panel mb-8">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
            <div>
                <h2 class="h4 mb-2">Matches by Month</h2>
                <p class="text-secondary mb-0">Last 12 months of scheduled matches.</p>
            </div>
            <div class="text-end">
                <div class="summary-value"><?= esc((string) $totalMatches) ?></div>
                <div class="text-secondary small">Matches in period</div>
            </div>
        </div>
        <div class="match-chart-shell">
            <canvas id="monthly-matches-chart" class="match-chart" aria-label="Bar chart of matches by month for the last 12 months"></canvas>
            <p class="match-chart-note mb-0">X axis: month. Y axis: matches.</p>
        </div>
    </div>
    <div class="panel mb-8">
        <?php $totalExpense = array_sum(array_column($matchExpenseChart, 'amount')); ?>
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
            <div>
                <h2 class="h4 mb-2">Expenses by Match</h2>
                <p class="text-secondary mb-0">Total expenses for last 10 matches.</p>
            </div>
            <div class="text-end">
                <div class="summary-value"><?= esc(number_format($totalExpense, 2)) ?></div>
                <div class="text-secondary small">Total in period</div>
            </div>
        </div>
        <?php if ($matchExpenseChart !== []): ?>
            <div class="match-chart-shell">
                <canvas id="match-expense-chart" class="match-chart" aria-label="Bar chart of expenses per match"></canvas>
                <p class="match-chart-note mb-0">X axis: match. Y axis: expenses.</p>
            </div>
        <?php else: ?>
            <p class="text-secondary mb-0">No match expenses recorded yet.</p>
        <?php endif; ?>
    </div>

    <div class="row mb-8">
        <div class="col-lg-6 mb-5 mb-lg-0">
            <div class="panel">
                <div class="recent-panel-header">
                    <div>
                        <h2 class="h4 mb-2">Recent Matches</h2>
                        <p class="text-secondary mb-0">Latest scheduled or created matches.</p>
                    </div>
                </div>
                <?php if ($recentMatches !== []): ?>
                    <div class="table-responsive">
                        <table class="table table-hover dashboard-table">
                            <thead>
                                <tr>
                                    <th>Match</th>
                                    <th>Venue</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentMatches as $match): ?>
                                    <tr>
                                        <td><a href="<?= site_url('/admin/matches/' . $match['id'] . '/edit') ?>"><?= esc($match['team_name']) ?> vs <?= esc($match['opponent_name']) ?></a></td>
                                        <td><?= esc($match['venue'] ?: 'Venue TBD') ?></td>
                                        <td><?= esc(getFormattedDate($match['scheduled_at'] ?? null, 'Not scheduled')) ?></td>
                                        <td><?= esc(ucwords(str_replace('_', ' ', $match['match_status'] ?? 'draft'))) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-secondary mb-0">No matches recorded yet.</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="panel">
                <div class="recent-panel-header">
                    <div>
                        <h2 class="h4 mb-2">Recent Players</h2>
                        <p class="text-secondary mb-0">Newest player registrations.</p>
                    </div>
                </div>
                <?php if ($recentPlayers !== []): ?>
                    <div class="table-responsive">
                        <table class="table table-hover dashboard-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Added</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentPlayers as $player): ?>
                                    <tr>
                                        <td>
                                            <a href="<?= site_url('/admin/players/' . $player['id'] . '/edit') ?>"><?= esc($player['name']) ?></a>
                                        </td>
                                        <td><?= esc($player['email'] ?: 'No email') ?></td>
                                        <td><?= esc(ucfirst((string) ($player['status'] ?? 'pending'))) ?></td>
                                        <td><?= esc(getFormattedDate($player['created_at'] ?? null, 'recently')) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-secondary mb-0">No players added yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="row g-0">
        <div class="col-lg-6 mb-5 mb-lg-0">
            <div class="leaderboard-panel leaderboard-panel--batting">
                <div class="leaderboard-head">
                    <h2 class="leaderboard-title">Top 10 Batsmen</h2>
                    <p class="leaderboard-subtitle">Ranked by total runs across recorded match balls</p>
                </div>
                <?php if ($topBatsmen !== []): ?>
                    <div class="table-responsive">
                        <table class="leaderboard-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Mat</th>
                                    <th>Inn</th>
                                    <th>Runs</th>
                                    <th>Balls</th>
                                    <th>4s</th>
                                    <th>6s</th>
                                    <th>R (B)</th>
                                    <th>SR</th>
                                    <th>Avg</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topBatsmen as $index => $batsman): ?>
                                    <tr>
                                        <td><span class="rank-chip"><?= esc((string) ($index + 1)) ?></span></td>
                                        <td><a target="_blank" href="<?= site_url('admin/players/' . $batsman['player_id'] . '/edit') ?>"><?= esc($batsman['name']) ?></a></td>
                                        <td><?= esc((string) $batsman['matches_played']) ?></td>
                                        <td><?= esc((string) $batsman['innings']) ?></td>
                                        <td><span class="leaderboard-stat"><?= esc((string) $batsman['runs']) ?></span></td>
                                        <td><?= esc((string) $batsman['balls']) ?></td>
                                        <td><?= esc((string) $batsman['fours']) ?></td>
                                        <td><?= esc((string) $batsman['sixes']) ?></td>
                                        <td><?= $batsman['best_score'] !== null ? esc($batsman['best_score']) : '-' ?></td>
                                        <td><?= $batsman['strike_rate'] !== null ? esc(number_format((float) $batsman['strike_rate'], 2)) : '-' ?></td>
                                        <td><?= $batsman['average'] !== null ? esc(number_format((float) $batsman['average'], 2)) : '-' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="leaderboard-empty">No batting data available yet.</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="leaderboard-panel leaderboard-panel--bowling">
                <div class="leaderboard-head">
                    <h2 class="leaderboard-title">Top 10 Bowlers</h2>
                    <p class="leaderboard-subtitle">Ranked by wickets, then economy rate</p>
                </div>
                <?php if ($topBowlers !== []): ?>
                    <div class="table-responsive">
                        <table class="leaderboard-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Matches</th>
                                    <th>Inn</th>
                                    <th>Wickets</th>
                                    <th>Overs</th>
                                    <th>Runs</th>
                                    <th>W / R</th>
                                    <th>Economy</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topBowlers as $index => $bowler): ?>
                                    <tr>
                                        <td><span class="rank-chip"><?= esc((string) ($index + 1)) ?></span></td>
                                        <td><a target="_blank" href="<?= site_url('admin/players/' . $bowler['player_id'] . '/edit') ?>"><?= esc($bowler['name']) ?></a></td>
                                        <td><?= esc((string) $bowler['matches_played']) ?></td>
                                        <td><?= esc((string) $bowler['innings']) ?></td>
                                        <td><span class="leaderboard-stat"><?= esc((string) $bowler['wickets']) ?></span></td>
                                        <td><?= esc($bowler['overs']) ?></td>
                                        <td><?= esc((string) $bowler['runs']) ?></td>
                                        <td><?= $bowler['best_bowling'] !== null ? esc($bowler['best_bowling']) : '-' ?></td>
                                        <td><?= $bowler['economy'] !== null ? esc(number_format((float) $bowler['economy'], 2)) : '-' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="leaderboard-empty">No bowling data available yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chartElement = document.getElementById('monthly-matches-chart');

        if (!chartElement || typeof Chart === 'undefined') {
            return;
        }

        const chartRows = <?= json_encode(array_map(static function (array $point): array {
            $label = (string) $point['label'];
            $count = (int) $point['count'];

            return [$label, $count, sprintf('%s: %d match%s', $label, $count, $count === 1 ? '' : 'es')];
        }, $monthlyMatchCounts), JSON_THROW_ON_ERROR) ?>;
        const labels = chartRows.map(function (row) {
            return row[0];
        });

        const counts = chartRows.map(function (row) {
            return row[1];
        });

        const tooltipLabels = chartRows.map(function (row) {
            return row[2];
        });

        new Chart(chartElement, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Matches',
                    data: counts,
                    borderColor: '#1f7a3d',
                    borderWidth: 2,
                    backgroundColor: 'rgba(31, 122, 61, 0.15)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#1f7a3d',
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return tooltipLabels[context.dataIndex] ?? '';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Month',
                            font: { weight: 'bold' }
                        },
                        ticks: {
                            color: '#374151',
                            maxRotation: window.innerWidth < 768 ? 45 : 0,
                            minRotation: window.innerWidth < 768 ? 45 : 0
                        },
                        grid: { display: false },
                        border: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0, color: '#374151' },
                        title: {
                            display: true,
                            text: 'Matches',
                            font: { weight: 'bold' }
                        },
                        grid: { color: '#d1d5db' },
                        border: { display: false }
                    }
                }
            }
        });
    });

    (function () {
        const expenseCanvas = document.getElementById('match-expense-chart');
        if (!expenseCanvas || typeof Chart === 'undefined') { return; }

        const expenseData = <?= json_encode(array_map(static fn(array $r): array => [
            $r['label'],
            $r['amount'],
        ], $matchExpenseChart), JSON_THROW_ON_ERROR) ?>;

        new Chart(expenseCanvas, {
            type: 'line',
            data: {
                labels: expenseData.map(function (r) { return r[0]; }),
                datasets: [{
                    label: 'Expenses',
                    data: expenseData.map(function (r) { return r[1]; }),
                    borderColor: '#1f7a3d',
                    borderWidth: 2,
                    backgroundColor: 'rgba(31, 122, 61, 0.15)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#1f7a3d',
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                return 'Expenses: ' + ctx.parsed.y.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: { display: true, text: 'Match', font: { weight: 'bold' } },
                        ticks: { color: '#374151', maxRotation: 45, minRotation: 45 },
                        grid: { display: false },
                        border: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Expenses', font: { weight: 'bold' } },
                        ticks: { color: '#374151' },
                        grid: { color: '#d1d5db' },
                        border: { display: false }
                    }
                }
            }
        });
    }());
</script>
<?= $this->endSection() ?>