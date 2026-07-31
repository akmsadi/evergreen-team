<?= $this->extend('admin/template/default') ?>

<?= $this->section('admin_nav') ?>players<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php $errors = session()->getFlashdata('errors') ?? []; ?>
<div class="content-card">
    <div class="panel mb-8">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
            <div>
                <h2 class="h4 mb-2"><?= esc($player['name']) ?></h2>
                <p class="text-secondary mb-0">Player Profile</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="<?= site_url('/admin/players') ?>" class="btn btn-light border">Back to Players</a>
                <a href="<?= site_url('/admin/players/create') ?>" class="btn btn-primary">Add Player</a>
            </div>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>

    <div class="row g-4 mb-8">
        <div class="col-md-3">
            <div class="summary-card p-4 h-100">
                <div class="summary-label mb-2">Matches</div>
                <div class="summary-value"><?= esc((string) $wallet['match_count']) ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card p-4 h-100">
                <div class="summary-label mb-2">Deposited</div>
                <div class="summary-value"><?= esc(number_format((float) $wallet['deposited'], 2)) ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card p-4 h-100">
                <div class="summary-label mb-2">Match Contribution</div>
                <div class="summary-value"><?= esc(number_format((float) $matchContributionTotal, 2)) ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card p-4 h-100">
                <div class="summary-label mb-2">Balance</div>
                <div class="summary-value"><?= esc(number_format((float) $wallet['balance'], 2)) ?></div>
            </div>
        </div>
    </div>

    <div class="panel mb-8">
        <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
            <div>
                <h2 class="h4 fw-bold mb-1">Edit Player</h2>
                <p class="text-secondary mb-0">Keep the player profile current for admin and finance tracking.</p>
            </div>
        </div>
        <form action="<?= site_url('/admin/players/' . $player['id'] . '/update') ?>" method="post" class="row g-3">
            <?= csrf_field() ?>
            <div class="col-md-6">
                <label for="name" class="form-label fw-semibold">Name</label>
                <input type="text" class="form-control<?= isset($errors['name']) ? ' is-invalid' : '' ?>" id="name" name="name" value="<?= esc(old('name', $player['name'])) ?>">
                <?php if (isset($errors['name'])): ?>
                    <div class="invalid-feedback"><?= esc($errors['name']) ?></div>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <label for="email" class="form-label fw-semibold">Email</label>
                <input type="email" class="form-control<?= isset($errors['email']) ? ' is-invalid' : '' ?>" id="email" name="email" value="<?= esc(old('email', $player['email'])) ?>">
                <?php if (isset($errors['email'])): ?>
                    <div class="invalid-feedback"><?= esc($errors['email']) ?></div>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <label for="phone" class="form-label fw-semibold">Phone</label>
                <input type="text" class="form-control<?= isset($errors['phone']) ? ' is-invalid' : '' ?>" id="phone" name="phone" value="<?= esc(old('phone', $player['phone'])) ?>">
                <?php if (isset($errors['phone'])): ?>
                    <div class="invalid-feedback"><?= esc($errors['phone']) ?></div>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <label for="nickname" class="form-label fw-semibold">Nickname</label>
                <input type="text" class="form-control<?= isset($errors['nickname']) ? ' is-invalid' : '' ?>" id="nickname" name="nickname" value="<?= esc(old('nickname', $player['nickname'])) ?>">
                <?php if (isset($errors['nickname'])): ?>
                    <div class="invalid-feedback"><?= esc($errors['nickname']) ?></div>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <label for="status" class="form-label fw-semibold">Status</label>
                <select class="form-select<?= isset($errors['status']) ? ' is-invalid' : '' ?>" id="status" name="status">
                    <?php foreach (['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'guest' => 'Guest', 'external' => 'External'] as $value => $label): ?>
                        <option value="<?= esc($value) ?>" <?= old('status', $player['status']) === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['status'])): ?>
                    <div class="invalid-feedback"><?= esc($errors['status']) ?></div>
                <?php endif; ?>
            </div>
            <?php $selectedStatus = old('status', $player['status']); ?>
            <div class="col-12<?= $selectedStatus === 'guest' ? '' : ' d-none' ?>" id="guest-of-field">
                <label for="guest_of_player_id" class="form-label fw-semibold">Guest OF</label>
                <select class="form-select<?= isset($errors['guest_of_player_id']) ? ' is-invalid' : '' ?>" id="guest_of_player_id" name="guest_of_player_id">
                    <option value="">Select approved player</option>
                    <?php foreach ($approvedPlayers as $approvedPlayer): ?>
                        <option value="<?= esc((string) $approvedPlayer['id']) ?>" <?= (string) old('guest_of_player_id', $player['guest_of_player_id'] ?? '') === (string) $approvedPlayer['id'] ? 'selected' : '' ?>><?= esc($approvedPlayer['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="form-text">Required when the player status is Guest.</div>
                <?php if (isset($errors['guest_of_player_id'])): ?>
                    <div class="invalid-feedback"><?= esc($errors['guest_of_player_id']) ?></div>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <label for="organization" class="form-label fw-semibold">Organization</label>
                <input type="text" class="form-control<?= isset($errors['organization']) ? ' is-invalid' : '' ?>" id="organization" name="organization" value="<?= esc(old('organization', $player['organization'])) ?>">
                <?php if (isset($errors['organization'])): ?>
                    <div class="invalid-feedback"><?= esc($errors['organization']) ?></div>
                <?php endif; ?>
            </div>
            <div class="col-6">
                <label for="address" class="form-label fw-semibold">Address</label>
                <textarea class="form-control<?= isset($errors['address']) ? ' is-invalid' : '' ?>" id="address" name="address" rows="3"><?= esc(old('address', $player['address'])) ?></textarea>
                <?php if (isset($errors['address'])): ?>
                    <div class="invalid-feedback"><?= esc($errors['address']) ?></div>
                <?php endif; ?>
            </div>
            <div class="col-12 d-flex justify-content-end gap-2">
                <a href="<?= site_url('/admin/players') ?>" class="btn btn-light border">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
        <?php if ($player['status'] !== 'approved'): ?>
            <div class="mt-3 pt-3 border-top d-flex justify-content-start">
                <form action="<?= site_url('/admin/players/' . $player['id'] . '/delete') ?>" method="post" onsubmit="return confirm('Delete this player? This will remove linked player finance records and squad entries.');">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-outline-danger">Delete Player</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <div class="content-card p-4">
        <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
            <div>
                <h2 class="h4 mb-1">Match Details</h2>
                <p class="text-secondary mb-0">Match appearances and this player's contribution share in each fixture.</p>
            </div>
        </div>
        <?php if ($matchBreakdown === []): ?>
            <p class="text-secondary mb-0">This player has not been linked to any matches yet.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Match</th>
                            <th>Scheduled</th>
                            <th>Role</th>
                            <th>Participation</th>
                            <th>Status</th>
                            <th>Scoreboard Stats</th>
                            <th>Contribution</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($matchBreakdown as $index => $match): ?>
                            <?php $scoreboard = $match['scoreboard']; ?>
                            <tr>
                                <td><?= esc((string) ($index + 1)) ?></td>
                                <td>
                                    <div class="fw-semibold"><?= esc($match['team_name']) ?> vs <?= esc($match['opponent_name']) ?></div>
                                    <div class="text-secondary small"><?= esc($match['venue'] ?: 'Venue not set') ?></div>
                                </td>
                                <td><?= esc(getFormattedDate($match['scheduled_at'] ?? null, '-')) ?></td>
                                <td>
                                    <div><?= esc($match['role'] ?: 'Player') ?></div>
                                    <?php if (!empty($match['batting_position'])): ?>
                                        <div class="text-secondary small">Batting #<?= esc((string) $match['batting_position']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div><?= esc($match['side_label']) ?></div>
                                    <div class="text-secondary small">
                                        <?= !empty($match['playing_xi']) ? 'Playing XI' : 'Squad only' ?>
                                        <?php if (!empty($match['is_captain'])): ?>
                                            · Captain
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="<?= !empty($match['is_archived']) ? 'badge-soft-warning' : 'badge-soft-success' ?>">
                                        <?= esc(!empty($match['is_archived']) ? 'archived' : $match['match_status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="small">
                                        <?php if ($scoreboard['batted']): ?>
                                            <div><span class="fw-semibold">Bat:</span> <?= esc((string) $scoreboard['bat_runs']) ?><?= $scoreboard['dismissed'] ? '' : '*' ?> (<?= esc((string) $scoreboard['bat_balls']) ?>)</div>
                                            <div class="text-secondary">4s <?= esc((string) $scoreboard['fours']) ?> · 6s <?= esc((string) $scoreboard['sixes']) ?><?php if ($scoreboard['strike_rate'] !== null): ?> · SR <?= esc(number_format((float) $scoreboard['strike_rate'], 2)) ?><?php endif; ?></div>
                                        <?php else: ?>
                                            <div class="text-secondary">Did not bat</div>
                                        <?php endif; ?>
                                        <?php if ($scoreboard['bowled']): ?>
                                            <div class="mt-2"><span class="fw-semibold">Bowl:</span> <?= esc((string) $scoreboard['bowl_wickets']) ?>/<?= esc((string) $scoreboard['bowl_runs']) ?> in <?= esc($scoreboard['bowling_overs']) ?> ov<?php if ($scoreboard['economy'] !== null): ?> · Eco <?= esc(number_format((float) $scoreboard['economy'], 2)) ?><?php endif; ?></div>
                                        <?php else: ?>
                                            <div class="text-secondary mt-2">Did not bowl</div>
                                        <?php endif; ?>
                                        <?php if ($scoreboard['catches'] > 0 || $scoreboard['run_outs'] > 0 || $scoreboard['stumpings'] > 0): ?>
                                            <div class="text-secondary mt-2">Field: Catches <?= esc((string) $scoreboard['catches']) ?> · Run outs <?= esc((string) $scoreboard['run_outs']) ?> · Stumpings <?= esc((string) $scoreboard['stumpings']) ?></div>
                                        <?php endif; ?>
                                        <?php if ($scoreboard['dismissed'] && $scoreboard['dismissal_type'] !== null): ?>
                                            <div class="text-secondary">Dismissal: <?= esc(ucwords(str_replace('_', ' ', (string) $scoreboard['dismissal_type']))) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td><?= esc(number_format((float) $match['contribution'], 2)) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const field = document.getElementById('guest-of-field');
        const statusSelect = document.getElementById('status');

        if (!field || !statusSelect) {
            return;
        }

        const syncGuestFieldVisibility = function () {
            field.classList.toggle('d-none', statusSelect.value !== 'guest');
        };

        statusSelect.addEventListener('change', syncGuestFieldVisibility);
        syncGuestFieldVisibility();
    });
</script>
<?= $this->endSection() ?>