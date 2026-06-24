<?= $this->extend('admin/template/default') ?>

<?= $this->section('admin_nav') ?>matches<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$inns1BowlingSide = $inns1BattingSide === 'team_a' ? 'team_b' : 'team_a';
$inns2BattingSide = $inns1BowlingSide;
$inns2BowlingSide = $inns1BattingSide;

$sideLabel = static function (string $side) use ($match): string {
    return $side === 'team_a' ? (string) $match['team_name'] : (string) $match['opponent_name'];
};

$dismissalText = static function (array $row): string {
    $mode    = $row['dismissal_mode'] ?? '';
    $bowler  = $row['dismissal_by_name'] ?? '';
    $fielder = $row['fielder_name'] ?? '';
    return match ($mode) {
        'bowled'       => 'b ' . $bowler,
        'caught'       => $fielder ? 'c ' . $fielder . ' b ' . $bowler : 'c &amp; b ' . $bowler,
        'lbw'          => 'lbw b ' . $bowler,
        'run_out'      => 'run out (' . implode('/', array_filter([$fielder, $bowler])) . ')',
        'stumped'      => 'st ' . $fielder . ' b ' . $bowler,
        'hit_wicket'   => 'hit wkt b ' . $bowler,
        'not_out'      => 'not out',
        'retired_hurt' => 'ret hurt',
        default        => $mode ? esc(ucwords(str_replace('_', ' ', $mode))) : '—',
    };
};
?>
<div class="content-card">
    <div class="panel mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h1 class="h2 fw-bold mb-2"><?= esc($match['team_name']) ?> vs <?= esc($match['opponent_name']) ?></h1>
                <p class="text-secondary mb-0">
                    Match #<?= $matchNumber ?>
                    &mdash;
                    <span class="badge bg-<?= ($match['match_status'] ?? '') === 'completed' ? 'success' : 'secondary' ?>">
                        <?= esc(ucfirst($match['match_status'] ?? '')) ?>
                    </span>
                </p>
                <?php if ($match['toss_winner'] ?? ''): ?>
                    <?php $tossName = $match['toss_winner'] === 'team_a' ? $match['team_name'] : $match['opponent_name']; ?>
                    <p class="text-secondary mb-0 small mt-1">
                        Toss: <?= esc($tossName) ?> won and elected to <?= esc($match['toss_decision'] ?? '—') ?> first
                    </p>
                <?php endif; ?>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="<?= site_url('/admin/matches/' . $match['id'] . '/scoreboard') ?>" class="btn btn-light border">Edit Scoreboard</a>
                <a href="<?= site_url('/admin/matches/' . $match['id']) ?>" class="btn btn-light border">Back to Match</a>
            </div>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>

    <?php foreach ([1, 2] as $inns): ?>
        <?php
        $battingSide = $inns === 1 ? $inns1BattingSide : $inns2BattingSide;
        $bowlingSide = $inns === 1 ? $inns1BowlingSide : $inns2BowlingSide;
        $batRows     = $battingByInnings[$inns] ?? [];
        $bowlRows    = $bowlingByInnings[$inns] ?? [];
        $fowRows     = $wicketsByInnings[$inns] ?? [];
        $extrasRow   = $extrasByInnings[$inns] ?? [];

        if ($batRows === [] && $bowlRows === []) { continue; }
        ?>
        <div class="card mb-4">
            <div class="card-header fw-semibold">
                Innings <?= $inns ?> — <?= esc($sideLabel($battingSide)) ?> Batting
            </div>
            <div class="card-body p-0">
                <?php if ($batRows !== []): ?>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:36px">#</th>
                                    <th>Batter</th>
                                    <th class="text-center" style="width:52px">R</th>
                                    <th class="text-center" style="width:52px">B</th>
                                    <th class="text-center" style="width:46px">4s</th>
                                    <th class="text-center" style="width:46px">6s</th>
                                    <th class="text-secondary small">Dismissal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($batRows as $i => $row): ?>
                                    <tr>
                                        <td class="text-secondary"><?= $i + 1 ?></td>
                                        <td class="fw-semibold"><?= esc($row['player_name'] ?? '—') ?></td>
                                        <td class="text-center fw-bold"><?= $row['runs'] !== null ? esc((string) $row['runs']) : '—' ?></td>
                                        <td class="text-center text-secondary"><?= $row['balls'] !== null ? esc((string) $row['balls']) : '—' ?></td>
                                        <td class="text-center"><?= $row['fours'] !== null ? esc((string) $row['fours']) : '—' ?></td>
                                        <td class="text-center"><?= $row['sixes'] !== null ? esc((string) $row['sixes']) : '—' ?></td>
                                        <td class="text-secondary small"><?= $dismissalText($row) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <?php
                            $totalRuns  = array_sum(array_column(array_filter($batRows, static fn($r) => $r['runs'] !== null), 'runs'));
                            $totalBalls = array_sum(array_column(array_filter($batRows, static fn($r) => $r['balls'] !== null), 'balls'));
                            $totalFours = array_sum(array_column(array_filter($batRows, static fn($r) => $r['fours'] !== null), 'fours'));
                            $totalSixes = array_sum(array_column(array_filter($batRows, static fn($r) => $r['sixes'] !== null), 'sixes'));
                            $extrasTotal = (int) ($extrasRow['byes'] ?? 0) + (int) ($extrasRow['leg_byes'] ?? 0) + (int) ($extrasRow['wides'] ?? 0) + (int) ($extrasRow['no_balls'] ?? 0);
                            ?>
                            <tfoot class="table-light">
                                <?php if ($extrasRow !== []): ?>
                                    <tr>
                                        <td colspan="2" class="text-secondary small">
                                            Extras
                                            (B <?= (int) ($extrasRow['byes'] ?? 0) ?>,
                                            LB <?= (int) ($extrasRow['leg_byes'] ?? 0) ?>,
                                            W <?= (int) ($extrasRow['wides'] ?? 0) ?>,
                                            NB <?= (int) ($extrasRow['no_balls'] ?? 0) ?>)
                                        </td>
                                        <td class="text-center text-secondary"><?= $extrasTotal ?></td>
                                        <td colspan="4"></td>
                                    </tr>
                                <?php endif; ?>
                                <tr class="fw-semibold">
                                    <td colspan="2">Total</td>
                                    <td class="text-center"><?= $totalRuns + $extrasTotal ?></td>
                                    <td class="text-center text-secondary"><?= $totalBalls ?></td>
                                    <td class="text-center"><?= $totalFours ?></td>
                                    <td class="text-center"><?= $totalSixes ?></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-secondary small p-3 mb-0">No batting data recorded.</p>
                <?php endif; ?>

                <?php if ($fowRows !== []): ?>
                    <div class="px-3 py-2 border-top bg-light">
                        <span class="text-secondary small fw-semibold me-2">Fall of Wickets:</span>
                        <?php foreach ($fowRows as $i => $row): ?>
                            <span class="small me-3"><?= $i + 1 ?>-<?= esc((string) $row['score_at_dismissal']) ?> (<?= esc($row['player_name'] ?? '—') ?>, <?= esc((string) $row['over_at_dismissal']) ?> ov)</span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="card-header fw-semibold border-top">
                Bowling — <?= esc($sideLabel($bowlingSide)) ?>
            </div>
            <div class="card-body p-0">
                <?php if ($bowlRows !== []): ?>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:36px">#</th>
                                    <th>Bowler</th>
                                    <th class="text-center" style="width:52px">O</th>
                                    <th class="text-center" style="width:52px">M</th>
                                    <th class="text-center" style="width:52px">R</th>
                                    <th class="text-center" style="width:52px">W</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bowlRows as $i => $row): ?>
                                    <tr>
                                        <td class="text-secondary"><?= $i + 1 ?></td>
                                        <td class="fw-semibold"><?= esc($row['player_name'] ?? '—') ?></td>
                                        <td class="text-center"><?= $row['overs'] !== null ? esc((string) $row['overs']) : '—' ?></td>
                                        <td class="text-center text-secondary"><?= $row['maidens'] !== null ? esc((string) $row['maidens']) : '—' ?></td>
                                        <td class="text-center"><?= $row['runs'] !== null ? esc((string) $row['runs']) : '—' ?></td>
                                        <td class="text-center fw-bold"><?= $row['wickets'] !== null ? esc((string) $row['wickets']) : '—' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-secondary small p-3 mb-0">No bowling data recorded.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?= $this->endSection() ?>
