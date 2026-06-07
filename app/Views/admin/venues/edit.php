<?= $this->extend('admin/default') ?>

<?= $this->section('title') ?>Edit Venue<?= $this->endSection() ?>

<?= $this->section('admin_nav') ?>venues<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php $errors = session()->getFlashdata('errors') ?? []; ?>
<div class="content-stack">
    <div class="panel p-4 p-md-5 mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <p class="text-uppercase text-success fw-bold small mb-2">Evergreen Team Admin</p>
                <h1 class="h2 fw-bold mb-2">Edit Venue</h1>
                <p class="text-secondary mb-0">Update the venue name used by existing and future matches.</p>
            </div>
            <div>
                <a href="<?= site_url('/admin/venues') ?>" class="btn btn-light border">Back to Venues</a>
            </div>
        </div>
    </div>

    <div class="content-card p-4">
        <div class="mb-4">
            <div class="small text-secondary text-uppercase mb-2">Current Usage</div>
            <div class="summary-value" style="font-size: 1.4rem;"><?= esc((string) $matchCount) ?></div>
            <p class="text-secondary mb-0">Matches currently assigned to this venue.</p>
        </div>

        <form action="<?= site_url('/admin/venues/' . $venue['id'] . '/update') ?>" method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label for="name" class="form-label fw-semibold">Venue Name</label>
                <input type="text" class="form-control<?= isset($errors['name']) ? ' is-invalid' : '' ?>" id="name" name="name" value="<?= esc(old('name', $venue['name'])) ?>">
                <?php if (isset($errors['name'])): ?>
                    <div class="invalid-feedback"><?= esc($errors['name']) ?></div>
                <?php endif; ?>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= site_url('/admin/venues') ?>" class="btn btn-light border">Cancel</a>
                <button type="submit" class="btn btn-evergreen">Save Changes</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>