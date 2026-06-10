<?= $this->extend('admin/default') ?>

<?= $this->section('title') ?>Admin Login<?= $this->endSection() ?>

<?= $this->section('layout_mode') ?>auth<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php $errors = session()->getFlashdata('errors') ?? []; ?>
<div class="container d-flex align-items-center justify-content-center min-vh-100">
    <div class="card shadow-sm" style="max-width: 420px; width: 100%;">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <img src="<?= base_url('assets/images/logo1.png') ?>" alt="Evergreen Team Logo" class="mb-3" style="width: 120px;">
                <h1 class="card-title mb-0 h5">Sign in to your account</h1>
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('auth_error')): ?>
                <div class="alert alert-danger"><?= esc(session()->getFlashdata('auth_error')) ?></div>
            <?php endif; ?>

            <form action="<?= site_url('/admin/login') ?>" method="post" class="mt-3">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="login" class="form-label">Username or Email</label>
                    <input
                        type="text"
                        class="form-control<?= isset($errors['login']) ? ' is-invalid' : '' ?>"
                        id="login"
                        name="login"
                        value="<?= esc(old('login')) ?>"
                        placeholder="admin or admin@evergreenteam.com">
                    <?php if (isset($errors['login'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['login']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input
                        type="password"
                        class="form-control<?= isset($errors['password']) ? ' is-invalid' : '' ?>"
                        id="password"
                        name="password"
                        placeholder="Enter your password">
                    <?php if (isset($errors['password'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['password']) ?></div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary w-100">Sign in</button>
            </form>

            <div class="text-center mt-3 small text-muted">
                <a href="<?= site_url('/') ?>" class="link-primary">Back to Home</a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>