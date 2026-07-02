<?= $this->extend('admin/template/default') ?>

<?= $this->section('admin_nav') ?><?= ($match ?? null) === null ? 'create-match' : 'matches' ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-card">
    <?php $fieldLockAttr = !empty($isMatchLocked) ? ' disabled' : ''; ?>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-2"><?= esc($heading) ?></h1>
            <p class="text-secondary mb-0"><?= esc($intro) ?></p>
        </div>
        <?php if (($match ?? null) !== null): ?>
            <div class="d-flex gap-2 flex-wrap">
                <a href="<?= site_url('/admin/matches/' . $match['id']) ?>" class="btn btn-primary">Match Overview</a>
            </div>
        <?php endif; ?>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>

    <?php if (!empty($isMatchLocked)): ?>
        <div class="alert alert-warning d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
            <span>Completed match fields are read-only. Continue using the overview page for finance, contributors, expenses, and scoreboard actions.</span>
            <form action="<?= site_url('/admin/matches/' . $match['id'] . '/update-status') ?>" method="post" class="d-flex gap-2 align-items-center flex-shrink-0">
                <?= csrf_field() ?>
                <select name="match_status" class="form-select form-select-sm" style="width:auto">
                    <?php foreach (MATCH_STATUS as $status): ?>
                        <option value="<?= esc($status) ?>" <?= ($match['match_status'] ?? '') === $status ? 'selected' : '' ?>><?= esc(ucfirst($status)) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-sm btn-primary text-nowrap">Update Status</button>
            </form>
        </div>
    <?php endif; ?>

    <?php $errors = session()->getFlashdata('errors') ?? []; ?>

    <?php
    $teamAPlayerIds = array_map('intval', (array) old('team_a_player_ids', $teamAPlayerIds ?? []));
    $teamBPlayerIds = array_map('intval', (array) old('team_b_player_ids', $teamBPlayerIds ?? []));
    $playersById = [];
    $approvedPlayers = [];
    $guestPlayers = [];
    $externalPlayers = [];
    foreach ($players as $p) {
        $playersById[(int) $p['id']] = $p;
        $status = $p['status'] ?? '';
        if ($status === 'approved') {
            $approvedPlayers[] = $p;
        } elseif ($status === 'guest') {
            $guestPlayers[] = $p;
        } elseif ($status === 'external') {
            $externalPlayers[] = $p;
        }
    }
    $assignedIds = array_merge($teamAPlayerIds, $teamBPlayerIds);
    $guestLabel = static function (array $p) use ($playersById): string {
        if (($p['status'] ?? '') !== 'guest' || empty($p['guest_of_player_id']))
            return '';
        $host = $playersById[(int) $p['guest_of_player_id']] ?? null;
        return $host ? ' (Guest - ' . esc($host['name']) . ')' : '';
    };
    $playerDataJson = [];
    foreach ($players as $p) {
        $guestOfName = '';
        if (($p['status'] ?? '') === 'guest' && !empty($p['guest_of_player_id'])) {
            $host = $playersById[(int) $p['guest_of_player_id']] ?? null;
            $guestOfName = $host['name'] ?? '';
        }
        $playerDataJson[(string) $p['id']] = [
            'id' => (string) $p['id'],
            'name' => $p['name'],
            'status' => $p['status'] ?? 'approved',
            'guestOfName' => $guestOfName,
        ];
    }
    ?>

    <form action="<?= esc($formAction) ?>" method="post" id="match-form">
        <?= csrf_field() ?>

        <div class="row g-3">
            <div class="col-md-6">
                <label for="team_name" class="form-label fw-semibold">Team A Name</label>
                <input type="text" class="form-control<?= isset($errors['team_name']) ? ' is-invalid' : '' ?>" id="team_name" name="team_name" value="<?= esc(old('team_name', $match['team_name'] ?? 'Challenger Red')) ?>" <?= $fieldLockAttr ?>>
                <?php if (isset($errors['team_name'])): ?>
                    <div class="invalid-feedback"><?= esc($errors['team_name']) ?></div>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <label for="opponent_name" class="form-label fw-semibold">Team B Name</label>
                <input type="text" class="form-control<?= isset($errors['opponent_name']) ? ' is-invalid' : '' ?>" id="opponent_name" name="opponent_name" value="<?= esc(old('opponent_name', $match['opponent_name'] ?? 'Challenger Blue')) ?>" <?= $fieldLockAttr ?>>
                <?php if (isset($errors['opponent_name'])): ?>
                    <div class="invalid-feedback"><?= esc($errors['opponent_name']) ?></div>
                <?php endif; ?>
            </div>
            <div class="col-md-4">
                <label for="match_type" class="form-label fw-semibold">Match Type</label>
                <select class="form-select<?= isset($errors['match_type']) ? ' is-invalid' : '' ?>" id="match_type" name="match_type" <?= $fieldLockAttr ?>>
                    <?php foreach (['t20', 'test', 't10', 'odi', 'friendly'] as $type): ?>
                        <option value="<?= esc($type) ?>" <?= old('match_type', $match['match_type'] ?? 'limited_overs') === $type ? 'selected' : '' ?>><?= esc(ucwords(str_replace('_', ' ', $type))) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['match_type'])): ?>
                    <div class="invalid-feedback"><?= esc($errors['match_type']) ?></div>
                <?php endif; ?>
            </div>
            <div class="col-md-4">
                <label for="format_overs" class="form-label fw-semibold">Overs</label>
                <input type="number" min="1" class="form-control<?= isset($errors['format_overs']) ? ' is-invalid' : '' ?>" id="format_overs" name="format_overs" value="<?= esc(old('format_overs', (string) ($match['format_overs'] ?? '20'))) ?>" <?= $fieldLockAttr ?>>
                <?php if (isset($errors['format_overs'])): ?>
                    <div class="invalid-feedback"><?= esc($errors['format_overs']) ?></div>
                <?php endif; ?>
            </div>
            <div class="col-md-4">
                <label for="match_status" class="form-label fw-semibold">Status</label>
                <select class="form-select<?= isset($errors['match_status']) ? ' is-invalid' : '' ?>" id="match_status" name="match_status" <?= $fieldLockAttr ?>>
                    <?php foreach (MATCH_STATUS as $status): ?>
                        <option value="<?= esc($status) ?>" <?= old('match_status', $match['match_status'] ?? 'scheduled') === $status ? 'selected' : '' ?>><?= esc(ucfirst($status)) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['match_status'])): ?>
                    <div class="invalid-feedback"><?= esc($errors['match_status']) ?></div>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <label for="venue_id" class="form-label fw-semibold">Venue</label>
                <select class="form-select<?= isset($errors['venue_id']) ? ' is-invalid' : '' ?>" id="venue_id" name="venue_id" <?= $fieldLockAttr ?>>
                    <option value="">Select venue</option>
                    <?php foreach ($venues as $venue): ?>
                        <option value="<?= esc((string) $venue['id']) ?>" <?= (string) old('venue_id', $match['venue_id'] ?? '') === (string) $venue['id'] ? 'selected' : '' ?>><?= esc($venue['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['venue_id'])): ?>
                    <div class="invalid-feedback"><?= esc($errors['venue_id']) ?></div>
                <?php endif; ?>
                <div class="form-text">
                    <a href="<?= site_url('/admin/venues') ?>">Manage venues</a>
                    <?php if ($venues === []): ?>
                        <span class="text-danger ms-2">No venues available yet.</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-6">
                <label for="scheduled_at" class="form-label fw-semibold">Scheduled At</label>
                <?php $scheduledAtValue = old('scheduled_at', isset($match['scheduled_at']) && $match['scheduled_at'] ? str_replace(' ', 'T', substr((string) $match['scheduled_at'], 0, 16)) : ''); ?>
                <input type="datetime-local" class="form-control<?= isset($errors['scheduled_at']) ? ' is-invalid' : '' ?>" id="scheduled_at" name="scheduled_at" value="<?= esc($scheduledAtValue) ?>" <?= $fieldLockAttr ?>>
                <?php if (isset($errors['scheduled_at'])): ?>
                    <div class="invalid-feedback"><?= esc($errors['scheduled_at']) ?></div>
                <?php endif; ?>
            </div>

            <!-- ── Player Pool ── -->
            <div class="col-12">
                <div class="d-flex align-items-baseline gap-2 mb-2">
                    <label class="form-label fw-semibold mb-0">All Players</label>
                    <?php if (empty($isMatchLocked)): ?>
                        <span class="text-secondary small">Drag into a team squad below</span>
                    <?php endif; ?>
                </div>
                <div class="row g-2">
                    <?php
                    $poolSections = [
                        ['id' => 'pool-approved', 'label' => 'Approved', 'color' => 'text-success', 'players' => $approvedPlayers],
                        ['id' => 'pool-guest', 'label' => 'Guest', 'color' => 'text-primary', 'players' => $guestPlayers],
                        ['id' => 'pool-external', 'label' => 'External', 'color' => 'text-secondary', 'players' => $externalPlayers],
                    ];
                    ?>
                    <?php foreach ($poolSections as $key => $section): ?>
                        <div class="col-md-<?= !$key ? 6 : 3 ?>">
                            <div class="border rounded p-2 bg-white h-100">
                                <div class="fw-semibold small mb-2 px-1 <?= esc($section['color']) ?>">
                                    <?= esc($section['label']) ?>
                                    <span class="text-secondary fw-normal">(<?= count($section['players']) ?>)</span>
                                </div>
                                <div class="player-pool<?= $section['id'] === 'pool-approved' ? ' pool-two-col' : '' ?>" id="<?= esc($section['id']) ?>">
                                    <?php foreach ($section['players'] as $p): ?>
                                        <?php if (in_array((int) $p['id'], $assignedIds, true))
                                            continue; ?>
                                        <div class="player-card pool-card bg-body-tertiary" <?= empty($isMatchLocked) ? 'draggable="true"' : '' ?> data-player-id="<?= esc((string) $p['id']) ?>">
                                            ⠿ <?= esc($p['name']) ?>
                                            <?php if (!empty($guestLabel($p))): ?>
                                                <span class="text-secondary ms-1" style="font-size:0.78em"><?= $guestLabel($p) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                    <?php if (empty($section['players'])): ?>
                                        <span class="text-secondary small px-1">None</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- ── Team Squads ── -->
            <?php
            $squadConfigs = [
                [
                    'squad_id' => 'squad-a',
                    'label_id' => 'team_a_squad_label',
                    'name_input_id' => 'team_name',
                    'inputs_id' => 'team-a-inputs',
                    'input_name' => 'team_a_player_ids[]',
                    'captain_id' => 'team_a_captain_id',
                    'captain_name' => 'team_a_captain_id',
                    'captain_old' => old('team_a_captain_id', $teamACaptainId ?? ''),
                    'player_ids' => $teamAPlayerIds,
                    'error_key' => 'team_a_player_ids',
                    'error_cap' => 'team_a_captain_id',
                    'default_label' => 'Team A',
                    'team_label' => old('team_name', $match['team_name'] ?? 'Team A'),
                ],
                [
                    'squad_id' => 'squad-b',
                    'label_id' => 'team_b_squad_label',
                    'name_input_id' => 'opponent_name',
                    'inputs_id' => 'team-b-inputs',
                    'input_name' => 'team_b_player_ids[]',
                    'captain_id' => 'team_b_captain_id',
                    'captain_name' => 'team_b_captain_id',
                    'captain_old' => old('team_b_captain_id', $teamBCaptainId ?? ''),
                    'player_ids' => $teamBPlayerIds,
                    'error_key' => 'team_b_player_ids',
                    'error_cap' => 'team_b_captain_id',
                    'default_label' => 'Team B',
                    'team_label' => old('opponent_name', $match['opponent_name'] ?? 'Team B'),
                ],
            ];
            ?>
            <?php foreach ($squadConfigs as $sq): ?>
                <div class="col-lg-6">
                    <div class="team-panel h-100 border p-3 bg-light">
                        <label class="form-label fw-semibold mb-2" id="<?= esc($sq['label_id']) ?>">
                            <?= esc($sq['team_label']) ?> Squad (<?= count($sq['player_ids']) ?> players)
                        </label>
                        <?php if (isset($errors[$sq['error_key']])): ?>
                            <div class="text-danger small mb-2"><?= esc($errors[$sq['error_key']]) ?></div>
                        <?php endif; ?>
                        <div class="squad-drop-zone mb-3" id="<?= esc($sq['squad_id']) ?>">
                            <div class="squad-drop-hint text-secondary small text-center py-2<?= !empty($sq['player_ids']) ? ' d-none' : '' ?>">Drag players here</div>
                            <?php foreach ($sq['player_ids'] as $pid): ?>
                                <?php if (!isset($playersById[$pid]))
                                    continue; ?>
                                <?php $p = $playersById[$pid]; ?>
                                <div class="player-card squad-card" <?= empty($isMatchLocked) ? 'draggable="true"' : '' ?> data-player-id="<?= esc((string) $pid) ?>">
                                    <?php if (empty($isMatchLocked)): ?>
                                        <span class="drag-handle me-1">&#8597;</span>
                                    <?php endif; ?>
                                    <?= esc($p['name']) ?>
                                    <?php if (!empty($guestLabel($p))): ?>
                                        <span class="text-secondary ms-1" style="font-size:0.78em"><?= $guestLabel($p) ?></span>
                                    <?php endif; ?>
                                    <?php if (empty($isMatchLocked)): ?>
                                        <button type="button" class="remove-player" data-player-id="<?= esc((string) $pid) ?>" title="Remove">&#x2715;</button>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                            <div class="squad-inputs" id="<?= esc($sq['inputs_id']) ?>">
                                <?php foreach ($sq['player_ids'] as $pid): ?>
                                    <input type="hidden" name="<?= esc($sq['input_name']) ?>" value="<?= esc((string) $pid) ?>">
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="<?= esc($sq['captain_id']) ?>" class="form-label fw-semibold">Captain</label>
                            <select class="form-select<?= isset($errors[$sq['error_cap']]) ? ' is-invalid' : '' ?>" id="<?= esc($sq['captain_id']) ?>" name="<?= esc($sq['captain_name']) ?>" <?= $fieldLockAttr ?>>
                                <option value="">Select captain</option>
                                <?php foreach ($sq['player_ids'] as $pid): ?>
                                    <?php if (!isset($playersById[$pid]))
                                        continue; ?>
                                    <?php $cp = $playersById[$pid]; ?>
                                    <?php $cl = esc($cp['name']) . $guestLabel($cp); ?>
                                    <option value="<?= esc((string) $pid) ?>" <?= (string) $sq['captain_old'] === (string) $pid ? 'selected' : '' ?>><?= $cl ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors[$sq['error_cap']])): ?>
                                <div class="invalid-feedback d-block"><?= esc($errors[$sq['error_cap']]) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="col-12">
                <label for="notes" class="form-label fw-semibold">Notes</label>
                <textarea class="form-control" id="notes" name="notes" rows="4" <?= $fieldLockAttr ?>><?= esc(old('notes', $match['notes'] ?? '')) ?></textarea>
            </div>
            <div class="col-12">
                <?php if (empty($isMatchLocked)): ?>
                    <button type="submit" class="btn btn-primary"><?= esc($submitLabel) ?></button>
                <?php else: ?>
                    <div class="text-secondary small">This match is completed, so match fields are displayed for reference only.</div>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .player-card {
        display: flex;
        align-items: center;
        gap: 4px;
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 5px 8px;
        margin-bottom: 5px;
        font-size: 0.875rem;
        user-select: none;
    }

    .player-card[draggable="true"] {
        cursor: grab;
    }

    .player-card[draggable="true"]:active {
        cursor: grabbing;
    }

    .player-card.dragging {
        opacity: 0.3;
    }

    .drag-handle {
        color: #adb5bd;
        flex-shrink: 0;
    }

    .remove-player {
        margin-left: auto;
        flex-shrink: 0;
        background: none;
        border: none;
        color: #adb5bd;
        cursor: pointer;
        padding: 0 2px;
        font-size: 0.85rem;
        line-height: 1;
    }

    .remove-player:hover {
        color: #dc3545;
    }

    .squad-inputs {
        display: none;
    }

    .squad-drop-zone {
        min-height: 80px;
        border: 2px dashed #dee2e6;
        border-radius: 6px;
        padding: 8px;
        transition: border-color 0.15s, background-color 0.15s;
    }

    .squad-drop-zone.drag-over {
        border-color: #0d6efd;
        background-color: #eef3ff;
    }

    .player-pool {
        min-height: 36px;
        border-radius: 4px;
        transition: background-color 0.15s, outline 0.15s;
    }

    .player-pool.pool-two-col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 5px;
        align-content: start;
    }

    .player-pool.pool-two-col .player-card {
        margin-bottom: 0;
    }

    .player-pool.drag-over {
        background-color: #f0fff4;
        outline: 2px dashed #198754;
        outline-offset: 2px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    (function () {
        const isMatchLocked = <?= !empty($isMatchLocked) ? 'true' : 'false' ?>;
        const PLAYERS = <?= json_encode($playerDataJson, JSON_HEX_TAG | JSON_HEX_APOS) ?>;

        if (isMatchLocked) return;

        const squadA = document.getElementById('squad-a');
        const squadB = document.getElementById('squad-b');
        const pools = {
            approved: document.getElementById('pool-approved'),
            guest: document.getElementById('pool-guest'),
            external: document.getElementById('pool-external'),
        };

        let dragId = null;

        const escHtml = (s) => String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');

        const playerPool = (id) => {
            const status = PLAYERS[id]?.status || 'approved';
            return pools[status] || pools.approved;
        };

        const makePoolCard = (id) => {
            const p = PLAYERS[id];
            if (!p) return null;
            const el = document.createElement('div');
            el.className = 'player-card pool-card';
            el.draggable = true;
            el.dataset.playerId = id;
            let html = escHtml(p.name);
            if (p.status === 'guest' && p.guestOfName) {
                html += ` <span class="text-secondary ms-1" style="font-size:0.78em">(Guest - ${escHtml(p.guestOfName)})</span>`;
            }
            el.innerHTML = html;
            attachDrag(el);
            return el;
        };

        const makeSquadCard = (id) => {
            const p = PLAYERS[id];
            if (!p) return null;
            const el = document.createElement('div');
            el.className = 'player-card squad-card';
            el.draggable = true;
            el.dataset.playerId = id;
            let html = '<span class="drag-handle me-1">&#8597;</span>' + escHtml(p.name);
            if (p.status === 'guest' && p.guestOfName) {
                html += ` <span class="text-secondary ms-1" style="font-size:0.78em">(Guest - ${escHtml(p.guestOfName)})</span>`;
            }
            html += `<button type="button" class="remove-player" data-player-id="${escHtml(id)}" title="Remove">&#x2715;</button>`;
            el.innerHTML = html;
            attachDrag(el);
            el.querySelector('.remove-player').addEventListener('click', (e) => {
                e.stopPropagation();
                removeFromSquads(id);
                playerPool(id).appendChild(makePoolCard(id));
                sync();
            });
            return el;
        };

        const removeFromSquads = (id) => {
            [squadA, squadB].forEach(sq => {
                sq.querySelectorAll(`.squad-card[data-player-id="${id}"]`).forEach(el => el.remove());
            });
        };

        const removeFromPools = (id) => {
            Object.values(pools).forEach(pool => {
                pool.querySelectorAll(`.pool-card[data-player-id="${id}"]`).forEach(el => el.remove());
            });
        };

        const assignToSquad = (id, squadEl) => {
            if (squadEl.querySelector(`.squad-card[data-player-id="${id}"]`)) return;
            removeFromSquads(id);
            removeFromPools(id);
            const inputContainer = squadEl.querySelector('.squad-inputs');
            squadEl.insertBefore(makeSquadCard(id), inputContainer);
            sync();
        };

        const syncInputs = (squadEl, name) => {
            const container = squadEl.querySelector('.squad-inputs');
            if (!container) return;
            container.innerHTML = '';
            squadEl.querySelectorAll('.squad-card').forEach(card => {
                const inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = name;
                inp.value = card.dataset.playerId;
                container.appendChild(inp);
            });
        };

        const syncCaptain = (squadEl, selectId) => {
            const sel = document.getElementById(selectId);
            if (!sel) return;
            const prev = sel.value;
            sel.innerHTML = '<option value="">Select captain</option>';
            squadEl.querySelectorAll('.squad-card').forEach(card => {
                const id = card.dataset.playerId;
                const p = PLAYERS[id] || { name: id, status: '', guestOfName: '' };
                let label = p.name;
                if (p.status === 'guest' && p.guestOfName) label += ` (Guest - ${p.guestOfName})`;
                sel.appendChild(new Option(label, id, false, id === prev));
            });
            if (!squadEl.querySelector(`.squad-card[data-player-id="${prev}"]`)) sel.value = '';
        };

        const syncLabel = (squadEl, labelId, nameInputId) => {
            const label = document.getElementById(labelId);
            if (!label) return;
            const count = squadEl.querySelectorAll('.squad-card').length;
            const name = document.getElementById(nameInputId)?.value || '';
            label.textContent = `${name} Squad (${count} player${count !== 1 ? 's' : ''})`;
        };

        const syncHint = (squadEl) => {
            const hint = squadEl.querySelector('.squad-drop-hint');
            if (hint) hint.classList.toggle('d-none', squadEl.querySelectorAll('.squad-card').length > 0);
        };

        const sync = () => {
            syncInputs(squadA, 'team_a_player_ids[]');
            syncInputs(squadB, 'team_b_player_ids[]');
            syncCaptain(squadA, 'team_a_captain_id');
            syncCaptain(squadB, 'team_b_captain_id');
            syncLabel(squadA, 'team_a_squad_label', 'team_name');
            syncLabel(squadB, 'team_b_squad_label', 'opponent_name');
            syncHint(squadA);
            syncHint(squadB);
        };

        const attachDrag = (el) => {
            el.addEventListener('dragstart', (e) => {
                dragId = el.dataset.playerId;
                el.classList.add('dragging');
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/plain', dragId);
            });
            el.addEventListener('dragend', () => {
                el.classList.remove('dragging');
                document.querySelectorAll('.drag-over').forEach(z => z.classList.remove('drag-over'));
            });
        };

        const makeDropZone = (el, onDrop) => {
            el.addEventListener('dragover', (e) => {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
                el.classList.add('drag-over');
            });
            el.addEventListener('dragleave', (e) => {
                if (!el.contains(e.relatedTarget)) el.classList.remove('drag-over');
            });
            el.addEventListener('drop', (e) => {
                e.preventDefault();
                el.classList.remove('drag-over');
                if (dragId) onDrop(dragId);
                dragId = null;
            });
        };

        makeDropZone(squadA, (id) => assignToSquad(id, squadA));
        makeDropZone(squadB, (id) => assignToSquad(id, squadB));

        Object.entries(pools).forEach(([status, pool]) => {
            makeDropZone(pool, (id) => {
                if (playerPool(id) !== pool) return;
                if (pool.querySelector(`.pool-card[data-player-id="${id}"]`)) return;
                removeFromSquads(id);
                removeFromPools(id);
                pool.appendChild(makePoolCard(id));
                sync();
            });
        });

        // Wire up server-rendered cards
        document.querySelectorAll('.pool-card').forEach(el => attachDrag(el));
        document.querySelectorAll('.squad-card').forEach(el => {
            attachDrag(el);
            el.querySelector('.remove-player')?.addEventListener('click', (e) => {
                e.stopPropagation();
                const id = el.dataset.playerId;
                removeFromSquads(id);
                playerPool(id).appendChild(makePoolCard(id));
                sync();
            });
        });

        // Keep squad labels live when team name fields change
        document.getElementById('team_name')?.addEventListener('input', () => syncLabel(squadA, 'team_a_squad_label', 'team_name'));
        document.getElementById('opponent_name')?.addEventListener('input', () => syncLabel(squadB, 'team_b_squad_label', 'opponent_name'));

        sync();
    }());
</script>
<?= $this->endSection() ?>