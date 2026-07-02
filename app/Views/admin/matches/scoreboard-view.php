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

$sidePlayers = static function (string $side) use ($teamAPlayers, $teamBPlayers): array {
    return $side === 'team_a' ? $teamAPlayers : $teamBPlayers;
};
?>
<div class="content-card">
    <div class="panel mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h1 class="h2 fw-bold mb-2"><?= esc($match['team_name']) ?> vs <?= esc($match['opponent_name']) ?></h1>
                <p class="text-secondary mb-0">Match #<?= $matchNumber ?></p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="<?= site_url('/admin/matches/' . $match['id']) ?>" class="btn btn-light border">Back to Match</a>
                <form action="<?= site_url('/admin/matches/' . $match['id'] . '/complete') ?>" method="post" onsubmit="return confirm('Mark this match as completed? The scoreboard will become read-only.');">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-primary">Complete Match</button>
                </form>
            </div>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header bg-body-tertiary">Toss</div>
        <form action="<?= site_url('/admin/matches/' . $match['id'] . '/scoreboard/toss') ?>" method="post" class="card-body">
            <?= csrf_field() ?>
            <div class="row g-2 align-items-end">
                <div class="col-sm-4">
                    <label class="form-label">Winner</label>
                    <select name="toss_winner" class="form-select">
                        <option value="">—</option>
                        <option value="team_a" <?= ($match['toss_winner'] ?? '') === 'team_a' ? 'selected' : '' ?>><?= esc($match['team_name']) ?></option>
                        <option value="team_b" <?= ($match['toss_winner'] ?? '') === 'team_b' ? 'selected' : '' ?>><?= esc($match['opponent_name']) ?></option>
                    </select>
                </div>
                <div class="col-sm-4">
                    <label class="form-label">Decision</label>
                    <select name="toss_decision" class="form-select">
                        <option value="">—</option>
                        <option value="bat" <?= ($match['toss_decision'] ?? '') === 'bat' ? 'selected' : '' ?>>Bat first</option>
                        <option value="bowl" <?= ($match['toss_decision'] ?? '') === 'bowl' ? 'selected' : '' ?>>Bowl first</option>
                    </select>
                </div>
                <div class="col-sm-auto">
                    <button type="submit" class="btn btn-primary">Save Toss</button>
                </div>
            </div>
        </form>
    </div>

    <?php foreach ([1, 2] as $inns): ?>
        <?php
        $battingSide = $inns === 1 ? $inns1BattingSide : $inns2BattingSide;
        $bowlingSide = $inns === 1 ? $inns1BowlingSide : $inns2BowlingSide;
        $batters = $sidePlayers($battingSide);
        $bowlers = $sidePlayers($bowlingSide);
        $existBat = $batsmenByInnings[$inns] ?? [];
        $existBowl = $bowlersByInnings[$inns] ?? [];
        ?>
        <div class="card mb-4">
            <div class="card-header bg-body-tertiary">
                Innings <?= $inns ?>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <div class="card-title">Batting</div>
                    <p class="text-secondary small mb-3"><?= esc($sideLabel($battingSide)) ?></p>
                    <?php if ($batters === []): ?>
                        <p class="text-secondary small">No players assigned to this team yet. Add them via <a href="<?= site_url('/admin/matches/' . $match['id'] . '/edit') ?>">Edit Match</a>.</p>
                    <?php else: ?>
                        <form action="<?= site_url('/admin/matches/' . $match['id'] . '/scoreboard/batting/' . $inns . '/save') ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="table-responsive">
                                <table class="table align-middle mb-3">
                                    <thead>
                                        <tr>
                                            <th style="width:32px"></th>
                                            <th>#</th>
                                            <th style="min-width:140px">Player</th>
                                            <th style="width:70px">Runs</th>
                                            <th style="width:70px">Balls</th>
                                            <th style="width:60px">4s</th>
                                            <th style="width:60px">6s</th>
                                            <th style="min-width:130px">Dismissal</th>
                                            <th style="min-width:160px">Bowler</th>
                                            <th style="min-width:160px">Fielder</th>
                                        </tr>
                                    </thead>
                                    <tbody class="sortable-batting" data-innings="<?= $inns ?>">
                                        <?php
                                        usort($batters, static function ($a, $b) use ($existBat) {
                                            $aOrder = isset($existBat[(int) $a['player_id']]) ? (int) $existBat[(int) $a['player_id']]['sort_order'] : PHP_INT_MAX;
                                            $bOrder = isset($existBat[(int) $b['player_id']]) ? (int) $existBat[(int) $b['player_id']]['sort_order'] : PHP_INT_MAX;
                                            return $aOrder <=> $bOrder;
                                        });
                                        ?>
                                        <?php foreach ($batters as $key => $player): ?>
                                            <?php
                                            $pid = (int) $player['player_id'];
                                            $ex = $existBat[$pid] ?? [];
                                            ?>
                                            <tr>
                                                <td class="drag-handle text-secondary" style="cursor:grab">⠿</td>
                                                <td><?= $key + 1 ?></td>
                                                <td class="fw-semibold">
                                                    <?= esc($player['name']) ?>
                                                    <?php if (($player['status'] ?? '') === 'guest' && !empty($player['guest_of_name'])): ?>
                                                        <span class="text-secondary small">(Guest - <?= esc($player['guest_of_name']) ?>)</span>
                                                    <?php endif; ?>
                                                </td>
                                                <input type="hidden" name="batsmen[<?= $pid ?>][sort_order]" class="sort-order-input" value="<?= esc((string) ($ex['sort_order'] ?? 0)) ?>">
                                                <td><input type="number" name="batsmen[<?= $pid ?>][runs]" class="form-control form-control-sm" value="<?= esc((string) ($ex['runs'] ?? '')) ?>" min="0" placeholder="0"></td>
                                                <td><input type="number" name="batsmen[<?= $pid ?>][balls]" class="form-control form-control-sm" value="<?= esc((string) ($ex['balls'] ?? '')) ?>" min="0" placeholder="0"></td>
                                                <td><input type="number" name="batsmen[<?= $pid ?>][fours]" class="form-control form-control-sm" value="<?= esc((string) ($ex['fours'] ?? '')) ?>" min="0" placeholder="0"></td>
                                                <td><input type="number" name="batsmen[<?= $pid ?>][sixes]" class="form-control form-control-sm" value="<?= esc((string) ($ex['sixes'] ?? '')) ?>" min="0" placeholder="0"></td>
                                                <td>
                                                    <select name="batsmen[<?= $pid ?>][dismissal_mode]" class="form-select form-select-sm">
                                                        <option value="">—</option>
                                                        <?php foreach (['bowled', 'caught', 'lbw', 'run_out', 'stumped', 'hit_wicket', 'not_out', 'retired_hurt'] as $mode): ?>
                                                            <option value="<?= $mode ?>" <?= ($ex['dismissal_mode'] ?? '') === $mode ? 'selected' : '' ?>><?= ucwords(str_replace('_', ' ', $mode)) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="batsmen[<?= $pid ?>][dismissal_by]" class="form-select form-select-sm">
                                                        <option value="">—</option>
                                                        <?php foreach ($bowlers as $bowler): ?>
                                                            <option value="<?= esc((string) $bowler['player_id']) ?>" <?= (int) ($ex['dismissal_by'] ?? 0) === (int) $bowler['player_id'] ? 'selected' : '' ?>>
                                                                <?= esc($bowler['name']) ?>                 <?= ($bowler['status'] ?? '') === 'guest' && !empty($bowler['guest_of_name']) ? ' (Guest - ' . esc($bowler['guest_of_name']) . ')' : '' ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="batsmen[<?= $pid ?>][fielder_id]" class="form-select form-select-sm">
                                                        <option value="">—</option>
                                                        <?php foreach ($bowlers as $fielder): ?>
                                                            <option value="<?= esc((string) $fielder['player_id']) ?>" <?= (int) ($ex['fielder_id'] ?? 0) === (int) $fielder['player_id'] ? 'selected' : '' ?>>
                                                                <?= esc($fielder['name']) ?>                 <?= ($fielder['status'] ?? '') === 'guest' && !empty($fielder['guest_of_name']) ? ' (Guest - ' . esc($fielder['guest_of_name']) . ')' : '' ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php $exExtras = $extrasByInnings[$inns] ?? []; ?>
                            <div class="mt-3 mb-3">
                                <div class="card-title mb-2">Extras</div>
                                <div class="d-flex gap-3 flex-wrap">
                                    <div>
                                        <label class="form-label small mb-1">Byes</label>
                                        <input type="number" name="extras[byes]" class="form-control form-control-sm" style="width:80px" value="<?= esc((string) ($exExtras['byes'] ?? 0)) ?>" min="0">
                                    </div>
                                    <div>
                                        <label class="form-label small mb-1">Leg Byes</label>
                                        <input type="number" name="extras[leg_byes]" class="form-control form-control-sm" style="width:80px" value="<?= esc((string) ($exExtras['leg_byes'] ?? 0)) ?>" min="0">
                                    </div>
                                    <div>
                                        <label class="form-label small mb-1">Wides</label>
                                        <input type="number" name="extras[wides]" class="form-control form-control-sm" style="width:80px" value="<?= esc((string) ($exExtras['wides'] ?? 0)) ?>" min="0">
                                    </div>
                                    <div>
                                        <label class="form-label small mb-1">No Balls</label>
                                        <input type="number" name="extras[no_balls]" class="form-control form-control-sm" style="width:80px" value="<?= esc((string) ($exExtras['no_balls'] ?? 0)) ?>" min="0">
                                    </div>
                                </div>
                            </div>
                            <?php
                            $batTotalRuns = 0;
                            $batTotalWickets = 0;
                            foreach ($existBat as $b) {
                                $batTotalRuns += (int) ($b['runs'] ?? 0);
                                $dm = $b['dismissal_mode'] ?? '';
                                if ($dm !== '' && !in_array($dm, ['not_out', 'retired_hurt'], true)) {
                                    $batTotalWickets++;
                                }
                            }
                            $extrasSum = (int) ($exExtras['byes'] ?? 0) + (int) ($exExtras['leg_byes'] ?? 0) + (int) ($exExtras['wides'] ?? 0) + (int) ($exExtras['no_balls'] ?? 0);
                            $batTotalRuns += $extrasSum;
                            if ($existBat !== []):
                                ?>
                                <div class="alert alert-secondary py-2 px-3 mb-3 d-flex align-items-center gap-3">
                                    <span class="fw-bold fs-5"><?= $batTotalRuns ?>/<?= $batTotalWickets ?></span>
                                    <span class="text-secondary small">Extras: <?= $extrasSum ?></span>
                                </div>
                            <?php endif; ?>
                            <button type="submit" class="btn btn-primary">Save Batting</button>
                        </form>
                    <?php endif; ?>
                </div>
                <div class="mb-4">
                    <div class="card-title">Bowling</div>
                    <p class="text-secondary small mb-3"><?= esc($sideLabel($bowlingSide)) ?></p>
                    <?php if ($bowlers === []): ?>
                        <p class="text-secondary small">No players assigned to this team yet.</p>
                    <?php else: ?>
                        <form action="<?= site_url('/admin/matches/' . $match['id'] . '/scoreboard/bowling/' . $inns . '/save') ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="table-responsive">
                                <table class="table align-middle mb-3">
                                    <thead>
                                        <tr>
                                            <th style="width:32px"></th>
                                            <th>#</th>
                                            <th style="min-width:140px">Player</th>
                                            <th style="width:80px">Overs</th>
                                            <th style="width:80px">Maidens</th>
                                            <th style="width:70px">Runs</th>
                                            <th style="width:80px">Wickets</th>
                                        </tr>
                                    </thead>
                                    <tbody class="sortable-bowling" data-innings="<?= $inns ?>">
                                        <?php
                                        usort($bowlers, static function ($a, $b) use ($existBowl) {
                                            $aOrder = isset($existBowl[(int) $a['player_id']]) ? (int) $existBowl[(int) $a['player_id']]['sort_order'] : PHP_INT_MAX;
                                            $bOrder = isset($existBowl[(int) $b['player_id']]) ? (int) $existBowl[(int) $b['player_id']]['sort_order'] : PHP_INT_MAX;
                                            return $aOrder <=> $bOrder;
                                        });
                                        ?>
                                        <?php foreach ($bowlers as $key => $player): ?>
                                            <?php
                                            $pid = (int) $player['player_id'];
                                            $ex = $existBowl[$pid] ?? [];
                                            ?>
                                            <tr>
                                                <td class="drag-handle text-secondary" style="cursor:grab">⠿</td>
                                                <td><?= $key + 1 ?></td>
                                                <td class="fw-semibold">
                                                    <?= esc($player['name']) ?>
                                                    <?php if (($player['status'] ?? '') === 'guest' && !empty($player['guest_of_name'])): ?>
                                                        <span class="text-secondary small">(Guest - <?= esc($player['guest_of_name']) ?>)</span>
                                                    <?php endif; ?>
                                                </td>
                                                <input type="hidden" name="bowlers[<?= $pid ?>][sort_order]" class="sort-order-input" value="<?= esc((string) ($ex['sort_order'] ?? 0)) ?>">
                                                <td><input type="number" name="bowlers[<?= $pid ?>][overs]" class="form-control form-control-sm" value="<?= esc((string) ($ex['overs'] ?? '')) ?>" min="0" step="0.1" placeholder="0"></td>
                                                <td><input type="number" name="bowlers[<?= $pid ?>][maidens]" class="form-control form-control-sm" value="<?= esc((string) ($ex['maidens'] ?? '')) ?>" min="0" placeholder="0"></td>
                                                <td><input type="number" name="bowlers[<?= $pid ?>][runs]" class="form-control form-control-sm" value="<?= esc((string) ($ex['runs'] ?? '')) ?>" min="0" placeholder="0"></td>
                                                <td><input type="number" name="bowlers[<?= $pid ?>][wickets]" class="form-control form-control-sm" value="<?= esc((string) ($ex['wickets'] ?? '')) ?>" min="0" placeholder="0"></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php
                            $bowlTotalWickets = 0;
                            $bowlTotalRuns = 0;
                            $bowlTotalBalls = 0;
                            foreach ($existBowl as $bw) {
                                $bowlTotalWickets += (int) ($bw['wickets'] ?? 0);
                                $bowlTotalRuns += (int) ($bw['runs'] ?? 0);
                                $oVal = (string) ($bw['overs'] ?? '0');
                                [$oFull, $oBalls] = array_pad(explode('.', $oVal, 2), 2, '0');
                                $bowlTotalBalls += (int) $oFull * 6 + (int) $oBalls;
                            }
                            $bowlOversDisplay = floor($bowlTotalBalls / 6) . '.' . ($bowlTotalBalls % 6);
                            if ($existBowl !== []):
                                ?>
                                <div class="alert alert-secondary py-2 px-3 mb-3 d-flex align-items-center gap-3">
                                    <span class="fw-bold"><?= $bowlTotalWickets ?> wkt<?= $bowlTotalWickets !== 1 ? 's' : '' ?></span>
                                    <span class="text-secondary small"><?= $bowlTotalRuns ?> runs conceded</span>
                                    <span class="text-secondary small"><?= $bowlOversDisplay ?> overs</span>
                                </div>
                            <?php endif; ?>
                            <button type="submit" class="btn btn-primary">Save Bowling</button>
                        </form>
                    <?php endif; ?>
                </div>
                <hr>
                <div class="card-title mb-3">Fall of Wickets</div>
                <?php $innsWickets = array_values(array_filter($wickets, static fn($w) => (int) $w['innings_number'] === $inns)); ?>
                <?php $fowPlayers = json_encode(array_map(static fn($p) => ['id' => (int) $p['player_id'], 'name' => $p['name']], $batters)); ?>
                <form action="<?= site_url('/admin/matches/' . $match['id'] . '/scoreboard/wickets/' . $inns . '/save') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="table-responsive mb-2">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th style="width:36px">#</th>
                                    <th>Player</th>
                                    <th style="width:90px">Score</th>
                                    <th style="width:90px">Over</th>
                                    <th style="width:50px"></th>
                                </tr>
                            </thead>
                            <tbody id="fow-tbody-<?= $inns ?>" data-innings="<?= $inns ?>" data-players="<?= esc($fowPlayers) ?>" data-next-index="<?= count($innsWickets) ?>">
                                <?php foreach ($innsWickets as $i => $row): ?>
                                    <tr>
                                        <td class="fow-num"><?= $i + 1 ?></td>
                                        <td>
                                            <select name="wickets[<?= $i ?>][player_id]" class="form-select form-select-sm fow-player-select" required>
                                                <option value="">Select player</option>
                                                <?php foreach ($batters as $player): ?>
                                                    <option value="<?= esc((string) $player['player_id']) ?>" <?= (int) $row['player_id'] === (int) $player['player_id'] ? 'selected' : '' ?>>
                                                        <?= esc($player['name']) ?>             <?= ($player['status'] ?? '') === 'guest' && !empty($player['guest_of_name']) ? ' (Guest - ' . esc($player['guest_of_name']) . ')' : '' ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td><input type="number" name="wickets[<?= $i ?>][score_at_dismissal]" class="form-control form-control-sm" value="<?= esc((string) $row['score_at_dismissal']) ?>" min="0" required></td>
                                        <td><input type="number" name="wickets[<?= $i ?>][over_at_dismissal]" class="form-control form-control-sm" value="<?= esc((string) $row['over_at_dismissal']) ?>" min="0" step="0.1" required></td>
                                        <td><button type="button" class="btn btn-sm btn-outline-danger fow-remove-row">✕</button></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary fow-add-row" data-innings="<?= $inns ?>">+ Add Row</button>
                        <button type="submit" class="btn btn-primary">Save FOW</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
    /* ─ ─ Drag-and-drop sort ── */
    function updateSortOrders(tbody) {
        tbody.querySelectorAll('tr').forEach(function (tr, index) {
            var input = tr.querySelector('.sort-order-input');
            if (input) { input.value = index + 1; }
        });
    }

    document.querySelectorAll('.sortable-batting, .sortable-bowling').forEach(function (tbody) {
        Sortable.create(tbody, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function () { updateSortOrders(tbody); },
        });
    });

    /* ─ ─ Fall of Wickets ── */
    function fowRenumber(tbody) {
        tbody.querySelectorAll('tr').forEach(function (tr, i) {
            var cell = tr.querySelector('.fow-num');
            if (cell) { cell.textContent = i + 1; }
        });
    }

    function fowSyncSelects(tbody) {
        var selects = Array.from(tbody.querySelectorAll('.fow-player-select'));
        var selected = {};
        selects.forEach(function (sel) { if (sel.value) { selected[sel.value] = true; } });
        selects.forEach(function (sel) {
            sel.querySelectorAll('option').forEach(function (opt) {
                if (opt.value === '') { return; }
                opt.hidden = selected[opt.value] && opt.value !== sel.value;
            });
        });
    }

    function fowBuildRow(tbody, index) {
        var players = JSON.parse(tbody.dataset.players || '[]');
        var opts = '<option value="">Select player</option>' +
            players.map(function (p) {
                return '<option value="' + p.id + '">' + String(p.name).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</option>';
            }).join('');
        var tr = document.createElement('tr');
        tr.innerHTML =
            '<td class="fow-num"></td>' +
            '<td><select name="wickets[' + index + '][player_id]" class="form-select form-select-sm fow-player-select" required>' + opts + '</select></td>' +
            '<td><input type="number" name="wickets[' + index + '][score_at_dismissal]" class="form-control form-control-sm" value="0" min="0" required></td>' +
            '<td><input type="number" name="wickets[' + index + '][over_at_dismissal]" class="form-control form-control-sm" value="0" min="0" step="0.1" required></td>' +
            '<td><button type="button" class="btn btn-sm btn-outline-danger fow-remove-row">✕</button></td>';
        return tr;
    }

    document.querySelectorAll('[id^="fow-tbody-"]').forEach(function (tbody) {
        fowRenumber(tbody);
        fowSyncSelects(tbody);

        tbody.addEventListener('change', function (e) {
            if (e.target.classList.contains('fow-player-select')) {
                fowSyncSelects(tbody);
            }
        });

        tbody.addEventListener('click', function (e) {
            if (e.target.classList.contains('fow-remove-row')) {
                e.target.closest('tr').remove();
                fowRenumber(tbody);
                fowSyncSelects(tbody);
            }
        });
    });

    document.querySelectorAll('.fow-add-row').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var tbody = document.getElementById('fow-tbody-' + btn.dataset.innings);
            var nextIndex = parseInt(tbody.dataset.nextIndex || '0', 10);
            tbody.appendChild(fowBuildRow(tbody, nextIndex));
            tbody.dataset.nextIndex = nextIndex + 1;
            fowRenumber(tbody);
            fowSyncSelects(tbody);
        });
    });
</script>
<?= $this->endSection() ?>