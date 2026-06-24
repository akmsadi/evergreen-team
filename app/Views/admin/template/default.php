<?php
$layoutMode = trim($this->renderSection('layout_mode'));
$isAuthLayout = $layoutMode === 'auth';
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Evergreen | Admin</title>
        <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('assets/images/favicon/apple-touch-icon.png') ?>">
        <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/images/favicon/favicon-32x32.png') ?>">
        <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/images/favicon/favicon-16x16.png') ?>">
        <link rel="shortcut icon" href="<?= base_url('assets/images/favicon/favicon.ico') ?>">
        <link rel="manifest" href="<?= base_url('assets/images/favicon/site.webmanifest') ?>">
        <?= $this->renderSection('head') ?>
        <link href="<?= base_url('assets/inapp/css/main.css') ?>" rel="stylesheet">
        <link href="<?= base_url('assets/css/admin.css') ?>" rel="stylesheet">
        <?= $this->renderSection('styles') ?>
    </head>

    <body class="<?= $isAuthLayout ? 'bg-body-secondary' : '' ?>">
        <?php if ($isAuthLayout): ?>
            <div class="container-fluid px-3">
                <?= $this->renderSection('content') ?>
            </div>
        <?php else: ?>
            <div id="overlay" class="overlay"></div>
            <nav id="topbar" class="navbar bg-white border-bottom fixed-top topbar px-3">
                <button id="toggleBtn" class="d-none d-lg-inline-flex btn btn-light btn-icon btn-sm" type="button" aria-label="Toggle sidebar">
                    <i class="ti ti-layout-sidebar-left-expand"></i>
                </button>
                <button id="mobileBtn" class="btn btn-light btn-icon btn-sm d-lg-none me-2" type="button" aria-label="Open sidebar">
                    <i class="ti ti-layout-sidebar-left-expand"></i>
                </button>
            </nav>
            <?= view('admin/template/_sidebar', ['activeNav' => $activeNav ?? '']) ?>
            <main id="content" class="content pt-9 pb-8">
                <div class="container-fluid">
                    <?= $this->renderSection('content') ?>
                </div>
            </main>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@latest/dist/js/bootstrap.bundle.min.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var sidebar = document.getElementById('sidebar');
                    var content = document.getElementById('content');
                    var topbar = document.getElementById('topbar');
                    var toggleBtn = document.getElementById('toggleBtn');
                    var mobileBtn = document.getElementById('mobileBtn');
                    var overlay = document.getElementById('overlay');

                    if (toggleBtn) {
                        toggleBtn.addEventListener('click', function () {
                            sidebar && sidebar.classList.toggle('collapsed');
                            content && content.classList.toggle('full');
                            topbar && topbar.classList.toggle('full');
                        });
                    }

                    if (mobileBtn) {
                        mobileBtn.addEventListener('click', function () {
                            sidebar && sidebar.classList.add('mobile-show');
                            overlay && overlay.classList.add('show');
                        });
                    }

                    if (overlay) {
                        overlay.addEventListener('click', function () {
                            sidebar && sidebar.classList.remove('mobile-show');
                            overlay.classList.remove('show');
                        });
                    }
                });
            </script>
        <?php endif; ?>
        <?= $this->renderSection('scripts') ?>
    </body>

</html>