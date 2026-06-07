<?= $this->extend('admin/default') ?>

<?= $this->section('title') ?>Add Player<?= $this->endSection() ?>

<?= $this->section('admin_nav') ?>create-player<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-card">
    <div class="panel">
        <div class="mb-4">
            <h1 class="h2 fw-bold mb-2">Add Player</h1>
            <p class="text-secondary mb-0">Create a new player record and set the initial approval status.</p>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
        <?php endif; ?>

        <?php $errors = session()->getFlashdata('errors') ?? []; ?>

        <form action="<?= site_url('/admin/players') ?>" method="post">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="name" class="form-label fw-semibold">Name</label>
                <input type="text" class="form-control<?= isset($errors['name']) ? ' is-invalid' : '' ?>" id="name" name="name" value="<?= esc(old('name')) ?>">
                <?php if (isset($errors['name'])): ?>
                    <div class="invalid-feedback"><?= esc($errors['name']) ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">Email (optional)</label>
                <input type="email" class="form-control<?= isset($errors['email']) ? ' is-invalid' : '' ?>" id="email" name="email" value="<?= esc(old('email')) ?>">
                <?php if (isset($errors['email'])): ?>
                    <div class="invalid-feedback"><?= esc($errors['email']) ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label fw-semibold">Phone</label>
                <input type="text" class="form-control<?= isset($errors['phone']) ? ' is-invalid' : '' ?>" id="phone" name="phone" value="<?= esc(old('phone')) ?>">
                <?php if (isset($errors['phone'])): ?>
                    <div class="invalid-feedback"><?= esc($errors['phone']) ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <label for="address" class="form-label fw-semibold">Address</label>
                <textarea class="form-control<?= isset($errors['address']) ? ' is-invalid' : '' ?>" id="address" name="address" rows="4"><?= esc(old('address')) ?></textarea>
                <?php if (isset($errors['address'])): ?>
                    <div class="invalid-feedback"><?= esc($errors['address']) ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold d-block">Status</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input<?= isset($errors['status']) ? ' is-invalid' : '' ?>" type="radio" name="status" id="status_pending" value="pending" <?= old('status') === 'pending' ? 'checked' : '' ?>>
                    <label class="form-check-label" for="status_pending">Pending</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input<?= isset($errors['status']) ? ' is-invalid' : '' ?>" type="radio" name="status" id="status_approved" value="approved" <?= old('status', 'approved') === 'approved' ? 'checked' : '' ?>>
                    <label class="form-check-label" for="status_approved">Approved</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input<?= isset($errors['status']) ? ' is-invalid' : '' ?>" type="radio" name="status" id="status_rejected" value="rejected" <?= old('status') === 'rejected' ? 'checked' : '' ?>>
                    <label class="form-check-label" for="status_rejected">Rejected</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input<?= isset($errors['status']) ? ' is-invalid' : '' ?>" type="radio" name="status" id="status_guest" value="guest" <?= old('status') === 'guest' ? 'checked' : '' ?>>
                    <label class="form-check-label" for="status_guest">Guest</label>
                </div>
                <?php if (isset($errors['status'])): ?>
                    <div class="text-danger small mt-2"><?= esc($errors['status']) ?></div>
                <?php endif; ?>
            </div>

            <?php $selectedStatus = old('status', 'approved'); ?>
            <div class="mb-4<?= $selectedStatus === 'guest' ? '' : ' d-none' ?>" id="guest-of-field">
                <label for="guest_of_player_id" class="form-label fw-semibold">Guest OF</label>
                <select class="form-select<?= isset($errors['guest_of_player_id']) ? ' is-invalid' : '' ?>" id="guest_of_player_id" name="guest_of_player_id">
                    <option value="">Select approved player</option>
                    <?php foreach ($approvedPlayers as $approvedPlayer): ?>
                        <option value="<?= esc((string) $approvedPlayer['id']) ?>" <?= old('guest_of_player_id') == $approvedPlayer['id'] ? 'selected' : '' ?>><?= esc($approvedPlayer['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="form-text">Required when the player status is Guest.</div>
                <?php if (isset($errors['guest_of_player_id'])): ?>
                    <div class="invalid-feedback"><?= esc($errors['guest_of_player_id']) ?></div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-evergreen">Save Player</button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const field = document.getElementById('guest-of-field');

        if (!field) {
            return;
        }

        const statusInputs = document.querySelectorAll('input[name="status"]');

        const syncGuestFieldVisibility = function() {
            const selected = document.querySelector('input[name="status"]:checked');
            field.classList.toggle('d-none', !selected || selected.value !== 'guest');
        };

        statusInputs.forEach(function(input) {
            input.addEventListener('change', syncGuestFieldVisibility);
        });

        syncGuestFieldVisibility();
    });
</script>
<?= $this->endSection() ?>