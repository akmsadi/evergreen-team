<?= $this->extend('admin/default') ?>

<?= $this->section('title') ?>Venue Management<?= $this->endSection() ?>

<?= $this->section('admin_nav') ?>venues<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php $errors = session()->getFlashdata('errors') ?? []; ?>
<div class="content-card">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="h2 mb-2">Venue Management</h1>
            <p class="text-secondary mb-0">Create, update, and review the venue list used by match records.</p>
        </div>
        <a href="<?= site_url('/admin/matches/create') ?>" class="btn btn-light border">Back to Match Form</a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="panel">
                <h2 class="h4 mb-3">Add Venue</h2>
                <form action="<?= site_url('/admin/venues') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Venue Name</label>
                        <input type="text" class="form-control<?= isset($errors['name']) ? ' is-invalid' : '' ?>" id="name" name="name" value="<?= esc(old('name')) ?>" placeholder="Enter venue name">
                        <?php if (isset($errors['name'])): ?>
                            <div class="invalid-feedback"><?= esc($errors['name']) ?></div>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="btn btn-evergreen">Save Venue</button>
                </form>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="panel">
                <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
                    <div>
                        <h2 class="h4 mb-1">Venue List</h2>
                        <p class="text-secondary mb-0">Venues already available for match selection.</p>
                    </div>
                </div>
                <?php if ($venues === []): ?>
                    <p class="text-secondary mb-0">No venues added yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle admin-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Matches</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($venues as $venue): ?>
                                    <tr>
                                        <td><?= esc($venue['name']) ?></td>
                                        <td><?= esc((string) $venue['match_count']) ?></td>
                                        <td class="text-end">
                                            <div class="d-inline-flex gap-2">
                                                <a href="<?= site_url('/admin/venues/' . $venue['id'] . '/edit') ?>" class="btn btn-sm btn-primary">Edit</a>
                                                <form action="<?= site_url('/admin/venues/' . $venue['id'] . '/delete') ?>" method="post" onsubmit="return confirm('Delete this venue?');">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" <?= (int) $venue['match_count'] > 0 ? 'disabled title="This venue is used by matches."' : '' ?>>Delete</button>
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
    </div>
</div>
<?= $this->endSection() ?>