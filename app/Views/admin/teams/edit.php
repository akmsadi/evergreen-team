<?= $this->extend('admin/template/default') ?>

<?= $this->section('admin_nav') ?>teams<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php $errors = session()->getFlashdata('errors') ?? []; ?>
<div class="content-card">
    <div class="panel mb-8">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h1 class="h2 fw-bold mb-2"><?= esc($team['name']) ?></h1>
                <p class="text-secondary mb-0">Update team details and manage roster.</p>
            </div>
            <div>
                <a href="<?= site_url('/admin/teams') ?>" class="btn btn-light border">Back to Teams</a>
            </div>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>

    <!-- Team Details -->
    <div class="panel mb-8">
        <h2 class="h5 mb-3">Team Details</h2>
        <form action="<?= site_url('/admin/teams/' . $team['id'] . '/update') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="row g-3 mb-3">
                <div class="col-lg-4 mb-3 mb-lg-0">
                    <label for="name" class="form-label fw-semibold">Team Name</label>
                    <input type="text" class="form-control<?= isset($errors['name']) ? ' is-invalid' : '' ?>" id="name" name="name" value="<?= esc(old('name', $team['name'])) ?>">
                    <?php if (isset($errors['name'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['name']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-lg-4 mb-3 mb-lg-0">
                    <label for="slug" class="form-label fw-semibold">Slug</label>
                    <input type="text" class="form-control<?= isset($errors['slug']) ? ' is-invalid' : '' ?>" id="slug" name="slug" value="<?= esc(old('slug', $team['slug'])) ?>">
                    <?php if (isset($errors['slug'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['slug']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-lg-4">
                    <label for="slogan" class="form-label fw-semibold">Slogan</label>
                    <input type="text" class="form-control<?= isset($errors['slogan']) ? ' is-invalid' : '' ?>" id="slogan" name="slogan" value="<?= esc(old('slogan', $team['slogan'])) ?>">
                    <?php if (isset($errors['slogan'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['slogan']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mb-3">
                <label for="about" class="form-label fw-semibold">About</label>
                <textarea class="form-control<?= isset($errors['about']) ? ' is-invalid' : '' ?>" id="about" name="about" rows="4"><?= esc(old('about', $team['about'])) ?></textarea>
                <?php if (isset($errors['about'])): ?>
                    <div class="invalid-feedback"><?= esc($errors['about']) ?></div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="is_external" name="is_external" value="1" <?= old('is_external', $team['is_external']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="is_external">
                        <strong>External Team</strong>
                        <span class="text-secondary d-block small">Check this if this team is not part of your home teams (e.g., opposing teams)</span>
                    </label>
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-lg-3 mb-3 mb-lg-0">
                    <label for="established" class="form-label fw-semibold">Established</label>
                    <input type="text" class="form-control<?= isset($errors['established']) ? ' is-invalid' : '' ?>" id="established" name="established" value="<?= esc(old('established', $team['established'])) ?>">
                    <?php if (isset($errors['established'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['established']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-lg-3 mb-3 mb-lg-0">
                    <label for="matches" class="form-label fw-semibold">Matches</label>
                    <input type="text" class="form-control<?= isset($errors['matches']) ? ' is-invalid' : '' ?>" id="matches" name="matches" value="<?= esc(old('matches', $team['matches'])) ?>">
                    <?php if (isset($errors['matches'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['matches']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-lg-3 mb-3 mb-lg-0">
                    <label for="wins" class="form-label fw-semibold">Wins</label>
                    <input type="text" class="form-control<?= isset($errors['wins']) ? ' is-invalid' : '' ?>" id="wins" name="wins" value="<?= esc(old('wins', $team['wins'])) ?>">
                    <?php if (isset($errors['wins'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['wins']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-lg-3"><label for="titles" class="form-label fw-semibold">Titles</label>
                    <input type="text" class="form-control<?= isset($errors['titles']) ? ' is-invalid' : '' ?>" id="titles" name="titles" value="<?= esc(old('titles', $team['titles'])) ?>">
                    <?php if (isset($errors['titles'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['titles']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-lg-6 mb-3 mb-lg-0">
                    <label for="logo" class="form-label fw-semibold">Logo <span class="text-secondary fw-normal">(leave blank to keep current)</span></label>
                    <?php if ($team['logo']): ?>
                        <div class="mb-2">
                            <img src="<?= base_url('assets/images/teams/' . esc($team['logo'])) ?>" alt="Current logo" style="width:60px;height:60px;object-fit:cover;border-radius:4px;">
                        </div>
                    <?php endif; ?>
                    <input type="file" class="form-control<?= isset($errors['logo']) ? ' is-invalid' : '' ?>" id="logo" name="logo" accept="image/*">
                    <?php if (isset($errors['logo'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['logo']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-lg-6">
                    <label for="banner" class="form-label fw-semibold">Banner <span class="text-secondary fw-normal">(leave blank to keep current)</span></label>
                    <?php if ($team['banner']): ?>
                        <div class="mb-2">
                            <img src="<?= base_url('assets/images/teams/' . esc($team['banner'])) ?>" alt="Current banner" style="width:60px;height:60px;object-fit:cover;border-radius:4px;">
                        </div>
                    <?php endif; ?>
                    <input type="file" class="form-control<?= isset($errors['banner']) ? ' is-invalid' : '' ?>" id="banner" name="banner" accept="image/*">
                    <?php if (isset($errors['banner'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['banner']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-lg-6 mb-3 mb-lg-0">
                    <label for="captain_id" class="form-label fw-semibold">Captain</label>
                    <select class="form-select<?= isset($errors['captain_id']) ? ' is-invalid' : '' ?>" id="captain_id" name="captain_id">
                        <option value="">— Select Captain —</option>
                        <?php foreach ($members as $member): ?>
                            <?php if ($member['linked_player_name']): ?>
                                <option value="<?= esc((string) $member['player_id']) ?>" <?= old('captain_id', $team['captain_id']) == $member['player_id'] ? 'selected' : '' ?>>
                                    <?= esc($member['linked_player_name']) ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['captain_id'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['captain_id']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-lg-6">
                    <label for="vice_captain_id" class="form-label fw-semibold">Vice Captain</label>
                    <select class="form-select<?= isset($errors['vice_captain_id']) ? ' is-invalid' : '' ?>" id="vice_captain_id" name="vice_captain_id">
                        <option value="">— Select Vice Captain —</option>
                        <?php foreach ($members as $member): ?>
                            <?php if ($member['linked_player_name']): ?>
                                <option value="<?= esc((string) $member['player_id']) ?>" <?= old('vice_captain_id', $team['vice_captain_id']) == $member['player_id'] ? 'selected' : '' ?>>
                                    <?= esc($member['linked_player_name']) ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['vice_captain_id'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['vice_captain_id']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>

    <!-- Roster -->
    <div class="panel mb-8">
        <h2 class="h5 mb-3">Add Player</h2>

        <!-- Add member form -->
        <form action="<?= site_url('/admin/teams/' . $team['id'] . '/members') ?>" method="post" class="mb-4">
            <?= csrf_field() ?>
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label for="member_name" class="form-label fw-semibold">Name</label>
                    <input type="text" class="form-control<?= isset($errors['member_name']) ? ' is-invalid' : '' ?>" id="member_name" name="member_name" value="<?= esc(old('member_name')) ?>" placeholder="Player name">
                    <?php if (isset($errors['member_name'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['member_name']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-5">
                    <label for="player_id" class="form-label fw-semibold">Link to Player <span class="text-secondary fw-normal">(optional)</span></label>
                    <select class="form-select<?= isset($errors['player_id']) ? ' is-invalid' : '' ?>" id="player_id" name="player_id">
                        <option value="">— External / no link —</option>
                        <?php foreach ($approvedPlayers as $player): ?>
                            <option value="<?= esc((string) $player['id']) ?>" <?= old('player_id') == $player['id'] ? 'selected' : '' ?>>
                                <?= esc($player['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['player_id'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['player_id']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Add</button>
                </div>
            </div>
        </form>

        <!-- Member list -->
        <?php if ($members === []): ?>
            <p class="text-secondary mb-0">No players in this team yet.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Player status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($members as $member): ?>
                            <tr>
                                <td><?= esc($member['linked_player_name'] ?: $member['name']) ?></td>
                                <td><?= $member['player_id'] ? 'Group member' : 'External' ?></td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#edit-member-<?= $member['id'] ?>">Edit</button>
                                        <form action="<?= site_url('/admin/teams/' . $team['id'] . '/members/' . $member['id'] . '/delete') ?>" method="post" onsubmit="return confirm('Remove this player from the team?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                        </form>
                                    </div>
                                    <div class="collapse mt-2" id="edit-member-<?= $member['id'] ?>">
                                        <form action="<?= site_url('/admin/teams/' . $team['id'] . '/members/' . $member['id'] . '/update') ?>" method="post" class="p-3 border rounded bg-light">
                                            <?= csrf_field() ?>
                                            <div class="row g-2 align-items-end">
                                                <div class="col-md-5">
                                                    <label class="form-label small fw-semibold">Name</label>
                                                    <input type="text" class="form-control form-control-sm" name="member_name" value="<?= esc($member['name']) ?>">
                                                </div>
                                                <div class="col-md-5">
                                                    <label class="form-label small fw-semibold">Link to Player</label>
                                                    <select class="form-select form-select-sm" name="player_id">
                                                        <option value="">— External / no link —</option>
                                                        <?php foreach ($approvedPlayers as $player): ?>
                                                            <option value="<?= esc((string) $player['id']) ?>" <?= (int) $member['player_id'] === (int) $player['id'] ? 'selected' : '' ?>>
                                                                <?= esc($player['name']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="submit" class="btn btn-sm btn-primary w-100">Save</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>