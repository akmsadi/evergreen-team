<?= $this->extend('admin/template/default') ?>

<?= $this->section('content') ?>
<div class="content-card">
    <div class="panel mb-8">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
            <div>
                <h1 class="h2 mb-2">Player Overview</h1>
                <p class="text-secondary mb-0">Review player status, contact details, and match participation.</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="<?= site_url('/admin/accounts') ?>" class="btn btn-light border">Accounts</a>
                <a href="<?= site_url('/admin/players/create') ?>" class="btn btn-primary">Add Player</a>
            </div>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
        <?php endif; ?>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="summary-card p-4 h-100">
                    <div class="summary-label">Players</div>
                    <div class="summary-value"><?= esc((string) $playerCount) ?></div>
                    <div class="summary-note">All players.</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card p-4 h-100">
                    <div class="summary-label">Approved</div>
                    <div class="summary-value"><?= esc((string) $approvedCount) ?></div>
                    <div class="summary-note">Active players.</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card p-4 h-100">
                    <div class="summary-label">Guest</div>
                    <div class="summary-value"><?= esc((string) count($guestPlayers)) ?></div>
                    <div class="summary-note">Guests of Active players.</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card p-4 h-100">
                    <div class="summary-label">External</div>
                    <div class="summary-value"><?= esc((string) count($externalPlayers)) ?></div>
                    <div class="summary-note">Friendly match.</div>
                </div>
            </div>
        </div>

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Approved</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">Guest</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact-tab-pane" type="button" role="tab" aria-controls="contact-tab-pane" aria-selected="false">External</button>
            </li>
        </ul>
        <?php $playerNameById = array_column($approvedPlayers, 'name', 'id'); ?>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active p-3" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle admin-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($approvedPlayers as $player): ?>
                                <tr>
                                    <td><?= esc($player['name']) ?></td>
                                    <td><?= esc($player['email']) ?></td>
                                    <td><?= esc($player['phone']) ?></td>
                                    <td class="text-capitalize"><?= esc($player['status']) ?></td>
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-2">
                                            <a href="<?= site_url('/admin/players/' . $player['id'] . '/edit') ?>" class="btn btn-sm btn-primary">Edit</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade p-3" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle admin-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($guestPlayers as $player): ?>
                                <tr>
                                    <td>
                                        <?= esc($player['name']) ?>
                                        <?php if (($player['status'] ?? '') === 'guest' && !empty($player['guest_of_player_id']) && isset($playerNameById[$player['guest_of_player_id']])): ?>
                                            <span class="text-secondary small">(Guest - <?= esc($playerNameById[$player['guest_of_player_id']]) ?>)</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($player['email']) ?></td>
                                    <td><?= esc($player['phone']) ?></td>
                                    <td class="text-capitalize"><?= esc($player['status']) ?></td>
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-2">
                                            <a href="<?= site_url('/admin/players/' . $player['id'] . '/edit') ?>" class="btn btn-sm btn-primary">Edit</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade p-3" id="contact-tab-pane" role="tabpanel" aria-labelledby="contact-tab" tabindex="0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle admin-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($externalPlayers as $player): ?>
                                <tr>
                                    <td><?= esc($player['name']) ?></td>
                                    <td><?= esc($player['email']) ?></td>
                                    <td><?= esc($player['phone']) ?></td>
                                    <td class="text-capitalize"><?= esc($player['status']) ?></td>
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-2">
                                            <a href="<?= site_url('/admin/players/' . $player['id'] . '/edit') ?>" class="btn btn-sm btn-primary">Edit</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>