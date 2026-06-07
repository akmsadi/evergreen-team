<?= $this->extend('admin/default') ?>

<?= $this->section('title') ?>Admin Dashboard<?= $this->endSection() ?>

<?= $this->section('admin_nav') ?>dashboard<?= $this->endSection() ?>

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
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$totalMatches = array_sum(array_map(static fn(array $point): int => (int) $point['count'], $monthlyMatchCounts));
?>
<div class="content-card">
    <div class="d-flex align-items-start justify-content-between">
        <div>
            <h1 class="h2 mb-2">Welcome, <?= esc($username) ?></h1>
            <p class="text-secondary mb-4">You are signed in to the admin area.</p>
        </div>
        <a href="<?= site_url('/admin/backup') ?>" class="btn btn-primary">Backup Database</a>
    </div>
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
        <div class="recent-panel-header">
            <div>
                <h2 class="h4 mb-2">Recent Matches</h2>
                <p class="text-secondary mb-0">Latest scheduled or created matches.</p>
            </div>
            <a href="<?= site_url('/admin/matches') ?>" class="btn btn-sm btn-primary">All</a>
        </div>
        <?php if ($recentMatches !== []): ?>
            <div class="table-responsive">
                <table class="table table-sm table-hover dashboard-table">
                    <thead>
                        <tr>
                            <th>Match</th>
                            <th>Venue</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentMatches as $match): ?>
                            <tr>
                                <td><?= esc($match['team_name']) ?> vs <?= esc($match['opponent_name']) ?></td>
                                <td><?= esc($match['venue'] ?: 'Venue TBD') ?></td>
                                <td><?= esc(getFormattedDate($match['scheduled_at'] ?? null, 'Not scheduled')) ?></td>
                                <td><?= esc(ucwords(str_replace('_', ' ', $match['match_status'] ?? 'draft'))) ?></td>
                                <td class="text-end"><a href="<?= site_url('/admin/matches/' . $match['id'] . '/edit') ?>" class="btn btn-sm btn-primary">Open</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-secondary mb-0">No matches recorded yet.</p>
        <?php endif; ?>
    </div>
    <div class="panel mb-8">
        <div class="recent-panel-header">
            <div>
                <h2 class="h4 mb-2">Recent Players</h2>
                <p class="text-secondary mb-0">Newest player registrations.</p>
            </div>
            <a href="<?= site_url('/admin/players') ?>" class="btn btn-sm btn-primary">All</a>
        </div>
        <?php if ($recentPlayers !== []): ?>
            <div class="table-responsive">
                <table class="table table-sm table-hover dashboard-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Added</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentPlayers as $player): ?>
                            <tr>
                                <td><?= esc($player['name']) ?></td>
                                <td><?= esc($player['email'] ?: 'No email') ?></td>
                                <td><?= esc(ucfirst((string) ($player['status'] ?? 'pending'))) ?></td>
                                <td><?= esc(getFormattedDate($player['created_at'] ?? null, 'recently')) ?></td>
                                <td class="text-end"><a href="<?= site_url('/admin/players/' . $player['id'] . '/edit') ?>" class="btn btn-sm btn-primary">Open</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-secondary mb-0">No players added yet.</p>
        <?php endif; ?>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="panel">
                <h2 class="h4 mb-2">Top 10 Batsmen</h2>
                <p class="text-secondary mb-3">Ranked by total runs across recorded match balls.</p>
                <?php if ($topBatsmen !== []): ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover dashboard-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Runs</th>
                                    <th>Balls</th>
                                    <th>4s</th>
                                    <th>6s</th>
                                    <th>SR</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topBatsmen as $index => $batsman): ?>
                                    <tr>
                                        <td><?= esc((string) ($index + 1)) ?></td>
                                        <td><?= esc($batsman['name']) ?></td>
                                        <td><?= esc((string) $batsman['runs']) ?></td>
                                        <td><?= esc((string) $batsman['balls']) ?></td>
                                        <td><?= esc((string) $batsman['fours']) ?></td>
                                        <td><?= esc((string) $batsman['sixes']) ?></td>
                                        <td><?= $batsman['strike_rate'] !== null ? esc(number_format((float) $batsman['strike_rate'], 2)) : '-' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-secondary mb-0">No batting data available yet.</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="panel">
                <h2 class="h4 mb-2">Top 10 Bowlers</h2>
                <p class="text-secondary mb-3">Ranked by wickets, then economy rate.</p>
                <?php if ($topBowlers !== []): ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover dashboard-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Wickets</th>
                                    <th>Overs</th>
                                    <th>Runs</th>
                                    <th>Economy</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topBowlers as $index => $bowler): ?>
                                    <tr>
                                        <td><?= esc((string) ($index + 1)) ?></td>
                                        <td><?= esc($bowler['name']) ?></td>
                                        <td><?= esc((string) $bowler['wickets']) ?></td>
                                        <td><?= esc($bowler['overs']) ?></td>
                                        <td><?= esc((string) $bowler['runs']) ?></td>
                                        <td><?= $bowler['economy'] !== null ? esc(number_format((float) $bowler['economy'], 2)) : '-' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-secondary mb-0">No bowling data available yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chartElement = document.getElementById('monthly-matches-chart');

        if (!chartElement || typeof Chart === 'undefined') {
            return;
        }

        const chartRows = <?= json_encode(array_map(static function (array $point): array {
                                $label = (string) $point['label'];
                                $count = (int) $point['count'];

                                return [$label, $count, sprintf('%s: %d match%s', $label, $count, $count === 1 ? '' : 'es')];
                            }, $monthlyMatchCounts), JSON_THROW_ON_ERROR) ?>;
        const labels = chartRows.map(function(row) {
            return row[0];
        });

        const counts = chartRows.map(function(row) {
            return row[1];
        });

        const tooltipLabels = chartRows.map(function(row) {
            return row[2];
        });

        new Chart(chartElement, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Matches',
                    data: counts,
                    backgroundColor: '#1f7a3d',
                    borderColor: '#14532d',
                    borderWidth: 1,
                    borderRadius: 8,
                    borderSkipped: false,
                    barPercentage: 0.68,
                    categoryPercentage: 0.8
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
                            label: function(context) {
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
                            color: '#14532d',
                            font: {
                                weight: 'bold'
                            }
                        },
                        ticks: {
                            color: '#374151',
                            maxRotation: window.innerWidth < 768 ? 45 : 0,
                            minRotation: window.innerWidth < 768 ? 45 : 0
                        },
                        grid: {
                            display: false
                        },
                        border: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            color: '#374151'
                        },
                        title: {
                            display: true,
                            text: 'Matches',
                            color: '#14532d',
                            font: {
                                weight: 'bold'
                            }
                        },
                        grid: {
                            color: '#d1d5db'
                        },
                        border: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
<?= $this->endSection() ?>