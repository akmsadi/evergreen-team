<nav class="navbar navbar-expand-lg py-3 bg-body-tertiary" style="border-bottom: 5px solid #1f7a3d;">
    <div class="container-fluid g-5">
        <div class="content-div d-flex w-100">
            <a class="navbar-brand" href="<?= site_url('/') ?>">
                <img src="<?= base_url('assets/images/logo1.png') ?>" alt="Evergreen Team Logo" style="width: 180px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavMenu" aria-controls="mainNavMenu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="mainNavMenu">
                <div class="d-flex flex-column flex-lg-row gap-2 justify-content-end top-menu py-2 py-lg-0">
                    <a class="hover-link mx-2" href="#teams">Teams</a>
                    <a class="hover-link mx-2" href="#members">Members</a>
                    <a class="hover-link mx-2" href="#gallery">Gallery</a>
                    <a class="hover-link mx-2" href="#join">Join</a>
                    <?php if (session()->get('is_admin')): ?>
                        <a class="hover-link mx-2" href="<?= site_url('/admin/dashboard') ?>">Dashboard</a>
                    <?php else: ?>
                        <a class="hover-link mx-2" href="<?= site_url('/admin/login') ?>">Admin</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</nav>