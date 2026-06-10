<?= $this->extend('admin/default') ?>

<?= $this->section('title') ?><?= esc($pageTitle) ?><?= $this->endSection() ?>

<?= $this->section('admin_nav') ?><?= ($match ?? null) === null ? 'create-match' : 'matches' ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-card">
    <?php $fieldLockAttr = ! empty($isMatchLocked) ? ' disabled' : ''; ?>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-2"><?= esc($heading) ?></h1>
            <p class="text-secondary mb-0"><?= esc($intro) ?></p>
        </div>
        <?php if (($match ?? null) !== null): ?>
            <div class="d-flex gap-2 flex-wrap">
                <a href="<?= site_url('/admin/matches/' . $match['id']) ?>" class="btn btn-light border">Match Overview</a>
            </div>
        <?php endif; ?>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>

    <?php if (! empty($isMatchLocked)): ?>
        <div class="alert alert-warning">Completed match fields are read-only. Continue using the overview page for finance, contributors, expenses, and scoreboard actions.</div>
    <?php endif; ?>

    <?php $errors = session()->getFlashdata('errors') ?? []; ?>

    <form action="<?= esc($formAction) ?>" method="post">
        <?= csrf_field() ?>

        <div class="row g-3">
            <div class="col-md-6">
                <label for="team_name" class="form-label fw-semibold">Team A Name</label>
                <input type="text" class="form-control<?= isset($errors['team_name']) ? ' is-invalid' : '' ?>" id="team_name" name="team_name" value="<?= esc(old('team_name', $match['team_name'] ?? 'Evergreen Green')) ?>" <?= $fieldLockAttr ?>>
                <?php if (isset($errors['team_name'])): ?>
                    <div class="invalid-feedback"><?= esc($errors['team_name']) ?></div>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <label for="opponent_name" class="form-label fw-semibold">Team B Name</label>
                <input type="text" class="form-control<?= isset($errors['opponent_name']) ? ' is-invalid' : '' ?>" id="opponent_name" name="opponent_name" value="<?= esc(old('opponent_name', $match['opponent_name'] ?? 'Evergreen Gold')) ?>" <?= $fieldLockAttr ?>>
                <?php if (isset($errors['opponent_name'])): ?>
                    <div class="invalid-feedback"><?= esc($errors['opponent_name']) ?></div>
                <?php endif; ?>
            </div>
            <div class="col-md-4">
                <label for="match_type" class="form-label fw-semibold">Match Type</label>
                <select class="form-select<?= isset($errors['match_type']) ? ' is-invalid' : '' ?>" id="match_type" name="match_type" <?= $fieldLockAttr ?>>
                    <?php foreach (['limited_overs', 'test', 't10', 'odi', 't20', 'friendly'] as $type): ?>
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
                    <?php foreach (['scheduled', 'live', 'completed', 'abandoned', 'archived'] as $status): ?>
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
            <?php $teamAPlayerIds = array_map('intval', (array) old('team_a_player_ids', $teamAPlayerIds ?? [])); ?>
            <?php $teamBPlayerIds = array_map('intval', (array) old('team_b_player_ids', $teamBPlayerIds ?? [])); ?>
            <?php $teamASquadCount = count($teamAPlayerIds); ?>
            <?php $teamBSquadCount = count($teamBPlayerIds); ?>
            <?php $playersById = []; ?>
            <?php foreach ($players as $player): ?>
                <?php $playersById[(int) $player['id']] = $player; ?>
            <?php endforeach; ?>
            <div class="col-lg-6">
                <div class="team-panel h-100">
                    <label class="form-label fw-semibold" id="team_a_squad_label">Team A Squad (<?= esc((string) $teamASquadCount) ?> checked)</label>
                    <div class="panel squad-box p-3 mb-3">
                        <div class="row g-2">
                            <?php foreach ($players as $player): ?>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input team-a-player" type="checkbox" id="team_a_player_<?= esc((string) $player['id']) ?>" name="team_a_player_ids[]" value="<?= esc((string) $player['id']) ?>" <?= in_array((int) $player['id'], $teamAPlayerIds, true) ? 'checked' : '' ?><?= $fieldLockAttr ?>>
                                        <label class="form-check-label" for="team_a_player_<?= esc((string) $player['id']) ?>">
                                            <?= esc($player['name']) ?>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php if (isset($errors['team_a_player_ids'])): ?>
                        <div class="text-danger small mb-3"><?= esc($errors['team_a_player_ids']) ?></div>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label for="team_a_captain_id" class="form-label fw-semibold">Team A Captain</label>
                        <select class="form-select<?= isset($errors['team_a_captain_id']) ? ' is-invalid' : '' ?>" id="team_a_captain_id" name="team_a_captain_id" <?= $fieldLockAttr ?>>
                            <option value="">Select captain</option>
                            <?php foreach ($teamAPlayerIds as $playerId): ?>
                                <?php if (! isset($playersById[$playerId])) {
                                    continue;
                                } ?>
                                <option value="<?= esc((string) $playerId) ?>" <?= old('team_a_captain_id', $teamACaptainId ?? '') == $playerId ? 'selected' : '' ?>><?= esc($playersById[$playerId]['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['team_a_captain_id'])): ?>
                            <div class="invalid-feedback d-block"><?= esc($errors['team_a_captain_id']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label for="team_a_keeper_id" class="form-label fw-semibold">Team A Wicketkeeper</label>
                        <select class="form-select<?= isset($errors['team_a_keeper_id']) ? ' is-invalid' : '' ?>" id="team_a_keeper_id" name="team_a_keeper_id" <?= $fieldLockAttr ?>>
                            <option value="">Select wicketkeeper</option>
                            <?php foreach ($teamAPlayerIds as $playerId): ?>
                                <?php if (! isset($playersById[$playerId])) {
                                    continue;
                                } ?>
                                <option value="<?= esc((string) $playerId) ?>" <?= old('team_a_keeper_id', $teamAKeeperId ?? '') == $playerId ? 'selected' : '' ?>><?= esc($playersById[$playerId]['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['team_a_keeper_id'])): ?>
                            <div class="invalid-feedback d-block"><?= esc($errors['team_a_keeper_id']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="team-panel h-100">
                    <label class="form-label fw-semibold" id="team_b_squad_label">Team B Squad (<?= esc((string) $teamBSquadCount) ?> checked)</label>
                    <div class="panel squad-box p-3 mb-3">
                        <div class="row g-2">
                            <?php foreach ($players as $player): ?>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input team-b-player" type="checkbox" id="team_b_player_<?= esc((string) $player['id']) ?>" name="team_b_player_ids[]" value="<?= esc((string) $player['id']) ?>" <?= in_array((int) $player['id'], $teamBPlayerIds, true) ? 'checked' : '' ?><?= $fieldLockAttr ?>>
                                        <label class="form-check-label" for="team_b_player_<?= esc((string) $player['id']) ?>">
                                            <?= esc($player['name']) ?>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php if (isset($errors['team_b_player_ids'])): ?>
                        <div class="text-danger small mb-3"><?= esc($errors['team_b_player_ids']) ?></div>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label for="team_b_captain_id" class="form-label fw-semibold">Team B Captain</label>
                        <select class="form-select<?= isset($errors['team_b_captain_id']) ? ' is-invalid' : '' ?>" id="team_b_captain_id" name="team_b_captain_id" <?= $fieldLockAttr ?>>
                            <option value="">Select captain</option>
                            <?php foreach ($teamBPlayerIds as $playerId): ?>
                                <?php if (! isset($playersById[$playerId])) {
                                    continue;
                                } ?>
                                <option value="<?= esc((string) $playerId) ?>" <?= old('team_b_captain_id', $teamBCaptainId ?? '') == $playerId ? 'selected' : '' ?>><?= esc($playersById[$playerId]['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['team_b_captain_id'])): ?>
                            <div class="invalid-feedback d-block"><?= esc($errors['team_b_captain_id']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label for="team_b_keeper_id" class="form-label fw-semibold">Team B Wicketkeeper</label>
                        <select class="form-select<?= isset($errors['team_b_keeper_id']) ? ' is-invalid' : '' ?>" id="team_b_keeper_id" name="team_b_keeper_id" <?= $fieldLockAttr ?>>
                            <option value="">Select wicketkeeper</option>
                            <?php foreach ($teamBPlayerIds as $playerId): ?>
                                <?php if (! isset($playersById[$playerId])) {
                                    continue;
                                } ?>
                                <option value="<?= esc((string) $playerId) ?>" <?= old('team_b_keeper_id', $teamBKeeperId ?? '') == $playerId ? 'selected' : '' ?>><?= esc($playersById[$playerId]['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['team_b_keeper_id'])): ?>
                            <div class="invalid-feedback d-block"><?= esc($errors['team_b_keeper_id']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
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

<?= $this->section('scripts') ?>
<script>
    const isMatchLocked = <?= ! empty($isMatchLocked) ? 'true' : 'false' ?>;

    const updateSquadLabel = (labelSelector, teamName, checkboxes) => {
        const label = document.querySelector(labelSelector);

        if (!label) {
            return;
        }

        const checkedCount = Array.from(checkboxes).filter((input) => input.checked).length;
        label.textContent = `${teamName} Squad (${checkedCount} checked)`;
    };

    const syncTeamSelections = () => {
        const teamACheckboxes = document.querySelectorAll('.team-a-player');
        const teamBCheckboxes = document.querySelectorAll('.team-b-player');
        const teamASelected = new Set(Array.from(teamACheckboxes).filter((input) => input.checked).map((input) => input.value));
        const teamBSelected = new Set(Array.from(teamBCheckboxes).filter((input) => input.checked).map((input) => input.value));

        teamACheckboxes.forEach((input) => {
            input.disabled = teamBSelected.has(input.value);
        });

        teamBCheckboxes.forEach((input) => {
            input.disabled = teamASelected.has(input.value);
        });

        updateSquadLabel('#team_a_squad_label', 'Team A', teamACheckboxes);
        updateSquadLabel('#team_b_squad_label', 'Team B', teamBCheckboxes);
        syncLeadershipOptions(teamACheckboxes, document.querySelector('#team_a_captain_id'), 'Select captain');
        syncLeadershipOptions(teamACheckboxes, document.querySelector('#team_a_keeper_id'), 'Select wicketkeeper');
        syncLeadershipOptions(teamBCheckboxes, document.querySelector('#team_b_captain_id'), 'Select captain');
        syncLeadershipOptions(teamBCheckboxes, document.querySelector('#team_b_keeper_id'), 'Select wicketkeeper');
    };

    const syncLeadershipOptions = (checkboxes, select, placeholder) => {
        if (!select) {
            return;
        }

        const selectedValue = select.value;
        const selectedPlayers = Array.from(checkboxes)
            .filter((input) => input.checked)
            .map((input) => ({
                id: input.value,
                name: document.querySelector(`label[for="${input.id}"]`)?.textContent.trim() ?? '',
            }));

        select.innerHTML = '';
        select.append(new Option(placeholder, ''));

        selectedPlayers.forEach((player) => {
            const option = new Option(player.name, player.id, false, player.id === selectedValue);
            select.append(option);
        });

        if (!selectedPlayers.some((player) => player.id === selectedValue)) {
            select.value = '';
        }
    };
    if (isMatchLocked) {
        syncTeamSelections();
    } else {
        document.querySelectorAll('.team-a-player, .team-b-player').forEach((input) => {
            input.addEventListener('change', syncTeamSelections);
        });

        syncTeamSelections();
    }
</script>
<?= $this->endSection() ?>