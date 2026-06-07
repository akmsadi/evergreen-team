<?php
$pageTitle = trim($this->renderSection('title'));
$layoutMode = trim($this->renderSection('layout_mode'));
$adminNav = trim($this->renderSection('admin_nav'));
$isAuthLayout = $layoutMode === 'auth';
$displayTitle = $pageTitle !== '' ? $pageTitle : 'Admin';
$displayUsername = $username ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($displayTitle) ?> | Evergreen Team</title>
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('assets/images/favicon/apple-touch-icon.png') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/images/favicon/favicon-32x32.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/images/favicon/favicon-16x16.png') ?>">
    <link rel="shortcut icon" href="<?= base_url('assets/images/favicon/favicon.ico') ?>">
    <link rel="manifest" href="<?= base_url('assets/images/favicon/site.webmanifest') ?>">
    <?= $this->renderSection('head') ?>
    <link href="<?= base_url('assets/inapp/css/main.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/admin.css') ?>" rel="stylesheet">
    <style {csp-style-nonce}>
        body {
            --bs-body-font-size: 13px;
            font-size: 13px;
        }

        body,
        body h1,
        body h2,
        body h3,
        body h4,
        body h5,
        body h6,
        body strong,
        body b,
        body .fw-bold,
        body .fw-semibold {
            font-weight: 400 !important;
        }

        .admin-table {
            --bs-table-bg: transparent;
            --bs-table-striped-bg: rgba(31, 122, 61, 0.04);
            --bs-table-hover-bg: rgba(31, 122, 61, 0.06);
            margin-bottom: 0;
        }

        .admin-table th {
            color: #4b5563;
            font-size: 0.78rem;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .admin-table td {
            vertical-align: middle;
        }
    </style>
    <?= $this->renderSection('styles') ?>
</head>

<body class="<?= $isAuthLayout ? 'admin-auth-body' : '' ?>">
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
            <ul class="list-unstyled d-flex align-items-center mb-0 gap-2">
                <li class="dropdown">
                    <a href="#" class="d-flex align-items-center gap-2 text-reset" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="avatar avatar-sm rounded-circle avatar-fallback"><?= esc(strtoupper(substr($displayUsername, 0, 1))) ?></span>
                        <span class="d-none d-md-inline"><?= esc($displayUsername) ?></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end p-2 admin-user-menu">
                        <a href="<?= site_url('/admin/dashboard') ?>" class="dropdown-item">Dashboard</a>
                        <a href="<?= site_url('/') ?>" class="dropdown-item" target="_blank">View Site</a>
                        <div class="dropdown-divider"></div>
                        <a href="<?= site_url('/admin/logout') ?>" class="dropdown-item text-danger">Logout</a>
                    </div>
                </li>
            </ul>
        </nav>

        <?= view('admin/_sidebar', ['username' => $displayUsername, 'activeNav' => $adminNav]) ?>

        <main id="content" class="content pt-9 pb-8">
            <div class="container-fluid">
                <?= $this->renderSection('content') ?>
            </div>
        </main>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@latest/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var sidebar = document.getElementById('sidebar');
                var content = document.getElementById('content');
                var topbar = document.getElementById('topbar');
                var toggleBtn = document.getElementById('toggleBtn');
                var mobileBtn = document.getElementById('mobileBtn');
                var overlay = document.getElementById('overlay');

                if (toggleBtn) {
                    toggleBtn.addEventListener('click', function() {
                        sidebar && sidebar.classList.toggle('collapsed');
                        content && content.classList.toggle('full');
                        topbar && topbar.classList.toggle('full');
                    });
                }

                if (mobileBtn) {
                    mobileBtn.addEventListener('click', function() {
                        sidebar && sidebar.classList.add('mobile-show');
                        overlay && overlay.classList.add('show');
                    });
                }

                if (overlay) {
                    overlay.addEventListener('click', function() {
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