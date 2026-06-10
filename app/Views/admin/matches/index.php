<?= $this->extend('admin/default') ?>

<?= $this->section('title') ?>Matches<?= $this->endSection() ?>

<?= $this->section('admin_nav') ?>matches<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$activeMatches = array_values(array_filter(
    $matches,
    static fn(array $match): bool => in_array((string) ($match['match_status'] ?? ''), ['scheduled', 'live'], true)
));
$otherMatches = array_values(array_filter(
    $matches,
    static fn(array $match): bool => ! in_array((string) ($match['match_status'] ?? ''), ['scheduled', 'live'], true)
));
?>
<div class="content-card">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="h2 mb-2">Matches</h1>
            <p class="text-secondary mb-0">Open a match to manage participant expenses, deposits, and balances.</p>
        </div>
        <a href="<?= site_url('/admin/matches/create') ?>" class="btn btn-primary">Create Match</a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>

    <?php if ($matches === []): ?>
        <div class="panel">
            <p class="text-secondary mb-0">No matches found yet. Create the first match to start tracking participants, expenses, and deposits.</p>
        </div>
    <?php else: ?>
        <?php if ($activeMatches !== []): ?>
            <div class="panel mb-4">
                <div class="row">
                    <?php foreach ($activeMatches as $match): ?>
                        <div class="col-md-6">
                            <div class="alert alert-<?= ($match['match_status'] ?? '') === 'live' ? 'success' : 'warning' ?>">
                                <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                    <div>
                                        <h2 class="h4 mb-1"><?= esc($match['team_name']) ?> vs <?= esc($match['opponent_name']) ?></h2>
                                        <div class="text-secondary small">
                                            <?= esc(ucwords(str_replace('_', ' ', $match['match_type']))) ?>
                                            • <?= esc((string) $match['format_overs']) ?> overs
                                        </div>
                                    </div>
                                    <span class="status-pill"><?= esc($match['match_status']) ?></span>
                                </div>
                                <div class="small text-secondary mb-3">
                                    <div>Venue: <?= esc($match['venue'] ?: 'TBD') ?></div>
                                    <div>Scheduled: <?= esc(getFormattedDate($match['scheduled_at'] ?? null, 'Not set')) ?></div>
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php if (($match['match_status'] ?? '') === 'live'): ?>
                                        <a href="<?= site_url('/admin/matches/' . $match['id'] . '/start') ?>" class="btn btn-primary">Start</a>
                                    <?php endif; ?>
                                    <a href="<?= site_url('/admin/matches/' . $match['id'] . '/edit') ?>" class="btn btn-primary">Edit</a>
                                    <?php if (($match['match_status'] ?? '') === 'scheduled'): ?>
                                        <form action="<?= site_url('/admin/matches/' . $match['id'] . '/delete') ?>" method="post" onsubmit="return confirm('Archive this match? It will be hidden from the active list but not permanently removed.');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-outline-danger">Archive</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($otherMatches !== []): ?>
            <div class="panel">
                <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
                    <div>
                        <h2 class="h4 mb-1">Other Matches</h2>
                        <p class="text-secondary mb-0">Completed, archived, and other non-active match records.</p>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle admin-table">
                        <thead>
                            <tr>
                                <th>Match</th>
                                <th>Type</th>
                                <th>Venue</th>
                                <th>Scheduled</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($otherMatches as $match): ?>
                                <tr>
                                    <td><?= esc($match['team_name']) ?> vs <?= esc($match['opponent_name']) ?></td>
                                    <td><?= esc(ucwords(str_replace('_', ' ', $match['match_type']))) ?> · <?= esc((string) $match['format_overs']) ?> overs</td>
                                    <td><?= esc($match['venue'] ?: 'TBD') ?></td>
                                    <td><?= esc(getFormattedDate($match['scheduled_at'] ?? null, 'Not set')) ?></td>
                                    <td><?= esc($match['match_status']) ?></td>
                                    <td class="text-end">
                                        <div class="d-inline-flex flex-wrap justify-content-end gap-2">
                                            <?php if (($match['match_status'] ?? '') === 'completed'): ?>
                                                <a href="<?= site_url('/admin/matches/' . $match['id']) ?>" class="btn btn-sm btn-primary">Details</a>
                                            <?php else: ?>
                                                <a href="<?= site_url('/admin/matches/' . $match['id'] . '/edit') ?>" class="btn btn-sm btn-primary">Edit</a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>