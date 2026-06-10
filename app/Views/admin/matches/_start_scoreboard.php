<?php if ($activeInnings === null): ?>
    <div class="card-surface">
        <p class="text-secondary mb-0">Start the match first to generate the live scoreboard.</p>
    </div>
<?php else: ?>
    <?php
    $inningsId = (int) $activeInnings['id'];
    $entryDefaults = $activeInnings['entry_defaults'] ?? [];
    $battingPlayers = $scoreboard['playersBySide'][$activeInnings['batting_side']] ?? [];
    $bowlingPlayers = $scoreboard['playersBySide'][$activeInnings['bowling_side']] ?? [];
    $strikerValue = (string) ($ballValues['striker_player_id'] ?? ($entryDefaults['striker_player_id'] ?? ''));
    $nonStrikerValue = (string) ($ballValues['non_striker_player_id'] ?? ($entryDefaults['non_striker_player_id'] ?? ''));
    $bowlerValue = (string) ($ballValues['bowler_player_id'] ?? ($entryDefaults['bowler_player_id'] ?? ''));
    $dismissedValue = (string) ($ballValues['dismissed_player_id'] ?? ($entryDefaults['dismissed_player_id'] ?? ''));
    $fielderValue = (string) ($ballValues['fielder_player_id'] ?? '');
    $runsValue = (string) ($ballValues['runs_bat'] ?? '0');
    $extrasValue = (string) ($ballValues['extras'] ?? '0');
    $extraTypeValue = (string) ($ballValues['extra_type'] ?? '');
    $commentaryValue = (string) ($ballValues['commentary'] ?? '');
    $completeValue = ! empty($ballValues['complete_innings']);
    $isWicketValue = ! empty($ballValues['is_wicket']);
    $wicketTypeValue = (string) ($ballValues['wicket_type'] ?? '');
    $editBallIdValue = (string) ($ballValues['edit_ball_id'] ?? '');
    $isEditingLastBall = $editBallIdValue !== '';
    $requiresNewBowler = ! empty($activeInnings['requires_new_bowler']);
    $previousBowlerId = isset($activeInnings['current_pair']['bowler_id']) ? (int) $activeInnings['current_pair']['bowler_id'] : null;
    $nextInningsNumber = (int) ($scoreboard['nextInningsNumber'] ?? 0);
    $canStartNextInnings = (int) ($activeInnings['completed'] ?? 0) === 1
        && $nextInningsNumber === ((int) ($activeInnings['innings_number'] ?? 0) + 1)
        && $nextInningsNumber <= 2;
    $nextBattingSide = (string) ($activeInnings['bowling_side'] ?? '');
    $nextBowlingSide = (string) ($activeInnings['batting_side'] ?? '');
    $dismissedBatterIds = array_map('intval', $activeInnings['dismissed_batter_ids'] ?? []);

    if (! $isEditingLastBall && $requiresNewBowler && $previousBowlerId !== null && $bowlerValue === (string) $previousBowlerId) {
        $bowlerValue = '';
    }
    ?>
    <div class="card-surface">
        <div class="scoreboard-hero d-flex flex-column flex-lg-row justify-content-between align-items-lg-start gap-4 mb-6">
            <div>
                <div class="scoreboard-hero__eyebrow">Live Match Center</div>
                <h2 class="scoreboard-hero__title">Live Scoreboard</h2>
                <div class="scoreboard-hero__chips">
                    <div class="scoreboard-hero__chip">
                        <span class="scoreboard-hero__chip-label">Score</span>
                        <span class="scoreboard-hero__chip-value"><?= esc($activeInnings['score_text']) ?></span>
                    </div>
                    <div class="scoreboard-hero__chip">
                        <span class="scoreboard-hero__chip-label">Overs</span>
                        <span class="scoreboard-hero__chip-value"><?= esc($activeInnings['overs_text']) ?></span>
                    </div>
                    <div class="scoreboard-hero__chip">
                        <span class="scoreboard-hero__chip-label">Batting</span>
                        <span class="scoreboard-hero__chip-value"><?= esc($activeInnings['batting_label']) ?></span>
                    </div>
                    <div class="scoreboard-hero__chip">
                        <span class="scoreboard-hero__chip-label">Bowling</span>
                        <span class="scoreboard-hero__chip-value"><?= esc($activeInnings['bowling_label']) ?></span>
                    </div>
                    <div class="scoreboard-hero__chip">
                        <span class="scoreboard-hero__chip-label">Status</span>
                        <span class="scoreboard-hero__chip-value"><?php if ((int) $activeInnings['completed'] === 1): ?>Completed<?php else: ?>Open<?php endif; ?></span>
                    </div>
                </div>
            </div>
            <div class="scoreboard-hero__meta">
                <span class="scoreboard-hero__meta-label">Next Ball</span>
                <div class="scoreboard-hero__meta-value"><?= esc($activeInnings['next_ball_code']) ?></div>
                <?php if (! empty($activeInnings['current_pair'])): ?>
                    <span class="scoreboard-hero__meta-label">Striker</span>
                    <div class="scoreboard-hero__meta-value"><?= esc((string) ($activeInnings['current_pair']['striker'] ?? '')) ?></div>
                    <span class="scoreboard-hero__meta-label">Non-striker</span>
                    <div class="scoreboard-hero__meta-value"><?= esc((string) ($activeInnings['current_pair']['non_striker'] ?? '')) ?></div>
                    <span class="scoreboard-hero__meta-label">Bowler</span>
                    <div class="scoreboard-hero__meta-value"><?= esc((string) ($activeInnings['current_pair']['bowler'] ?? '')) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <?php if (isset($ballErrors['innings_id'])): ?>
            <div class="alert alert-danger"><?= esc($ballErrors['innings_id']) ?></div>
        <?php endif; ?>

        <div class="score-entry-panel mb-6">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-3">
                <div>
                    <h3 class="h6 fw-bold mb-1">Add Next Ball</h3>
                    <p class="text-secondary small mb-0">Use a quick outcome, then adjust the details if the delivery needs them.</p>
                </div>
                <div class="score-entry-meta">
                    <div class="score-meta-tile">
                        <span class="label">Current Score</span>
                        <span class="fw-semibold"><?= esc($activeInnings['score_text']) ?></span>
                    </div>
                    <div class="score-meta-tile">
                        <span class="label">Overs</span>
                        <span class="fw-semibold"><?= esc($activeInnings['overs_text']) ?></span>
                    </div>
                    <div class="score-meta-tile">
                        <span class="label">Target</span>
                        <span class="fw-semibold"><?= esc($activeInnings['target_runs'] === null ? '-' : (string) $activeInnings['target_runs']) ?></span>
                    </div>
                </div>
            </div>

            <?php if ((int) $activeInnings['completed'] === 1 && ! $isEditingLastBall): ?>
                <div class="alert alert-warning mb-3">
                    This innings is completed. Only the latest delivery can be edited.
                </div>
            <?php endif; ?>

            <form action="<?= site_url('/admin/matches/' . $match['id'] . '/balls') ?>" method="post" data-wizard-ball-form data-default-dismissed="<?= esc((string) ($entryDefaults['dismissed_player_id'] ?? '')) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="innings_id" value="<?= esc((string) $inningsId) ?>">
                <input type="hidden" name="edit_ball_id" value="<?= esc($editBallIdValue) ?>" data-edit-ball-id>

                <div class="alert alert-warning mb-3<?= $isEditingLastBall ? '' : ' d-none' ?>" data-edit-mode-note>
                    Editing the latest delivery. Save to replace it, or cancel to return to normal scoring.
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label for="wizard_striker_player_id_<?= esc((string) $inningsId) ?>" class="form-label fw-semibold">Striker</label>
                        <select class="form-select<?= isset($ballErrors['striker_player_id']) ? ' is-invalid' : '' ?>" id="wizard_striker_player_id_<?= esc((string) $inningsId) ?>" name="striker_player_id" data-field="striker_player_id">
                            <option value="">Select striker</option>
                            <?php foreach ($battingPlayers as $player): ?>
                                <?php if (in_array((int) $player['player_id'], $dismissedBatterIds, true) && $strikerValue !== (string) $player['player_id']) {
                                    continue;
                                } ?>
                                <option value="<?= esc((string) $player['player_id']) ?>" <?= $strikerValue === (string) $player['player_id'] ? 'selected' : '' ?>><?= esc($player['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($ballErrors['striker_player_id'])): ?>
                            <div class="invalid-feedback"><?= esc($ballErrors['striker_player_id']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
                            <label for="wizard_non_striker_player_id_<?= esc((string) $inningsId) ?>" class="form-label fw-semibold mb-0">Non-striker</label>
                            <button type="button" class="btn btn-sm btn-primary" data-swap-batters>Swap</button>
                        </div>
                        <select class="form-select<?= isset($ballErrors['non_striker_player_id']) ? ' is-invalid' : '' ?>" id="wizard_non_striker_player_id_<?= esc((string) $inningsId) ?>" name="non_striker_player_id">
                            <option value="">Select non-striker</option>
                            <?php foreach ($battingPlayers as $player): ?>
                                <?php if (in_array((int) $player['player_id'], $dismissedBatterIds, true) && $nonStrikerValue !== (string) $player['player_id']) {
                                    continue;
                                } ?>
                                <option value="<?= esc((string) $player['player_id']) ?>" <?= $nonStrikerValue === (string) $player['player_id'] ? 'selected' : '' ?>><?= esc($player['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($ballErrors['non_striker_player_id'])): ?>
                            <div class="invalid-feedback"><?= esc($ballErrors['non_striker_player_id']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-4">
                        <label for="wizard_bowler_player_id_<?= esc((string) $inningsId) ?>" class="form-label fw-semibold">Bowler</label>
                        <select class="form-select<?= isset($ballErrors['bowler_player_id']) ? ' is-invalid' : '' ?>" id="wizard_bowler_player_id_<?= esc((string) $inningsId) ?>" name="bowler_player_id">
                            <option value="">Select bowler</option>
                            <?php foreach ($bowlingPlayers as $player): ?>
                                <?php if (! $isEditingLastBall && $requiresNewBowler && $previousBowlerId !== null && (int) $player['player_id'] === $previousBowlerId) {
                                    continue;
                                } ?>
                                <option value="<?= esc((string) $player['player_id']) ?>" <?= $bowlerValue === (string) $player['player_id'] ? 'selected' : '' ?>><?= esc($player['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($ballErrors['bowler_player_id'])): ?>
                            <div class="invalid-feedback"><?= esc($ballErrors['bowler_player_id']) ?></div>
                        <?php endif; ?>
                        <?php if (! $isEditingLastBall && $requiresNewBowler && ! isset($ballErrors['bowler_player_id'])): ?>
                            <div class="form-text text-warning">Over complete. Select a new bowler before the next ball.</div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-label fw-semibold">Quick Outcome</div>
                    <div class="score-outcome-grid">
                        <?php foreach ([0, 1, 2, 3, 4, 6] as $run): ?>
                            <button type="button" class="btn btn-outline-dark" data-outcome-button data-runs-bat="<?= esc((string) $run) ?>" data-extras="0" data-extra-type="" data-wicket="0"><?= esc((string) $run) ?></button>
                        <?php endforeach; ?>
                        <button type="button" class="btn btn-outline-primary" data-outcome-button data-runs-bat="0" data-extras="1" data-extra-type="wide" data-wicket="0">Wd</button>
                        <button type="button" class="btn btn-outline-primary" data-outcome-button data-runs-bat="0" data-extras="1" data-extra-type="no_ball" data-wicket="0">Nb</button>
                        <button type="button" class="btn btn-outline-secondary" data-outcome-button data-runs-bat="0" data-extras="1" data-extra-type="bye" data-wicket="0">B</button>
                        <button type="button" class="btn btn-outline-secondary" data-outcome-button data-runs-bat="0" data-extras="1" data-extra-type="leg_bye" data-wicket="0">Lb</button>
                        <button type="button" class="btn btn-outline-danger" data-outcome-button data-runs-bat="0" data-extras="0" data-extra-type="" data-wicket="1">W</button>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-2">
                        <label for="wizard_runs_bat_<?= esc((string) $inningsId) ?>" class="form-label fw-semibold">Bat Runs</label>
                        <input type="number" min="0" class="form-control" id="wizard_runs_bat_<?= esc((string) $inningsId) ?>" name="runs_bat" value="<?= esc($runsValue) ?>" data-field="runs_bat">
                    </div>
                    <div class="col-md-2">
                        <label for="wizard_extras_<?= esc((string) $inningsId) ?>" class="form-label fw-semibold">Extras</label>
                        <input type="number" min="0" class="form-control" id="wizard_extras_<?= esc((string) $inningsId) ?>" name="extras" value="<?= esc($extrasValue) ?>" data-field="extras">
                    </div>
                    <div class="col-md-4">
                        <label for="wizard_extra_type_<?= esc((string) $inningsId) ?>" class="form-label fw-semibold">Extra Type</label>
                        <select class="form-select<?= isset($ballErrors['extra_type']) ? ' is-invalid' : '' ?>" id="wizard_extra_type_<?= esc((string) $inningsId) ?>" name="extra_type" data-field="extra_type">
                            <option value="">No extra</option>
                            <?php foreach ($scoreboard['extraTypes'] as $extraType): ?>
                                <option value="<?= esc($extraType) ?>" <?= $extraTypeValue === $extraType ? 'selected' : '' ?>><?= esc(ucwords(str_replace('_', ' ', $extraType))) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($ballErrors['extra_type'])): ?>
                            <div class="invalid-feedback"><?= esc($ballErrors['extra_type']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="wizard_is_wicket_<?= esc((string) $inningsId) ?>" name="is_wicket" value="1" <?= $isWicketValue ? 'checked' : '' ?> data-field="is_wicket">
                            <label class="form-check-label" for="wizard_is_wicket_<?= esc((string) $inningsId) ?>">Wicket on this ball</label>
                        </div>
                    </div>
                </div>

                <details class="score-entry-advanced mb-3" <?= $isWicketValue || $extraTypeValue !== '' ? 'open' : '' ?>>
                    <summary>Advanced delivery details</summary>
                    <div class="row g-3 mt-2">
                        <div class="col-md-4" data-wicket-fields>
                            <label for="wizard_wicket_type_<?= esc((string) $inningsId) ?>" class="form-label fw-semibold">Wicket Type</label>
                            <select class="form-select<?= isset($ballErrors['wicket_type']) ? ' is-invalid' : '' ?>" id="wizard_wicket_type_<?= esc((string) $inningsId) ?>" name="wicket_type" data-field="wicket_type">
                                <option value="">Select wicket type</option>
                                <?php foreach ($scoreboard['wicketTypes'] as $wicketType): ?>
                                    <option value="<?= esc($wicketType) ?>" <?= $wicketTypeValue === $wicketType ? 'selected' : '' ?>><?= esc(ucwords(str_replace('_', ' ', $wicketType))) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($ballErrors['wicket_type'])): ?>
                                <div class="invalid-feedback d-block"><?= esc($ballErrors['wicket_type']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4" data-wicket-fields>
                            <label for="wizard_dismissed_player_id_<?= esc((string) $inningsId) ?>" class="form-label fw-semibold">Dismissed Batter</label>
                            <select class="form-select<?= isset($ballErrors['dismissed_player_id']) ? ' is-invalid' : '' ?>" id="wizard_dismissed_player_id_<?= esc((string) $inningsId) ?>" name="dismissed_player_id" data-field="dismissed_player_id">
                                <option value="">Select dismissed batter</option>
                                <?php foreach ($battingPlayers as $player): ?>
                                    <option value="<?= esc((string) $player['player_id']) ?>" <?= $dismissedValue === (string) $player['player_id'] ? 'selected' : '' ?>><?= esc($player['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($ballErrors['dismissed_player_id'])): ?>
                                <div class="invalid-feedback d-block"><?= esc($ballErrors['dismissed_player_id']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4" data-wicket-fields>
                            <label for="wizard_fielder_player_id_<?= esc((string) $inningsId) ?>" class="form-label fw-semibold">Fielder</label>
                            <select class="form-select<?= isset($ballErrors['fielder_player_id']) ? ' is-invalid' : '' ?>" id="wizard_fielder_player_id_<?= esc((string) $inningsId) ?>" name="fielder_player_id">
                                <option value="">No fielder</option>
                                <?php foreach ($bowlingPlayers as $player): ?>
                                    <option value="<?= esc((string) $player['player_id']) ?>" <?= $fielderValue === (string) $player['player_id'] ? 'selected' : '' ?>><?= esc($player['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($ballErrors['fielder_player_id'])): ?>
                                <div class="invalid-feedback d-block"><?= esc($ballErrors['fielder_player_id']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-8">
                            <label for="wizard_commentary_<?= esc((string) $inningsId) ?>" class="form-label fw-semibold">Commentary</label>
                            <input type="text" class="form-control" id="wizard_commentary_<?= esc((string) $inningsId) ?>" name="commentary" value="<?= esc($commentaryValue) ?>" placeholder="Optional note for this delivery">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="wizard_complete_innings_<?= esc((string) $inningsId) ?>" name="complete_innings" value="1" <?= $completeValue ? 'checked' : '' ?>>
                                <label class="form-check-label" for="wizard_complete_innings_<?= esc((string) $inningsId) ?>">Mark innings complete</label>
                            </div>
                        </div>
                    </div>
                </details>

                <div class="d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn btn-primary"><?= $isEditingLastBall ? 'Update Last Delivery' : 'Save Delivery' ?></button>
                    <button type="button" class="btn btn-light border<?= $isEditingLastBall ? '' : ' d-none' ?>" data-cancel-edit-ball>Cancel Edit</button>
                    <span class="text-secondary small align-self-center">The scoreboard refreshes inside this wizard after each saved ball.</span>
                </div>
            </form>
        </div>
        <?php if ($canStartNextInnings): ?>
            <div class="score-entry-panel mb-6">
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                    <div>
                        <h3 class="h6 fw-bold mb-1">Start <?= esc((string) $nextInningsNumber) ?>nd Innings</h3>
                        <p class="text-secondary small mb-0">Innings <?= esc((string) ($activeInnings['innings_number'] ?? 1)) ?> is complete. Start the next innings scoreboard to continue scoring.</p>
                    </div>
                    <form action="<?= site_url('/admin/matches/' . $match['id'] . '/innings') ?>" method="post" class="d-flex gap-2 align-items-center">
                        <?= csrf_field() ?>
                        <input type="hidden" name="innings_number" value="<?= esc((string) $nextInningsNumber) ?>">
                        <input type="hidden" name="batting_side" value="<?= esc($nextBattingSide) ?>">
                        <input type="hidden" name="bowling_side" value="<?= esc($nextBowlingSide) ?>">
                        <input type="hidden" name="return_to" value="<?= esc('/admin/matches/' . $match['id'] . '/start') ?>">
                        <button type="submit" class="btn btn-primary">Start <?= esc((string) $nextInningsNumber) ?>nd Innings Scoreboard</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-5">
                <h3 class="h6 fw-bold mb-3">Batting Card</h3>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Batter</th>
                                <th>R</th>
                                <th>B</th>
                                <th>4s</th>
                                <th>6s</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($activeInnings['batting_card'] as $batter): ?>
                                <tr>
                                    <td><?= esc($batter['name']) ?></td>
                                    <td><?= esc((string) $batter['runs']) ?></td>
                                    <td><?= esc((string) $batter['balls']) ?></td>
                                    <td><?= esc((string) $batter['fours']) ?></td>
                                    <td><?= esc((string) $batter['sixes']) ?></td>
                                    <td class="small"><?= esc($batter['dismissal']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-3">
                <h3 class="h6 fw-bold mb-3">Bowling Card</h3>
                <?php if ($activeInnings['bowling_card'] === []): ?>
                    <p class="text-secondary small mb-0">No bowling figures yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Bowler</th>
                                    <th>O</th>
                                    <th>R</th>
                                    <th>W</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($activeInnings['bowling_card'] as $bowler): ?>
                                    <tr>
                                        <td><?= esc($bowler['name']) ?></td>
                                        <td><?= esc($bowler['overs']) ?></td>
                                        <td><?= esc((string) $bowler['runs']) ?></td>
                                        <td><?= esc((string) $bowler['wickets']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-lg-4">
                <h3 class="h6 fw-bold mb-3">Recent Balls</h3>
                <?php if ($activeInnings['recent_balls'] === []): ?>
                    <p class="text-secondary small mb-0">No deliveries logged yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Ball</th>
                                    <th>Detail</th>
                                    <th>Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($activeInnings['recent_balls'] as $index => $ball): ?>
                                    <?php $displayBallCode = max(0, ((int) ($ball['over_number'] ?? 1)) - 1) . '.' . (int) ($ball['ball_in_over'] ?? 1); ?>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold"><?= esc($displayBallCode) ?></div>
                                            <div class="small text-secondary"><?= esc((string) ($ball['bowler_name'] ?? '')) ?></div>
                                        </td>
                                        <td>
                                            <div><?= esc((string) ($ball['striker_name'] ?? '')) ?> scored <?= esc((string) ($ball['runs_bat'] ?? 0)) ?></div>
                                            <?php if (! empty($ball['extra_type'])): ?>
                                                <div class="small text-secondary">Extras: <?= esc((string) ($ball['extras'] ?? 0)) ?> <?= esc(str_replace('_', ' ', (string) $ball['extra_type'])) ?></div>
                                            <?php endif; ?>
                                            <?php if ((int) ($ball['is_wicket'] ?? 0) === 1): ?>
                                                <div class="small text-danger">Wicket: <?= esc(str_replace('_', ' ', (string) ($ball['wicket_type'] ?? 'out'))) ?><?= ! empty($ball['dismissed_name']) ? ' - ' . esc((string) $ball['dismissed_name']) : '' ?></div>
                                            <?php endif; ?>
                                            <?php if (! empty($ball['commentary'])): ?>
                                                <div class="small text-secondary"><?= esc((string) $ball['commentary']) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= esc((string) ($ball['score_after_ball'] ?? '')) ?></td>
                                        <td class="text-end">
                                            <?php if ($index === 0): ?>
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-primary"
                                                    data-edit-last-ball
                                                    data-ball-id="<?= esc((string) ($ball['id'] ?? '')) ?>"
                                                    data-striker-player-id="<?= esc((string) ($ball['striker_player_id'] ?? '')) ?>"
                                                    data-non-striker-player-id="<?= esc((string) ($ball['non_striker_player_id'] ?? '')) ?>"
                                                    data-bowler-player-id="<?= esc((string) ($ball['bowler_player_id'] ?? '')) ?>"
                                                    data-bowler-name="<?= esc((string) ($ball['bowler_name'] ?? '')) ?>"
                                                    data-runs-bat="<?= esc((string) ($ball['runs_bat'] ?? '0')) ?>"
                                                    data-extras="<?= esc((string) ($ball['extras'] ?? '0')) ?>"
                                                    data-extra-type="<?= esc((string) ($ball['extra_type'] ?? '')) ?>"
                                                    data-is-wicket="<?= esc((string) ($ball['is_wicket'] ?? '0')) ?>"
                                                    data-wicket-type="<?= esc((string) ($ball['wicket_type'] ?? '')) ?>"
                                                    data-dismissed-player-id="<?= esc((string) ($ball['dismissed_player_id'] ?? '')) ?>"
                                                    data-fielder-player-id="<?= esc((string) ($ball['fielder_player_id'] ?? '')) ?>"
                                                    data-commentary="<?= esc((string) ($ball['commentary'] ?? '')) ?>">Edit Last</button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>