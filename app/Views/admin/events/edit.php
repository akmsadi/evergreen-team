<?= $this->extend('admin/template/default') ?>

<?= $this->section('admin_nav') ?>events<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style {csp-style-nonce}>
    .event-image-card {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        background: #f8f9fa;
    }

    .event-image-card img {
        width: 100%;
        height: 160px;
        object-fit: cover;
        display: block;
    }

    .event-image-caption {
        padding: 6px 10px;
        font-size: 0.78rem;
        color: #4b5563;
        min-height: 30px;
    }

    .event-image-delete {
        position: absolute;
        top: 6px;
        right: 6px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php $errors = session()->getFlashdata('errors') ?? []; ?>
<div class="content-card">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="h2 mb-1"><?= esc($event['title']) ?></h1>
            <p class="text-secondary mb-0">Edit event details and manage its image gallery.</p>
        </div>
        <a href="<?= site_url('/admin/events') ?>" class="btn btn-light border">Back to Events</a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="panel">
                <h2 class="h5 mb-3">Event Details</h2>
                <form action="<?= site_url('/admin/events/' . $event['id'] . '/update') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="title" class="form-label fw-semibold">Event Title</label>
                        <input type="text" class="form-control<?= isset($errors['title']) ? ' is-invalid' : '' ?>" id="title" name="title" value="<?= esc(old('title', $event['title'])) ?>">
                        <?php if (isset($errors['title'])): ?>
                            <div class="invalid-feedback"><?= esc($errors['title']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Description</label>
                        <textarea class="form-control<?= isset($errors['description']) ? ' is-invalid' : '' ?>" id="description" name="description" rows="5"><?= esc(old('description', $event['description'] ?? '')) ?></textarea>
                        <?php if (isset($errors['description'])): ?>
                            <div class="invalid-feedback"><?= esc($errors['description']) ?></div>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="panel">
                <h2 class="h5 mb-3">Upload Image</h2>
                <form action="<?= site_url('/admin/events/' . $event['id'] . '/images') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="images" class="form-label fw-semibold">Image Files</label>
                        <input type="file" class="form-control<?= !empty($errors) ? ' is-invalid' : '' ?>" id="images" name="images[]" accept="image/jpeg,image/png,image/webp" multiple>
                        <?php if (!empty($errors)): ?>
                            <div class="invalid-feedback d-block">
                                <?php foreach ($errors as $err): ?>
                                    <div><?= esc($err) ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <div class="form-text">JPEG, PNG or WebP — max 5 MB each. You can select multiple files.</div>
                    </div>
                    <button type="submit" class="btn btn-primary">Upload Images</button>
                </form>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 mb-0">Event Images <span class="text-secondary">(<?= count($images) ?>)</span></h2>
        </div>

        <?php if ($images === []): ?>
            <p class="text-secondary mb-0">No images uploaded yet. Use the form above to add images.</p>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($images as $img): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="event-image-card border">
                            <img src="<?= base_url(esc($img['image_path'])) ?>" alt="<?= esc($img['caption'] ?? 'Event image') ?>">
                            <form action="<?= site_url('/admin/events/' . $event['id'] . '/images/' . $img['id'] . '/delete') ?>" method="post" onsubmit="return confirm('Remove this image?');" class="event-image-delete">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-danger btn-sm" title="Remove image"><i class="ti ti-trash"></i></button>
                            </form>
                            <div class="event-image-caption">
                                <?php if (!empty($img['caption'])): ?>
                                    <?= esc($img['caption']) ?>
                                <?php else: ?>
                                    <span class="text-secondary fst-italic">No caption</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>