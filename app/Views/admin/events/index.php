<?= $this->extend('admin/default') ?>

<?= $this->section('title') ?>Event Gallery<?= $this->endSection() ?>

<?= $this->section('admin_nav') ?>events<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php $errors = session()->getFlashdata('errors') ?? []; ?>
<div class="content-card">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="h2 mb-2">Event Gallery</h1>
            <p class="text-secondary mb-0">Create and manage events. Add images to each event from the edit page.</p>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="panel">
                <h2 class="h4 mb-3">Add Event</h2>
                <form action="<?= site_url('/admin/events') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="title" class="form-label fw-semibold">Event Title</label>
                        <input type="text" class="form-control<?= isset($errors['title']) ? ' is-invalid' : '' ?>" id="title" name="title" value="<?= esc(old('title')) ?>" placeholder="Enter event title">
                        <?php if (isset($errors['title'])): ?>
                            <div class="invalid-feedback"><?= esc($errors['title']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Description</label>
                        <textarea class="form-control<?= isset($errors['description']) ? ' is-invalid' : '' ?>" id="description" name="description" rows="4" placeholder="Brief description of the event"><?= esc(old('description')) ?></textarea>
                        <?php if (isset($errors['description'])): ?>
                            <div class="invalid-feedback"><?= esc($errors['description']) ?></div>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="btn btn-primary">Create Event</button>
                </form>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="panel">
                <h2 class="h4 mb-3">All Events</h2>
                <?php if ($events === []): ?>
                    <p class="text-secondary mb-0">No events created yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle admin-table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Created</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($events as $event): ?>
                                    <tr>
                                        <td><?= esc($event['title']) ?></td>
                                        <td class="text-secondary" style="max-width:220px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                            <?= esc(mb_strimwidth($event['description'] ?? '', 0, 80, '…')) ?>
                                        </td>
                                        <td class="text-secondary"><?= esc(date('d M Y', strtotime($event['created_at']))) ?></td>
                                        <td class="text-end">
                                            <div class="d-inline-flex gap-2">
                                                <a href="<?= site_url('/admin/events/' . $event['id'] . '/edit') ?>" class="btn btn-sm btn-primary">Edit / Images</a>
                                                <form action="<?= site_url('/admin/events/' . $event['id'] . '/delete') ?>" method="post" onsubmit="return confirm('Delete this event and all its images?');">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
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
