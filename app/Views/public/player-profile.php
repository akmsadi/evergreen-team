<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= esc($player['name']) ?> | Evergreen Player Profile</title>
        <meta name="description" content="Career statistics and match performance for <?= esc($player['name']) ?>.">

        <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('assets/images/favicon/apple-touch-icon.png') ?>">
        <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/images/favicon/favicon-32x32.png') ?>">
        <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/images/favicon/favicon-16x16.png') ?>">
        <link rel="shortcut icon" href="<?= base_url('assets/images/favicon/favicon.ico') ?>">
        <link rel="manifest" href="<?= base_url('assets/images/favicon/site.webmanifest') ?>">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            :root {
                --cg: #1b5e20;
                --cgd: #0d3b13;
                --cgl: #2e7d32;
                --cgs: rgba(255, 255, 255, 0.12);
            }

            * {
                box-sizing: border-box;
            }

            body {
                background: #eef2ee;
                font-family: 'Inter', system-ui, sans-serif;
                min-height: 100vh;
                color: #1a1a1a;
            }

            /* ── Hero ── */
            .pp-hero {
                background: linear-gradient(150deg, var(--cgd) 0%, var(--cg) 55%, var(--cgl) 100%);
                color: #fff;
                padding: 32px 0 26px;
            }

            .pp-eyebrow {
                font-size: 0.76rem;
                font-weight: 700;
                letter-spacing: 0.1em;
                text-transform: uppercase;
                opacity: 0.75;
                margin-bottom: 8px;
            }

            .pp-name {
                font-size: clamp(1.5rem, 4.5vw, 2.4rem);
                font-weight: 800;
                letter-spacing: -0.02em;
                line-height: 1.15;
            }

            .pp-meta {
                margin-top: 6px;
                font-size: 0.85rem;
                opacity: 0.7;
            }

            .pp-scores {
                display: flex;
                gap: 12px;
                margin-top: 22px;
                flex-wrap: wrap;
            }

            .pp-score-card {
                flex: 1;
                min-width: 140px;
                background: var(--cgs);
                border: 1px solid rgba(255, 255, 255, 0.18);
                border-radius: 12px;
                padding: 14px 18px;
            }

            .pp-score-card .stat-label {
                font-size: 0.72rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                opacity: 0.7;
            }

            .pp-score-card .stat-num {
                font-size: 1.8rem;
                font-weight: 800;
                line-height: 1.15;
                margin-top: 3px;
            }

            /* ── Section tabs ── */
            .pp-tabs-bar {
                background: #fff;
                border-bottom: 2px solid #e0e7e0;
                position: sticky;
                top: 0;
                z-index: 100;
            }

            .pp-tabs-bar .nav-link {
                color: #555;
                font-weight: 600;
                font-size: 0.875rem;
                padding: 13px 22px;
                border: none;
                border-bottom: 3px solid transparent;
                border-radius: 0;
                transition: color 0.15s, border-color 0.15s;
                white-space: nowrap;
            }

            .pp-tabs-bar .nav-link.active {
                color: var(--cg);
                border-bottom-color: var(--cg);
                background: none;
            }

            .pp-tabs-bar .nav-link:hover:not(.active) {
                color: var(--cg);
                border-bottom-color: #a5d6a7;
                background: none;
            }

            /* ── Cards ── */
            .pp-card {
                background: #fff;
                border-radius: 12px;
                overflow: hidden;
                box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06), 0 0 0 1px #e5ece5;
                margin-bottom: 16px;
            }

            .pp-card-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 12px 18px;
                background: #f3f8f3;
                border-bottom: 1px solid #e5ece5;
                font-weight: 700;
                font-size: 0.95rem;
                color: #1b5e20;
            }

            .pp-card-body {
                padding: 18px;
            }

            /* ── Tables ── */
            .pp-tbl {
                width: 100%;
                border-collapse: collapse;
                font-size: 0.86rem;
            }

            .pp-tbl thead th {
                background: #f8fbf8;
                color: #888;
                font-size: 0.7rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                padding: 9px 14px;
                border-bottom: 1px solid #e8eee8;
                white-space: nowrap;
            }

            .pp-tbl tbody td {
                padding: 10px 14px;
                border-bottom: 1px solid #f2f5f2;
                vertical-align: middle;
            }

            .pp-tbl tbody tr:last-child td {
                border-bottom: none;
            }

            .pp-tbl tbody tr:hover {
                background: #f9fcf9;
            }

            .pp-tbl .td-name {
                font-weight: 600;
            }

            .scoreboard-link {
                color: inherit;
                text-decoration: none;
                border-bottom: 1px dashed transparent;
            }

            .scoreboard-link:hover {
                color: var(--cg);
                border-bottom-color: var(--cg);
            }

            .pp-tbl .td-num {
                text-align: center;
                width: 60px;
            }

            /* Key-value summary table */
            .kv-tbl th {
                font-weight: 600;
                color: #555;
                width: 55%;
                background: #f8fbf8;
            }

            .kv-tbl td,
            .kv-tbl th {
                padding: 10px 14px;
                border-bottom: 1px solid #f2f5f2;
            }

            .kv-tbl tr:last-child td,
            .kv-tbl tr:last-child th {
                border-bottom: none;
            }

            .story-chip {
                display: inline-flex;
                align-items: center;
                border-radius: 999px;
                padding: 3px 10px;
                background: #eef7ee;
                color: #2a6a33;
                font-size: 0.68rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.06em;
            }

            .no-data {
                padding: 24px;
                text-align: center;
                color: #aaa;
                font-size: 0.85rem;
            }

            .pp-footer {
                text-align: center;
                padding: 24px 16px;
                font-size: 0.76rem;
                color: #aaa;
            }

            @media (max-width: 576px) {
                .pp-score-card .stat-num {
                    font-size: 1.4rem;
                }

                .pp-name {
                    font-size: 1.4rem;
                }
            }
        </style>
    </head>

    <body>
        <?php
        $formatAverage = static fn($value): string => $value === null ? '—' : number_format((float) $value, 2);
        $formatDate = static fn($date): string => $date ? date('d.m.Y', strtotime((string) $date)) : '—';
        $scoreboardLink = static function ($matchId, string $matchName): string {
            $matchName = esc($matchName !== '' ? $matchName : 'Match');
            $matchId = (int) $matchId;
            if ($matchId <= 0) {
                return $matchName;
            }
            return '<a class="scoreboard-link" href="' . esc(site_url('matches/' . scoreboard_encode($matchId) . '/scoreboard')) . '">' . $matchName . '</a>';
        };

        $dismissalLabel = static function (array $row): string {
            $mode = (string) ($row['dismissal_mode'] ?? '');
            $bowler = (string) ($row['dismissal_by_name'] ?? '');
            $fielder = (string) ($row['fielder_name'] ?? '');
            switch ($mode) {
                case 'bowled':
                    return $bowler !== '' ? 'b ' . $bowler : 'b';
                case 'caught':
                    return $fielder !== '' ? 'c ' . $fielder . ' b ' . $bowler : 'c & b ' . $bowler;
                case 'lbw':
                    return 'lbw b ' . $bowler;
                case 'run_out':
                    return $fielder !== '' ? 'run out (' . $fielder . ')' : 'run out';
                case 'stumped':
                    return $fielder !== '' ? 'st ' . $fielder . ' b ' . $bowler : 'st';
                default:
                    return $mode !== '' ? ucwords(str_replace('_', ' ', $mode)) : '—';
            }
        };

        $fieldingLabel = static function (array $row): string {
            $mode = (string) ($row['dismissal_mode'] ?? '');
            $batsman = (string) ($row['batsman_name'] ?? 'Batsman');
            $bowler = (string) ($row['bowler_name'] ?? '');
            switch ($mode) {
                case 'caught':
                    return 'Caught ' . $batsman . ($bowler !== '' ? ' (b ' . $bowler . ')' : '');
                case 'stumped':
                    return 'Stumped ' . $batsman . ($bowler !== '' ? ' (b ' . $bowler . ')' : '');
                case 'run_out':
                    return 'Run out ' . $batsman;
                default:
                    return $mode !== '' ? ucwords(str_replace('_', ' ', $mode)) . ' ' . $batsman : '—';
            }
        };

        $battingRows = $stats['batting_rows'] ?? [];
        $bowlingRows = $stats['bowling_rows'] ?? [];
        $fieldingRows = $stats['fielding_rows'] ?? [];
        $fielding = $stats['fielding'] ?? ['catches' => 0, 'stumpings' => 0, 'run_outs' => 0, 'dismissals' => 0];

        $sections = [];
        $sections['summary'] = 'Summary';
        if ($battingRows !== []) {
            $sections['batting'] = 'Batting';
        }
        if ($bowlingRows !== []) {
            $sections['bowling'] = 'Bowling';
        }
        if ($fieldingRows !== []) {
            $sections['fielding'] = 'Fielding';
        }
        $sectionKeys = array_keys($sections);
        ?>

        <!-- ══ HERO ══ -->
        <div class="pp-hero">
            <div class="container">
                <div class="pp-eyebrow">Public Player Profile</div>
                <div class="pp-name"><?= esc($player['name']) ?></div>
                <div class="pp-meta">Career batting, bowling &amp; appearance record</div>

                <div class="pp-scores">
                    <div class="pp-score-card">
                        <div class="stat-label">Matches</div>
                        <div class="stat-num"><?= esc((string) $stats['matches_played']) ?></div>
                    </div>
                    <div class="pp-score-card">
                        <div class="stat-label">Runs</div>
                        <div class="stat-num"><?= esc((string) $stats['batting']['runs']) ?></div>
                    </div>
                    <div class="pp-score-card">
                        <div class="stat-label">Bat Avg</div>
                        <div class="stat-num"><?= esc($formatAverage($stats['batting']['average'])) ?></div>
                    </div>
                    <div class="pp-score-card">
                        <div class="stat-label">Strike Rate</div>
                        <div class="stat-num"><?= esc((string) $stats['batting']['strike_rate']) ?></div>
                    </div>
                    <div class="pp-score-card">
                        <div class="stat-label">Wickets</div>
                        <div class="stat-num"><?= esc((string) $stats['bowling']['wickets']) ?></div>
                    </div>
                    <div class="pp-score-card">
                        <div class="stat-label">Bowl Avg</div>
                        <div class="stat-num"><?= esc((string) $stats['bowling']['average']) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══ SECTION TABS ══ -->
        <div class="pp-tabs-bar">
            <div class="container">
                <ul class="nav" id="ppTabs" role="tablist">
                    <?php foreach ($sectionKeys as $i => $key): ?>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?= $i === 0 ? 'active' : '' ?>" id="tab-<?= $key ?>" data-bs-toggle="tab" data-bs-target="#pane-<?= $key ?>" type="button" role="tab" aria-controls="pane-<?= $key ?>" aria-selected="<?= $i === 0 ? 'true' : 'false' ?>">
                                <?= esc($sections[$key]) ?>
                            </button>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <!-- ══ MAIN CONTENT ══ -->
        <div class="container py-4">
            <div class="tab-content" id="ppTabContent">

                <!-- Summary -->
                <div class="tab-pane fade show active" id="pane-summary" role="tabpanel" aria-labelledby="tab-summary" tabindex="0">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="pp-card">
                                <div class="pp-card-header"><span>🏏 Batting Summary</span></div>
                                <div class="table-responsive">
                                    <table class="pp-tbl kv-tbl">
                                        <tbody>
                                            <tr>
                                                <th>Innings</th>
                                                <td><?= esc((string) $stats['batting']['innings']) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Not Outs</th>
                                                <td><?= esc((string) $stats['batting']['not_outs']) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Runs</th>
                                                <td><?= esc((string) $stats['batting']['runs']) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Balls Faced</th>
                                                <td><?= esc((string) $stats['batting']['balls']) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Highest Score</th>
                                                <td><?= esc((string) $stats['batting']['highest_score']) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Average</th>
                                                <td><?= esc($formatAverage($stats['batting']['average'])) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Strike Rate</th>
                                                <td><?= esc((string) $stats['batting']['strike_rate']) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Fours</th>
                                                <td><?= esc((string) $stats['batting']['fours']) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Sixes</th>
                                                <td><?= esc((string) $stats['batting']['sixes']) ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="pp-card">
                                <div class="pp-card-header"><span>⚡ Bowling Summary</span></div>
                                <div class="table-responsive">
                                    <table class="pp-tbl kv-tbl">
                                        <tbody>
                                            <tr>
                                                <th>Overs</th>
                                                <td><?= esc((string) $stats['bowling']['overs']) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Maidens</th>
                                                <td><?= esc((string) $stats['bowling']['maidens']) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Runs Conceded</th>
                                                <td><?= esc((string) $stats['bowling']['runs']) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Wickets</th>
                                                <td><?= esc((string) $stats['bowling']['wickets']) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Best Bowling</th>
                                                <td><?= esc((string) $stats['bowling']['best_bowling']) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Average</th>
                                                <td><?= esc((string) $stats['bowling']['average']) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Economy</th>
                                                <td><?= esc((string) $stats['bowling']['economy']) ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Batting breakdown -->
                <?php if ($battingRows !== []): ?>
                    <div class="tab-pane fade" id="pane-batting" role="tabpanel" aria-labelledby="tab-batting" tabindex="0">
                        <div class="pp-card">
                            <div class="pp-card-header"><span>🏏 Batting breakdown</span></div>
                            <div class="table-responsive">
                                <table class="pp-tbl">
                                    <thead>
                                        <tr>
                                            <th>Match</th>
                                            <th>Venue</th>
                                            <th>Date</th>
                                            <th class="td-num">Runs</th>
                                            <th class="td-num">Balls</th>
                                            <th class="td-num">4s</th>
                                            <th class="td-num">6s</th>
                                            <th>Dismissal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($battingRows as $row): ?>
                                            <tr>
                                                <td class="td-name"><?= $scoreboardLink($row['match_id'] ?? null, (string) ($row['match_name'] ?? '')) ?></td>
                                                <td><?= esc((string) ($row['venue_name'] ?? 'TBD')) ?></td>
                                                <td><?= esc($formatDate($row['match_date'] ?? null)) ?></td>
                                                <td class="td-num"><?= esc((string) ($row['runs'] ?? 0)) ?></td>
                                                <td class="td-num"><?= esc((string) ($row['balls'] ?? 0)) ?></td>
                                                <td class="td-num"><?= esc((string) ($row['fours'] ?? 0)) ?></td>
                                                <td class="td-num"><?= esc((string) ($row['sixes'] ?? 0)) ?></td>
                                                <td><?= esc($dismissalLabel($row)) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Bowling breakdown -->
                <?php if ($bowlingRows !== []): ?>
                    <div class="tab-pane fade" id="pane-bowling" role="tabpanel" aria-labelledby="tab-bowling" tabindex="0">
                        <div class="pp-card">
                            <div class="pp-card-header"><span>⚡ Bowling breakdown</span></div>
                            <div class="table-responsive">
                                <table class="pp-tbl">
                                    <thead>
                                        <tr>
                                            <th>Match</th>
                                            <th>Venue</th>
                                            <th>Date</th>
                                            <th class="td-num">Overs</th>
                                            <th class="td-num">Maidens</th>
                                            <th class="td-num">Runs</th>
                                            <th class="td-num">Wickets</th>
                                            <th class="td-num">Econ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($bowlingRows as $row): ?>
                                            <tr>
                                                <td class="td-name"><?= $scoreboardLink($row['match_id'] ?? null, (string) ($row['match_name'] ?? '')) ?></td>
                                                <td><?= esc((string) ($row['venue_name'] ?? 'TBD')) ?></td>
                                                <td><?= esc($formatDate($row['match_date'] ?? null)) ?></td>
                                                <td class="td-num"><?= esc((string) ($row['overs'] ?? 0)) ?></td>
                                                <td class="td-num"><?= esc((string) ($row['maidens'] ?? 0)) ?></td>
                                                <td class="td-num"><?= esc((string) ($row['runs'] ?? 0)) ?></td>
                                                <td class="td-num"><?= esc((string) ($row['wickets'] ?? 0)) ?></td>
                                                <td class="td-num"><?= esc((string) ($row['economy'] ?? '—')) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Fielding -->
                <?php if ($fieldingRows !== []): ?>
                    <div class="tab-pane fade" id="pane-fielding" role="tabpanel" aria-labelledby="tab-fielding" tabindex="0">
                        <div class="row g-3 mb-3">
                            <div class="col-4 col-md-3">
                                <div class="pp-score-card" style="background:#fff;border-color:#e5ece5;color:#1a1a1a">
                                    <div class="stat-label" style="color:#888">Catches</div>
                                    <div class="stat-num" style="color:var(--cg)"><?= esc((string) $fielding['catches']) ?></div>
                                </div>
                            </div>
                            <div class="col-4 col-md-3">
                                <div class="pp-score-card" style="background:#fff;border-color:#e5ece5;color:#1a1a1a">
                                    <div class="stat-label" style="color:#888">Stumpings</div>
                                    <div class="stat-num" style="color:var(--cg)"><?= esc((string) $fielding['stumpings']) ?></div>
                                </div>
                            </div>
                            <div class="col-4 col-md-3">
                                <div class="pp-score-card" style="background:#fff;border-color:#e5ece5;color:#1a1a1a">
                                    <div class="stat-label" style="color:#888">Run Outs</div>
                                    <div class="stat-num" style="color:var(--cg)"><?= esc((string) $fielding['run_outs']) ?></div>
                                </div>
                            </div>
                            <div class="col-4 col-md-3">
                                <div class="pp-score-card" style="background:#fff;border-color:#e5ece5;color:#1a1a1a">
                                    <div class="stat-label" style="color:#888">Total Dismissals</div>
                                    <div class="stat-num" style="color:var(--cg)"><?= esc((string) $fielding['dismissals']) ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="pp-card">
                            <div class="pp-card-header"><span>🧤 Fielding involvements</span></div>
                            <div class="table-responsive">
                                <table class="pp-tbl">
                                    <thead>
                                        <tr>
                                            <th>Match</th>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Involvement</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($fieldingRows as $row): ?>
                                            <tr>
                                                <td class="td-name"><?= $scoreboardLink($row['match_id'] ?? null, (string) ($row['match_name'] ?? '')) ?></td>
                                                <td><?= esc($formatDate($row['match_date'] ?? null)) ?></td>
                                                <td><span class="story-chip"><?= esc(ucwords(str_replace('_', ' ', (string) ($row['dismissal_mode'] ?? '')))) ?></span></td>
                                                <td><?= esc($fieldingLabel($row)) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>

        <div class="pp-footer">Evergreen Cricket &mdash; Player Profile</div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>

</html>