<?= $this->extend('admin/template/default') ?>

<?= $this->section('content') ?>
<?php
$inns1BowlingSide = $inns1BattingSide === 'team_a' ? 'team_b' : 'team_a';
$inns2BattingSide = $inns1BowlingSide;
$inns2BowlingSide = $inns1BattingSide;

$sideLabel = static function (string $side) use ($match): string {
    return $side === 'team_a' ? (string) $match['team_name'] : (string) $match['opponent_name'];
};

$dismissalText = static function (array $row): string {
    $mode = $row['dismissal_mode'] ?? '';
    $bowler = $row['dismissal_by_name'] ?? '';
    $fielder = $row['fielder_name'] ?? '';
    return match ($mode) {
        'bowled' => 'b ' . $bowler,
        'caught' => $fielder ? 'c ' . $fielder . ' b ' . $bowler : 'c &amp; b ' . $bowler,
        'lbw' => 'lbw b ' . $bowler,
        'run_out' => 'run out (' . implode('/', array_filter([$fielder, $bowler])) . ')',
        'stumped' => 'st ' . $fielder . ' b ' . $bowler,
        'hit_wicket' => 'hit wkt b ' . $bowler,
        'not_out' => 'not out',
        'retired_hurt' => 'ret hurt',
        default => $mode ? esc(ucwords(str_replace('_', ' ', $mode))) : '',
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
                <a href="<?= site_url('/matches/' . esc($publicHash) . '/scoreboard') ?>" class="btn btn-primary" target="_blank">Scorecard</a>
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
        $batRows = $battingByInnings[$inns] ?? [];
        $bowlRows = array_values(array_filter(
            $bowlingByInnings[$inns] ?? [],
            static fn($r) => $r['overs'] !== null && (float) $r['overs'] > 0.0
        ));
        $fowRows = $wicketsByInnings[$inns] ?? [];
        $extrasRow = $extrasByInnings[$inns] ?? [];

        if ($batRows === [] && $bowlRows === []) {
            continue;
        }
        ?>
        <div class="card mb-4">
            <div class="card-header bg-body-tertiary">
                Innings <?= $inns ?> — <?= esc($sideLabel($battingSide)) ?> Batting
            </div>
            <div class="card-body">
                <?php if ($batRows !== []): ?>
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th style="width:36px">#</th>
                                    <th>Batsman</th>
                                    <th>&nbsp;</th>
                                    <th class="text-center" style="width:52px">R</th>
                                    <th class="text-center" style="width:52px">B</th>
                                    <th class="text-center" style="width:46px">4s</th>
                                    <th class="text-center" style="width:46px">6s</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($batRows as $i => $row): ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td><?= esc($row['player_name'] ?? '—') ?></td>
                                        <td class="text-secondary small"><?= $dismissalText($row) ?></td>
                                        <td class="text-center"><?= $row['runs'] !== null ? esc((string) $row['runs']) : '0' ?></td>
                                        <td class="text-center"><?= $row['balls'] !== null ? esc((string) $row['balls']) : '0' ?></td>
                                        <td class="text-center"><?= $row['fours'] !== null ? esc((string) $row['fours']) : '0' ?></td>
                                        <td class="text-center"><?= $row['sixes'] !== null ? esc((string) $row['sixes']) : '0' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <?php
                            $totalRuns = array_sum(array_column(array_filter($batRows, static fn($r) => $r['runs'] !== null), 'runs'));
                            $totalBalls = array_sum(array_column(array_filter($batRows, static fn($r) => $r['balls'] !== null), 'balls'));
                            $totalFours = array_sum(array_column(array_filter($batRows, static fn($r) => $r['fours'] !== null), 'fours'));
                            $totalSixes = array_sum(array_column(array_filter($batRows, static fn($r) => $r['sixes'] !== null), 'sixes'));
                            $extrasTotal = (int) ($extrasRow['byes'] ?? 0) + (int) ($extrasRow['leg_byes'] ?? 0) + (int) ($extrasRow['wides'] ?? 0) + (int) ($extrasRow['no_balls'] ?? 0);
                            ?>
                            <tfoot>
                                <?php if ($extrasRow !== []): ?>
                                    <tr>
                                        <td colspan="2">Extras</td>
                                        <td>
                                            B <?= (int) ($extrasRow['byes'] ?? 0) ?>,
                                            LB <?= (int) ($extrasRow['leg_byes'] ?? 0) ?>,
                                            W <?= (int) ($extrasRow['wides'] ?? 0) ?>,
                                            NB <?= (int) ($extrasRow['no_balls'] ?? 0) ?>
                                        </td>
                                        <td class="text-center"><?= $extrasTotal ?></td>
                                        <td colspan="3"></td>
                                    </tr>
                                <?php endif; ?>
                                <tr>
                                    <td colspan="3">Total</td>
                                    <td class="text-center"><?= $totalRuns + $extrasTotal ?></td>
                                    <td class="text-center"><?= $totalBalls ?></td>
                                    <td class="text-center"><?= $totalFours ?></td>
                                    <td class="text-center"><?= $totalSixes ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="p-3 mb-0">No batting data recorded.</p>
                <?php endif; ?>

                <?php if ($fowRows !== []): ?>
                    <div class="px-3 py-2 border-top">
                        <span class="me-2">Fall of Wickets:</span>
                        <?php foreach ($fowRows as $i => $row): ?>
                            <span class="me-3"><?= $i + 1 ?>-<?= esc((string) $row['score_at_dismissal']) ?> (<?= esc($row['player_name'] ?? '—') ?>, <?= esc((string) $row['over_at_dismissal']) ?> ov)</span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="card-header border-top bg-body-tertiary">
                Bowling — <?= esc($sideLabel($bowlingSide)) ?>
            </div>
            <div class="card-body">
                <?php if ($bowlRows !== []): ?>
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
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
                            <?php
                            $bowlTotalWickets = 0;
                            $bowlTotalRuns = 0;
                            $bowlTotalBalls = 0;
                            foreach ($bowlRows as $bw) {
                                $bowlTotalWickets += (int) ($bw['wickets'] ?? 0);
                                $bowlTotalRuns += (int) ($bw['runs'] ?? 0);
                                $oVal = (string) ($bw['overs'] ?? '0');
                                [$oFull, $oBalls] = array_pad(explode('.', $oVal, 2), 2, '0');
                                $bowlTotalBalls += (int) $oFull * 6 + (int) $oBalls;
                            }
                            $bowlOversDisplay = floor($bowlTotalBalls / 6) . '.' . $bowlTotalBalls % 6;
                            ?>
                            <tfoot>
                                <tr>
                                    <td colspan="2">Total</td>
                                    <td class="text-center"><?= $bowlOversDisplay ?></td>
                                    <td class="text-center text-secondary">—</td>
                                    <td class="text-center"><?= $bowlTotalRuns ?></td>
                                    <td class="text-center fw-bold"><?= $bowlTotalWickets ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-secondary small p-3 mb-0">No bowling data recorded.</p>
                <?php endif; ?>
                <?php $didNotBowl = $didNotBowlByInnings[$inns] ?? []; ?>
                <?php if ($didNotBowl !== []): ?>
                    <div class="px-3 py-2 border-top">
                        <span class="me-1">Did not bowl:</span>
                        <?php foreach ($didNotBowl as $k => $p): ?>
                            <span><?= esc($p['player_name']) ?><?= $k < count($didNotBowl) - 1 ? ',' : '' ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?= $this->endSection() ?>