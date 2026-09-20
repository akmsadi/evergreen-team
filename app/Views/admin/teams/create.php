<?= $this->extend('admin/template/default') ?>

<?= $this->section('admin_nav') ?>teams<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php $errors = session()->getFlashdata('errors') ?? []; ?>
<div class="content-stack">
    <div class="panel p-4 p-md-5 mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <p class="text-uppercase text-success fw-bold small mb-2">Evergreen Team Admin</p>
                <h1 class="h2 fw-bold mb-2">Add Team</h1>
                <p class="text-secondary mb-0">Create a new team with an optional logo.</p>
            </div>
            <div>
                <a href="<?= site_url('/admin/teams') ?>" class="btn btn-light border">Back to Teams</a>
            </div>
        </div>
    </div>

    <div class="content-card p-4">
        <form action="<?= site_url('/admin/teams') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label for="name" class="form-label fw-semibold">Team Name</label>
                <input type="text" class="form-control<?= isset($errors['name']) ? ' is-invalid' : '' ?>" id="name" name="name" value="<?= esc(old('name')) ?>" placeholder="Enter team name">
                <?php if (isset($errors['name'])): ?>
                    <div class="invalid-feedback"><?= esc($errors['name']) ?></div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="is_external" name="is_external" value="1" <?= old('is_external') ? 'checked' : '' ?>>
                    <label class="form-check-label" for="is_external">
                        <strong>External Team</strong>
                        <span class="text-secondary d-block small">Check this if this team is not part of your home teams (e.g., opposing teams)</span>
                    </label>
                </div>
            </div>
            <div class="mb-4">
                <label for="logo" class="form-label fw-semibold">Logo <span class="text-secondary fw-normal">(optional, max 2 MB)</span></label>
                <input type="file" class="form-control<?= isset($errors['logo']) ? ' is-invalid' : '' ?>" id="logo" name="logo" accept="image/*">
                <?php if (isset($errors['logo'])): ?>
                    <div class="invalid-feedback"><?= esc($errors['logo']) ?></div>
                <?php endif; ?>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= site_url('/admin/teams') ?>" class="btn btn-light border">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Team</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>