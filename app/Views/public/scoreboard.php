<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= esc($match['team_name']) ?> vs <?= esc($match['opponent_name']) ?> — Scorecard</title>
        <meta name="description" content="Cricket scorecard: <?= esc($match['team_name']) ?> vs <?= esc($match['opponent_name']) ?>">

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
            .sc-hero {
                background: linear-gradient(150deg, var(--cgd) 0%, var(--cg) 55%, var(--cgl) 100%);
                color: #fff;
                padding: 28px 0 22px;
            }

            .sc-eyebrow {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 8px;
                font-size: 0.76rem;
                font-weight: 700;
                letter-spacing: 0.1em;
                text-transform: uppercase;
                opacity: 0.75;
            }

            .sc-match-title {
                font-size: clamp(1.3rem, 4vw, 2rem);
                font-weight: 800;
                letter-spacing: -0.02em;
                line-height: 1.15;
            }

            .sc-match-title .vs {
                font-weight: 400;
                opacity: 0.45;
                margin: 0 0.35em;
            }

            .sc-meta {
                margin-top: 5px;
                font-size: 0.82rem;
                opacity: 0.65;
            }

            /* Score cards in hero */
            .sc-scores {
                display: flex;
                gap: 12px;
                margin-top: 22px;
                flex-wrap: wrap;
            }

            .sc-score-card {
                flex: 1;
                min-width: 150px;
                background: var(--cgs);
                border: 1px solid rgba(255, 255, 255, 0.18);
                border-radius: 12px;
                padding: 14px 18px;
            }

            .sc-score-card .team-label {
                font-size: 0.72rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                opacity: 0.7;
            }

            .sc-score-card .score-num {
                font-size: 2rem;
                font-weight: 800;
                line-height: 1.05;
                margin-top: 3px;
            }

            .sc-score-card .score-overs {
                font-size: 0.77rem;
                opacity: 0.55;
                margin-top: 2px;
            }

            .sc-score-card.batting-now {
                border-color: rgba(255, 220, 80, 0.5);
                background: rgba(255, 220, 80, 0.12);
            }

            /* Result & toss */
            .sc-result {
                margin-top: 18px;
                background: linear-gradient(135deg, rgba(255, 215, 60, 0.22), rgba(255, 215, 60, 0.1));
                border: 1px solid rgba(255, 215, 60, 0.45);
                border-radius: 10px;
                padding: 13px 18px;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .sc-result .result-icon {
                font-size: 1.3rem;
                flex-shrink: 0;
            }

            .sc-result .result-body {
                flex: 1;
            }

            .sc-result .result-label {
                font-size: 0.65rem;
                font-weight: 800;
                letter-spacing: 0.12em;
                text-transform: uppercase;
                opacity: 0.6;
                margin-bottom: 1px;
            }

            .sc-result .result-text {
                font-size: 1rem;
                font-weight: 800;
                line-height: 1.2;
            }

            .sc-result-abandoned {
                background: rgba(120, 120, 120, 0.18);
                border-color: rgba(180, 180, 180, 0.4);
            }

            .sc-toss {
                margin-top: 10px;
                font-size: 0.78rem;
                opacity: 0.6;
            }

            /* Status pill */
            .st-pill {
                display: inline-block;
                padding: 3px 10px;
                border-radius: 20px;
                font-size: 0.7rem;
                font-weight: 700;
                letter-spacing: 0.07em;
                text-transform: uppercase;
            }

            .st-completed {
                background: #43a047;
                color: #fff;
            }

            .st-live {
                background: #e53935;
                color: #fff;
                animation: blink 1.4s ease-in-out infinite;
            }

            .st-scheduled {
                background: rgba(255, 255, 255, 0.22);
                color: #fff;
            }

            .st-abandoned {
                background: #757575;
                color: #fff;
            }

            @keyframes blink {

                0%,
                100% {
                    opacity: 1
                }

                50% {
                    opacity: .55
                }
            }

            /* ── Innings tabs ── */
            .sc-tabs-bar {
                background: #fff;
                border-bottom: 2px solid #e0e7e0;
                position: sticky;
                top: 0;
                z-index: 100;
            }

            .sc-tabs-bar .nav-link {
                color: #555;
                font-weight: 600;
                font-size: 0.875rem;
                padding: 13px 22px;
                border: none;
                border-bottom: 3px solid transparent;
                border-radius: 0;
                transition: color 0.15s, border-color 0.15s;
            }

            .sc-tabs-bar .nav-link.active {
                color: var(--cg);
                border-bottom-color: var(--cg);
                background: none;
            }

            .sc-tabs-bar .nav-link:hover:not(.active) {
                color: var(--cg);
                border-bottom-color: #a5d6a7;
                background: none;
            }

            .sc-tabs-bar .tab-score {
                font-size: 0.75rem;
                font-weight: 700;
                opacity: 0.6;
                margin-left: 6px;
            }

            /* ── Cards ── */
            .sc-card {
                background: #fff;
                border-radius: 12px;
                overflow: hidden;
                box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06), 0 0 0 1px #e5ece5;
                margin-bottom: 16px;
            }

            .sc-card-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 12px 18px;
                background: #f3f8f3;
                border-bottom: 1px solid #e5ece5;
                font-weight: 700;
                font-size: 0.88rem;
                color: #1b5e20;
            }

            .sc-card-header .hd-score {
                font-size: 1rem;
                font-weight: 800;
                color: var(--cg);
            }

            .bowl-header {
                background: #f0f4f0;
                border-top: 1px solid #e0e8e0;
                padding: 10px 18px;
                font-size: 0.76rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.07em;
                color: #388e3c;
                border-bottom: 1px solid #e5ece5;
            }

            /* ── Tables ── */
            .sc-tbl {
                width: 100%;
                border-collapse: collapse;
                font-size: 0.86rem;
            }

            .sc-tbl thead th {
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

            .sc-tbl tbody td {
                padding: 10px 14px;
                border-bottom: 1px solid #f2f5f2;
                vertical-align: middle;
            }

            .sc-tbl tbody tr:last-child td {
                border-bottom: none;
            }

            .sc-tbl tbody tr:hover {
                background: #f9fcf9;
            }

            .sc-tbl tfoot td {
                padding: 9px 14px;
            }

            /* Cell types */
            .td-seq {
                color: #bbb;
                font-size: 0.78rem;
                width: 32px;
            }

            .td-name {
                font-weight: 600;
            }

            .td-name .not-out-star {
                color: #2e7d32;
                font-size: 0.9em;
            }

            .td-runs {
                font-weight: 800;
                font-size: 1rem;
                text-align: center;
                width: 50px;
            }

            .td-balls {
                text-align: center;
                color: #888;
                width: 46px;
            }

            .td-boundary {
                text-align: center;
                color: #555;
                width: 40px;
            }

            .td-sr {
                text-align: center;
                color: #999;
                font-size: 0.8rem;
                width: 52px;
            }

            .td-dismiss {
                font-size: 0.78rem;
                color: #888;
            }

            .td-w {
                font-weight: 800;
                font-size: 1rem;
                text-align: center;
                width: 46px;
                color: #1b5e20;
            }

            .td-econ {
                text-align: center;
                color: #999;
                font-size: 0.8rem;
                width: 54px;
            }

            /* Extras & Total rows */
            .tr-extras td {
                background: #f9fbf9;
                font-size: 0.8rem;
                color: #666;
                border-bottom: 1px solid #e8eee8 !important;
            }

            .tr-total td {
                background: #edf6ed;
                font-weight: 700;
                border-top: 2px solid var(--cg) !important;
                border-bottom: none !important;
            }

            .tr-total .td-runs {
                color: var(--cg);
                font-size: 1.05rem;
            }

            .tr-total .label-cell {
                font-size: 0.75rem;
                text-transform: uppercase;
                letter-spacing: 0.06em;
                color: #666;
            }

            /* Fall of wickets */
            .fow-row {
                padding: 10px 18px;
                background: #fffef5;
                border-top: 1px dashed #ddd9c4;
                font-size: 0.78rem;
                line-height: 1.8;
            }

            .fow-label {
                font-weight: 700;
                color: #666;
                margin-right: 6px;
            }

            .fow-chip {
                display: inline-block;
                background: #f5f0dd;
                border-radius: 4px;
                padding: 2px 8px;
                margin: 1px 3px 1px 0;
                color: #5a5230;
            }

            /* Did not bat / bowl */
            .dnb-row {
                padding: 9px 18px;
                font-size: 0.8rem;
                color: #666;
                border-top: 1px dashed #e8ece8;
                background: #fafcfa;
                line-height: 1.7;
            }

            .dnb-label {
                font-weight: 700;
                color: #555;
                margin-right: 4px;
            }

            .dnb-name {
                margin-right: 2px;
            }

            /* No data */
            .no-data {
                padding: 24px;
                text-align: center;
                color: #aaa;
                font-size: 0.85rem;
            }

            /* Footer */
            .sc-footer {
                text-align: center;
                padding: 24px 16px;
                font-size: 0.76rem;
                color: #aaa;
            }

            @media (max-width: 576px) {
                .sc-score-card .score-num {
                    font-size: 1.5rem;
                }

                .sc-match-title {
                    font-size: 1.2rem;
                }
            }
        </style>
    </head>

    <body>

        <?php
        /* ── Derived side variables ── */
        $inns1BowlingSide = $inns1BattingSide === 'team_a' ? 'team_b' : 'team_a';
        $inns2BattingSide = $inns1BowlingSide;
        $inns2BowlingSide = $inns1BattingSide;

        $sides = [
            1 => ['bat' => $inns1BattingSide, 'bowl' => $inns1BowlingSide],
            2 => ['bat' => $inns2BattingSide, 'bowl' => $inns2BowlingSide],
        ];

        $sideLabel = static function (string $side) use ($match): string {
            return $side === 'team_a' ? (string) $match['team_name'] : (string) $match['opponent_name'];
        };

        /* ── Dismissal text ── */
        $dismissalText = static function (array $row): string {
            $mode = $row['dismissal_mode'] ?? '';
            $bowler = esc($row['dismissal_by_name'] ?? '');
            $fielder = esc($row['fielder_name'] ?? '');
            switch ($mode) {
                case 'bowled':
                    return 'b ' . $bowler;
                case 'caught':
                    return $fielder ? 'c ' . $fielder . ' b ' . $bowler : 'c &amp; b ' . $bowler;
                case 'lbw':
                    return 'lbw b ' . $bowler;
                case 'run_out':
                    return 'run out (' . implode('/', array_filter([$fielder, $bowler])) . ')';
                case 'stumped':
                    return 'st ' . $fielder . ' b ' . $bowler;
                case 'hit_wicket':
                    return 'hit wkt b ' . $bowler;
                case 'not_out':
                    return '<span style="color:#2e7d32;font-weight:600">not out</span>';
                case 'retired_hurt':
                    return 'ret hurt';
                default:
                    return $mode ? esc(ucwords(str_replace('_', ' ', $mode))) : '—';
            }
        };

        /* ── Innings summary (runs, wickets, overs from bowling) ── */
        $calcSummary = static function (array $batRows, array $bowlRows, array $extrasRow): array {
            $batRuns = 0;
            $wickets = 0;
            foreach ($batRows as $r) {
                $batRuns += (int) ($r['runs'] ?? 0);
                $m = $r['dismissal_mode'] ?? '';
                if ($m !== '' && !in_array($m, ['not_out', 'retired_hurt'], true)) {
                    $wickets++;
                }
            }
            $extras = (int) ($extrasRow['byes'] ?? 0)
                + (int) ($extrasRow['leg_byes'] ?? 0)
                + (int) ($extrasRow['wides'] ?? 0)
                + (int) ($extrasRow['no_balls'] ?? 0);
            $total = $batRuns + $extras;

            $totalBalls = 0;
            foreach ($bowlRows as $r) {
                if ($r['overs'] !== null) {
                    $o = (float) $r['overs'];
                    $full = (int) $o;
                    $b = (int) round(($o - $full) * 10);
                    $totalBalls += $full * 6 + $b;
                }
            }
            $overs = $totalBalls > 0 ? intdiv($totalBalls, 6) . '.' . ($totalBalls % 6) : '';

            return ['runs' => $total, 'wickets' => $wickets, 'overs' => $overs];
        };

        /* ── Economy rate ── */
        $economy = static function ($overs, $runs): string {
            if ($overs === null || $runs === null)
                return '—';
            $o = (float) $overs;
            $full = (int) $o;
            $b = (int) round(($o - $full) * 10);
            $balls = $full * 6 + $b;
            if ($balls === 0)
                return '—';
            return number_format($runs / $balls * 6, 2);
        };

        /* ── Pre-compute summaries ── */
        $summaries = [];
        $hasInns = [];
        foreach ([1, 2] as $inns) {
            $bat = $battingByInnings[$inns] ?? [];
            $bowl = $bowlingByInnings[$inns] ?? [];
            $ext = $extrasByInnings[$inns] ?? [];
            if ($bat !== [] || $bowl !== []) {
                $hasInns[] = $inns;
                $summaries[$inns] = $calcSummary($bat, $bowl, $ext);
            }
        }

        /* ── Status class ── */
        $statusClass = [
            'completed' => 'st-completed',
            'live' => 'st-live',
            'abandoned' => 'st-abandoned',
        ][($match['match_status'] ?? '')] ?? 'st-scheduled';

        /* ── Date string ── */
        $dateStr = '';
        if (!empty($match['scheduled_at'])) {
            $dateStr = date('D, d M Y', strtotime((string) $match['scheduled_at']));
        }
        $formatLabel = strtoupper((string) ($match['match_type'] ?? ''));
        if (!empty($match['format_overs'])) {
            $formatLabel .= ' · ' . $match['format_overs'] . ' ov';
        }
        ?>

        <!-- ══ HERO ══ -->
        <div class="sc-hero">
            <div class="container">
                <div class="sc-eyebrow">
                    <span>Match #<?= $matchNumber ?></span>
                    <span class="st-pill <?= $statusClass ?>"><?= esc(ucfirst((string) ($match['match_status'] ?? ''))) ?></span>
                </div>

                <div class="sc-match-title">
                    <?= esc($match['team_name']) ?><span class="vs">vs</span><?= esc($match['opponent_name']) ?>
                </div>

                <div class="sc-meta">
                    <?= esc($formatLabel) ?>
                    <?php if (!empty($match['venue'])): ?>
                        &nbsp;·&nbsp;<?= esc($match['venue']) ?>
                    <?php endif; ?>
                    <?php if ($dateStr): ?>
                        &nbsp;·&nbsp;<?= esc($dateStr) ?>
                    <?php endif; ?>
                </div>

                <!-- Per-innings score cards -->
                <?php if ($hasInns !== []): ?>
                    <div class="sc-scores">
                        <?php foreach ($hasInns as $inns): ?>
                            <?php $s = $summaries[$inns];
                            $battingSide = $sides[$inns]['bat']; ?>
                            <div class="sc-score-card">
                                <div class="team-label"><?= esc($sideLabel($battingSide)) ?></div>
                                <div class="score-num">
                                    <?php if ($s['runs'] > 0 || $s['wickets'] > 0): ?>
                                        <?= $s['runs'] ?><span style="opacity:.5;font-weight:500">/</span><?= $s['wickets'] ?>
                                    <?php else: ?>
                                        <span style="opacity:.4">—</span>
                                    <?php endif; ?>
                                </div>
                                <?php if ($s['overs']): ?>
                                    <div class="score-overs"><?= esc($s['overs']) ?> overs</div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php
                $status = $match['match_status'] ?? '';
                $resultSummary = trim((string) ($match['result_summary'] ?? ''));
                $isAbandoned = in_array($status, ['abandoned', 'archived'], true);

                // Auto-calculate result from innings data when result_summary is not saved
                $autoResult = '';
                if ($resultSummary === '' && isset($summaries[1], $summaries[2])) {
                    $s1 = $summaries[1];
                    $s2 = $summaries[2];
                    $team1Name = $sideLabel($sides[1]['bat']);
                    $team2Name = $sideLabel($sides[2]['bat']);

                    if ($s1['runs'] > $s2['runs']) {
                        $margin = $s1['runs'] - $s2['runs'];
                        $autoResult = $team1Name . ' won by ' . $margin . ' ' . ($margin === 1 ? 'run' : 'runs');
                    } elseif ($s2['runs'] > $s1['runs']) {
                        $remaining = 10 - $s2['wickets'];
                        $autoResult = $team2Name . ' won by '
                            . $remaining . ' ' . ($remaining === 1 ? 'wicket' : 'wickets');
                    } elseif ($s1['runs'] === $s2['runs'] && $s1['runs'] > 0) {
                        $autoResult = 'Match tied — ' . $s1['runs'] . ' runs each';
                    }
                }

                $displayResult = $resultSummary !== '' ? $resultSummary : $autoResult;
                ?>
                <?php if ($isAbandoned): ?>
                    <div class="sc-result sc-result-abandoned">
                        <span class="result-icon">🚫</span>
                        <div class="result-body">
                            <div class="result-label">Match Abandoned</div>
                            <div class="result-text"><?= $displayResult !== '' ? esc($displayResult) : 'No result' ?></div>
                        </div>
                    </div>
                <?php elseif ($displayResult !== ''): ?>
                    <div class="sc-result">
                        <span class="result-icon">🏆</span>
                        <div class="result-body">
                            <div class="result-label">Result</div>
                            <div class="result-text"><?= esc($displayResult) ?></div>
                        </div>
                    </div>
                <?php elseif ($status === 'live'): ?>
                    <div class="sc-result" style="background:rgba(229,57,53,0.18);border-color:rgba(229,57,53,0.4)">
                        <span class="result-icon">🏏</span>
                        <div class="result-body">
                            <div class="result-label">Status</div>
                            <div class="result-text">Match In Progress</div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($match['toss_winner'])): ?>
                    <?php $tossName = $match['toss_winner'] === 'team_a' ? $match['team_name'] : $match['opponent_name']; ?>
                    <div class="sc-toss">
                        Toss: <?= esc($tossName) ?> won and elected to <?= esc($match['toss_decision'] ?? '') ?> first
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ══ INNINGS TABS ══ -->
        <?php if (count($hasInns) > 1): ?>
            <div class="sc-tabs-bar">
                <div class="container">
                    <ul class="nav" id="innsTabs" role="tablist">
                        <?php foreach ($hasInns as $i => $inns): ?>
                            <?php $s = $summaries[$inns];
                            $batSide = $sides[$inns]['bat']; ?>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link <?= $i === 0 ? 'active' : '' ?>" id="tab-inns<?= $inns ?>" data-bs-toggle="tab" data-bs-target="#pane-inns<?= $inns ?>" type="button" role="tab" aria-controls="pane-inns<?= $inns ?>" aria-selected="<?= $i === 0 ? 'true' : 'false' ?>">
                                    <?= esc($sideLabel($batSide)) ?>
                                    <?php if ($s['runs'] > 0 || $s['wickets'] > 0): ?>
                                        <span class="tab-score"><?= $s['runs'] ?>/<?= $s['wickets'] ?></span>
                                    <?php endif; ?>
                                </button>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <!-- ══ MAIN CONTENT ══ -->
        <div class="container py-4">
            <?php if ($hasInns === []): ?>
                <div class="sc-card">
                    <div class="no-data">No scoreboard data recorded for this match yet.</div>
                </div>
            <?php else: ?>
                <div class="tab-content" id="innsTabContent">
                    <?php foreach ($hasInns as $i => $inns): ?>
                        <?php
                        $battingSide = $sides[$inns]['bat'];
                        $bowlingSide = $sides[$inns]['bowl'];
                        $batRows = $battingByInnings[$inns] ?? [];
                        $bowlRows = $bowlingByInnings[$inns] ?? [];
                        $fowRows = $wicketsByInnings[$inns] ?? [];
                        $extrasRow = $extrasByInnings[$inns] ?? [];
                        $s = $summaries[$inns];

                        $batRunsSum = array_sum(array_column(array_filter($batRows, static fn($r) => $r['runs'] !== null), 'runs'));
                        $extrasTotal = (int) ($extrasRow['byes'] ?? 0)
                            + (int) ($extrasRow['leg_byes'] ?? 0)
                            + (int) ($extrasRow['wides'] ?? 0)
                            + (int) ($extrasRow['no_balls'] ?? 0);
                        $grandTotal = $batRunsSum + $extrasTotal;
                        $totalBalls = array_sum(array_column(array_filter($batRows, static fn($r) => $r['balls'] !== null), 'balls'));
                        $totalFours = array_sum(array_column(array_filter($batRows, static fn($r) => $r['fours'] !== null), 'fours'));
                        $totalSixes = array_sum(array_column(array_filter($batRows, static fn($r) => $r['sixes'] !== null), 'sixes'));

                        $scoreStr = $s['runs'] . '/' . $s['wickets'];
                        if ($s['overs']) {
                            $scoreStr .= ' (' . $s['overs'] . ' ov)';
                        }
                        ?>
                        <div class="tab-pane fade <?= $i === 0 ? 'show active' : '' ?>" id="pane-inns<?= $inns ?>" role="tabpanel" aria-labelledby="tab-inns<?= $inns ?>" tabindex="0">

                            <!-- Batting card -->
                            <div class="sc-card">
                                <div class="sc-card-header">
                                    <span>🏏 <?= esc($sideLabel($battingSide)) ?> Innings</span>
                                    <?php if ($s['runs'] > 0 || $s['wickets'] > 0): ?>
                                        <span class="hd-score"><?= esc($scoreStr) ?></span>
                                    <?php endif; ?>
                                </div>

                                <?php if ($batRows !== []): ?>
                                    <div class="table-responsive">
                                        <table class="sc-tbl">
                                            <thead>
                                                <tr>
                                                    <th class="td-seq">#</th>
                                                    <th>Batter</th>
                                                    <th class="td-runs">R</th>
                                                    <th class="td-balls">B</th>
                                                    <th class="td-boundary">4s</th>
                                                    <th class="td-boundary">6s</th>
                                                    <th class="td-sr">SR</th>
                                                    <th>Dismissal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($batRows as $idx => $row): ?>
                                                    <?php
                                                    $mode = $row['dismissal_mode'] ?? '';
                                                    $isNotOut = in_array($mode, ['not_out', 'retired_hurt', ''], true) && $row['runs'] !== null;
                                                    $srVal = ($row['balls'] && (int) $row['balls'] > 0 && $row['runs'] !== null)
                                                        ? number_format((int) $row['runs'] / (int) $row['balls'] * 100, 1)
                                                        : '—';
                                                    ?>
                                                    <tr>
                                                        <td class="td-seq"><?= $idx + 1 ?></td>
                                                        <td class="td-name">
                                                            <?= esc($row['player_name'] ?? '—') ?>
                                                            <?php if ($isNotOut): ?><span class="not-out-star">*</span><?php endif; ?>
                                                        </td>
                                                        <td class="td-runs"><?= $row['runs'] !== null ? esc((string) $row['runs']) : '—' ?></td>
                                                        <td class="td-balls"><?= $row['balls'] !== null ? esc((string) $row['balls']) : '—' ?></td>
                                                        <td class="td-boundary"><?= $row['fours'] !== null ? esc((string) $row['fours']) : '—' ?></td>
                                                        <td class="td-boundary"><?= $row['sixes'] !== null ? esc((string) $row['sixes']) : '—' ?></td>
                                                        <td class="td-sr"><?= $srVal ?></td>
                                                        <td class="td-dismiss"><?= $dismissalText($row) ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                            <tfoot>
                                                <?php if ($extrasRow !== []): ?>
                                                    <tr class="tr-extras">
                                                        <td colspan="2">
                                                            Extras
                                                            <span style="margin-left:6px;opacity:0.75">
                                                                (B <?= (int) ($extrasRow['byes'] ?? 0) ?>,
                                                                LB <?= (int) ($extrasRow['leg_byes'] ?? 0) ?>,
                                                                W <?= (int) ($extrasRow['wides'] ?? 0) ?>,
                                                                NB <?= (int) ($extrasRow['no_balls'] ?? 0) ?>)
                                                            </span>
                                                        </td>
                                                        <td class="td-runs" style="font-size:.88rem;font-weight:600"><?= $extrasTotal ?></td>
                                                        <td colspan="5"></td>
                                                    </tr>
                                                <?php endif; ?>
                                                <tr class="tr-total">
                                                    <td colspan="2" class="label-cell">Total</td>
                                                    <td class="td-runs"><?= $grandTotal ?>/<?= $s['wickets'] ?></td>
                                                    <td class="td-balls" style="font-weight:400;font-size:.82rem;color:#666"><?= $totalBalls ?> b</td>
                                                    <td class="td-boundary" style="font-size:.82rem;color:#555"><?= $totalFours ?></td>
                                                    <td class="td-boundary" style="font-size:.82rem;color:#555"><?= $totalSixes ?></td>
                                                    <td colspan="2"></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="no-data">No batting data recorded.</div>
                                <?php endif; ?>

                                <!-- Fall of wickets -->
                                <?php if ($fowRows !== []): ?>
                                    <div class="fow-row">
                                        <span class="fow-label">Fall of Wickets</span>
                                        <?php foreach ($fowRows as $k => $fw): ?>
                                            <span class="fow-chip">
                                                <?= $k + 1 ?>-<?= esc((string) $fw['score_at_dismissal']) ?>
                                                <span style="opacity:.65"> (<?= esc($fw['player_name'] ?? '?') ?>, <?= esc((string) $fw['over_at_dismissal']) ?> ov)</span>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Bowling section inside same card -->
                                <?php if ($bowlRows !== []): ?>
                                    <div class="bowl-header">⚡ Bowling — <?= esc($sideLabel($bowlingSide)) ?></div>
                                    <div class="table-responsive">
                                        <table class="sc-tbl">
                                            <thead>
                                                <tr>
                                                    <th class="td-seq">#</th>
                                                    <th>Bowler</th>
                                                    <th class="td-balls" style="width:46px">O</th>
                                                    <th class="td-balls">M</th>
                                                    <th class="td-balls">R</th>
                                                    <th class="td-w">W</th>
                                                    <th class="td-econ">Econ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($bowlRows as $idx => $row): ?>
                                                    <tr>
                                                        <td class="td-seq"><?= $idx + 1 ?></td>
                                                        <td class="td-name"><?= esc($row['player_name'] ?? '—') ?></td>
                                                        <td class="td-balls"><?= $row['overs'] !== null ? esc((string) $row['overs']) : '—' ?></td>
                                                        <td class="td-balls"><?= $row['maidens'] !== null ? esc((string) $row['maidens']) : '—' ?></td>
                                                        <td class="td-balls"><?= $row['runs'] !== null ? esc((string) $row['runs']) : '—' ?></td>
                                                        <td class="td-w"><?= $row['wickets'] !== null ? esc((string) $row['wickets']) : '—' ?></td>
                                                        <td class="td-econ"><?= $economy($row['overs'], $row['runs']) ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- Did not bowl -->
                                    <?php $didNotBowl = $didNotBowlByInnings[$inns] ?? []; ?>
                                    <?php if ($didNotBowl !== []): ?>
                                        <div class="dnb-row">
                                            <span class="dnb-label">Did not bowl:</span>
                                            <?php foreach ($didNotBowl as $k => $p): ?>
                                                <span class="dnb-name"><?= esc($p['player_name']) ?><?= $k < count($didNotBowl) - 1 ? ',' : '' ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>

                        </div><!-- /.tab-pane -->
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="sc-footer">Evergreen Cricket &mdash; Scorecard</div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>

</html>