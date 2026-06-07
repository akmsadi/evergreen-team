<aside id="sidebar" class="sidebar">
    <div class="logo-area">
        <a href="<?= site_url('/admin/dashboard') ?>" class="d-inline-flex align-items-center text-reset">
            <img src="<?= base_url('assets/images/logo-wheel-square.png') ?>" alt="Evergreen Team Logo" style="width: 50px;">
            <span class="ms-2">
                <span class="logo-text d-block">Evergreen</span>
                <span class="nav-text small">Team Admin</span>
            </span>
        </a>
    </div>

    <ul class="nav flex-column">
        <li class="px-4 py-2"><small class="nav-text">Main</small></li>
        <li><a class="nav-link<?= ($activeNav ?? '') === 'dashboard' ? ' active' : '' ?>" href="<?= site_url('/admin/dashboard') ?>"><i class="ti ti-home"></i><span class="nav-text">Dashboard</span></a></li>
        <li><a class="nav-link<?= ($activeNav ?? '') === 'matches' ? ' active' : '' ?>" href="<?= site_url('/admin/matches') ?>"><i class="ti ti-cricket"></i><span class="nav-text">Match Overview</span></a></li>
        <li><a class="nav-link<?= ($activeNav ?? '') === 'create-match' ? ' active' : '' ?>" href="<?= site_url('/admin/matches/create') ?>"><i class="ti ti-plus"></i><span class="nav-text">Add Match</span></a></li>
        <li><a class="nav-link<?= ($activeNav ?? '') === 'venues' ? ' active' : '' ?>" href="<?= site_url('/admin/venues') ?>"><i class="ti ti-map-pin"></i><span class="nav-text">Venues</span></a></li>
        <li><a class="nav-link<?= ($activeNav ?? '') === 'players' ? ' active' : '' ?>" href="<?= site_url('/admin/players') ?>"><i class="ti ti-users"></i><span class="nav-text">Player Overview</span></a></li>
        <li><a class="nav-link<?= ($activeNav ?? '') === 'create-player' ? ' active' : '' ?>" href="<?= site_url('/admin/players/create') ?>"><i class="ti ti-user-plus"></i><span class="nav-text">Add Player</span></a></li>
        <li><a class="nav-link<?= ($activeNav ?? '') === 'accounts' ? ' active' : '' ?>" href="<?= site_url('/admin/accounts') ?>"><i class="ti ti-wallet"></i><span class="nav-text">Accounts</span></a></li>
        <li class="px-4 pt-4 pb-2"><small class="nav-text">Account</small></li>
        <li><a class="nav-link" href="<?= site_url('/') ?>"><i class="ti ti-world"></i><span class="nav-text">View Site</span></a></li>
        <li><a class="nav-link" href="<?= site_url('/admin/logout') ?>"><i class="ti ti-logout"></i><span class="nav-text">Logout</span></a></li>
    </ul>
</aside>