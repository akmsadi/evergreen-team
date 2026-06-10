<?= $this->extend('admin/default') ?>

<?= $this->section('title') ?>Player Overview<?= $this->endSection() ?>

<?= $this->section('admin_nav') ?>players<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style {csp-style-nonce}>
    .player-summary-card {
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 1.25rem;
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
    }

    .player-summary-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at top right, rgba(255, 255, 255, 0.45), transparent 42%);
        pointer-events: none;
    }

    .player-summary-card__label {
        color: rgba(15, 23, 42, 0.72);
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .player-summary-card__value {
        color: #0f172a;
        font-size: clamp(2rem, 4vw, 2.75rem);
        font-weight: 700;
        line-height: 1;
        margin-top: 0.5rem;
    }

    .player-summary-card__note {
        color: rgba(15, 23, 42, 0.72);
        font-size: 0.9rem;
        margin-top: 0.75rem;
    }

    .player-summary-card--players {
        background: linear-gradient(145deg, #eefbf3 0%, #dff5e8 58%, #d3eddd 100%);
    }

    .player-summary-card--approved {
        background: linear-gradient(145deg, #ecfdf3 0%, #d9fbe8 58%, #c7f4db 100%);
    }

    .player-summary-card--pending {
        background: linear-gradient(145deg, #fff8e7 0%, #ffefc7 58%, #fde5a0 100%);
    }

    .player-summary-card--rejected {
        background: linear-gradient(145deg, #fff1f2 0%, #ffe1e5 58%, #ffc9d2 100%);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row g-3 align-items-start">
    <div class="col-12">
        <div class="content-card">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
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
                    <div class="summary-card player-summary-card player-summary-card--players p-4 h-100">
                        <div class="player-summary-card__label">Players</div>
                        <div class="player-summary-card__value"><?= esc((string) $playerCount) ?></div>
                        <div class="player-summary-card__note">All registered players in the club pool.</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card player-summary-card player-summary-card--approved p-4 h-100">
                        <div class="player-summary-card__label">Approved</div>
                        <div class="player-summary-card__value"><?= esc((string) $approvedCount) ?></div>
                        <div class="player-summary-card__note">Eligible for squads, deposits, and match selection.</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card player-summary-card player-summary-card--pending p-4 h-100">
                        <div class="player-summary-card__label">Pending</div>
                        <div class="player-summary-card__value"><?= esc((string) $pendingCount) ?></div>
                        <div class="player-summary-card__note">Registrations still awaiting review and approval.</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card player-summary-card player-summary-card--rejected p-4 h-100">
                        <div class="player-summary-card__label">Rejected</div>
                        <div class="player-summary-card__value"><?= esc((string) $rejectedCount) ?></div>
                        <div class="player-summary-card__note">Registrations declined and excluded from admin actions.</div>
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle admin-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Matches</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($playerOverview as $entry): ?>
                                <tr>
                                    <td><?= esc($entry['player']['name']) ?></td>
                                    <td><?= esc($entry['player']['email']) ?></td>
                                    <td><?= esc($entry['player']['phone']) ?></td>
                                    <td class="text-capitalize"><?= esc($entry['player']['status']) ?></td>
                                    <td><?= esc((string) $entry['match_count']) ?></td>
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-2">
                                            <a href="<?= site_url('/admin/players/' . $entry['player']['id'] . '/edit') ?>" class="btn btn-sm btn-primary">Edit</a>
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