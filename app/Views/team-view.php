<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Evergreen Team</title>
        <meta name="description" content="Evergreen Team is a cricket club built on discipline, teamwork, and a winning green spirit.">
        <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('assets/images/favicon/apple-touch-icon.png') ?>">
        <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/images/favicon/favicon-32x32.png') ?>">
        <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/images/favicon/favicon-16x16.png') ?>">
        <link rel="shortcut icon" href="<?= base_url('assets/images/favicon/favicon.ico') ?>">
        <link rel="manifest" href="<?= base_url('assets/images/favicon/site.webmanifest') ?>">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@latest/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>?v=1.3">
    </head>

    <body>
        <?= $this->include('_top-nav') ?>

        <main class="bg-body-secondary">
            <!-- Hero -->
            <section class="position-relative overflow-hidden">
                <img src="<?= base_url('assets/images/teams/' . esc($team['banner'])) ?>" class="w-100" style="height:550px;object-fit:cover;" alt="Team Banner">
                <div class="position-absolute top-0 start-0 w-100 h-100" style="background:rgba(0,0,0,.55);"></div>
                <div class="position-absolute top-50 start-50 translate-middle text-center text-white">
                    <img src="<?= base_url('assets/images/teams/' . esc($team['logo'])) ?>" class="rounded-circle shadow mb-4" width="150">
                    <h1 class="display-3 fw-bold"><?= esc($team['name']) ?></h1>
                    <p class="fs-4 mb-4"><?= esc($team['slogan']) ?></p>
                    <a href="#players" class="btn btn-success btn-lg px-4">
                        View Squad
                    </a>
                </div>
            </section>

            <!-- Quick Stats -->
            <section class="py-5">
                <div class="container">
                    <div class="row g-4">
                        <div class="col-md-3">
                            <div class="card text-center border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <h2 class="text-success"><?= esc($team['established']) ?></h2>
                                    <p class="mb-0">Founded</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <h2 class="text-success"><?= esc($team['matches']) ?></h2>
                                    <p class="mb-0">Matches</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <h2 class="text-success"><?= esc($team['wins']) ?></h2>
                                    <p class="mb-0">Wins</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <h2 class="text-success"><?= esc($team['titles']) ?></h2>
                                    <p class="mb-0">Titles</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- About -->
            <section class="py-5 bg-body-tertiary">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <img src="<?= base_url('assets/images/teams/' . esc($team['logo'])) ?>" class="img-fluid rounded shadow">
                        </div>
                        <div class="col-lg-6">
                            <h2 class="fw-bold mb-4"><?= esc($team['name']) ?></h2>
                            <p class="text-muted"><?= $team['about'] ?></p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Leadership -->
            <section class="py-5">
                <div class="container">
                    <div class="text-center mb-5">
                        <h2 class="fw-bold">
                            Team Leadership
                        </h2>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm">
                                <img src="<?= base_url('assets/images/empty-male.png') ?>" class="card-img-top">
                                <div class="card-body text-center">
                                    <h4><?= esc($team['captain_name'] ?? 'Not Assigned') ?></h4>
                                    <p class="text-success fw-semibold">
                                        Captain
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm">
                                <img src="<?= base_url('assets/images/empty-male.png') ?>" class="card-img-top">
                                <div class="card-body text-center">
                                    <h4><?= esc($team['vice_captain_name'] ?? 'Not Assigned') ?></h4>
                                    <p class="text-success fw-semibold">
                                        Vice Captain
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Players -->
            <section id="players" class="py-5 bg-body-tertiary">
                <div class="container">
                    <div class="text-center mb-5">
                        <h2 class="fw-bold">Squad</h2>
                    </div>
                    <div class="row g-4">
                        <?php foreach ($members as $member): ?>
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <img src="<?= base_url('assets/images/empty-male.png') ?>" class="card-img-top">
                                    <div class="card-body text-center">
                                        <h5><?= esc($member['linked_player_name'] ?? $member['name']) ?></h5>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

            <!-- Achievements -->
            <section class="py-5">
                <div class="container">
                    <div class="text-center mb-5">
                        <h2 class="fw-bold">
                            Achievements
                        </h2>
                    </div>
                    <?php if ($achievements): ?>
                        <div class="row">
                            <?php foreach ($achievements as $achievement): ?>
                                <div class="col-md-4">
                                    <div class="card border-success">
                                        <div class="card-body">
                                            <h4 class="text-success">
                                                <?= esc($achievement['title']) ?>
                                            </h4>
                                            <p><?= esc($achievement['description']) ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-center text-muted">No achievements recorded for this team yet.</p>
                    <?php endif; ?>
                </div>
            </section>
        </main>

        <footer class="container-fluid g-5 py-4 text-center" style="border-top: 5px solid #1f7a3d;">Copyright &copy; 2026 Evergreen Team. All rights reserved.</footer>
        <button type="button" class="back-to-top" id="backToTopButton" aria-label="Back to top" title="Back to top">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M12 19V5" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M6.75 10.25L12 5l5.25 5.25" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@latest/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
        <script src="<?= base_url('assets/js/home.js') ?>"></script>
    </body>

</html>