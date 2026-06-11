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
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@latest/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>

<body>
    <nav class="navbar navbar-expand-lg py-3" style="border-bottom: 5px solid #1f7a3d;">
        <div class="container-fluid g-5">
            <a class="navbar-brand" href="#home">
                <img src="<?= base_url('assets/images/logo1.png') ?>" alt="Evergreen Team Logo" style="width: 180px;">
            </a>
            <div class="d-flex gap-2 flex-wrap justify-content-end top-menu">
                <a class="btn text-uppercase" href="#about">About</a>
                <a class="btn text-uppercase" href="#stories">Stories</a>
                <a class="btn text-uppercase" href="#gallery">Gallery</a>
                <a class="btn text-uppercase" href="#team">Team</a>
                <a class="btn text-uppercase" href="#testimonials">Testimonials</a>
                <a class="btn text-uppercase" href="#join">Join Us</a>
                <?php if (session()->get('is_admin')): ?>
                    <a class="btn text-uppercase" href="<?= site_url('/admin/dashboard') ?>">Dashboard</a>
                <?php else: ?>
                    <a class="btn text-uppercase" href="<?= site_url('/admin/login') ?>">Admin</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <main id="home" class="pb-5 bg-body-secondary">
        <section class="container-fluid g-5">
            <div class="p-4 p-md-5">
                <div class="hero-grid position-relative" style="z-index: 1;">
                    <div>
                        <p class="eyebrow mb-3">Cricket. Commitment. Community.</p>
                        <h1 class="display-title mb-3">Play bold with<br><span class="accent-text">Evergreen Team</span></h1>
                        <p class="fs-5 text-dark mb-4">A sharper home page for a sharper club: match energy, training culture, player identity, and community momentum all in one place.</p>
                        <div class="d-flex flex-wrap gap-3">
                            <a class="btn btn-evergreen btn-lg rounded-pill px-4" href="#join">Join the Squad</a>
                            <a class="btn btn-outline-evergreen btn-lg rounded-pill px-4" href="#stories">Explore Stories</a>
                        </div>
                        <div class="hero-badge-row">
                            <span class="hero-badge">Structured nets twice a week</span>
                            <span class="hero-badge">Game-speed fielding blocks</span>
                            <span class="hero-badge">Weekend fixtures and club friendlies</span>
                        </div>
                    </div>
                    <div class="hero-side-stack">
                        <div class="pitch-mark p-4 p-md-4 text-center">
                            <p class="eyebrow mb-2">Home Ground</p>
                            <h2 class="h1 fw-bold mb-3 text-black">Evergreen Oval</h2>
                            <p class="mb-4">Built for focused practice, match-day energy, and the kind of teamwork that wins close games.</p>
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="stat-card p-3">
                                        <div class="stat-value"><?= esc((string) $playerCount) ?></div>
                                        <div class="small text-uppercase">Active Players</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-card p-3">
                                        <div class="stat-value"><?= esc((string) $pendingCount) ?></div>
                                        <div class="small text-uppercase">Pending Requests</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mini-panel">
                            <div class="d-flex justify-content-between align-items-start gap-3">
                                <div>
                                    <div class="eyebrow mb-2">This Week</div>
                                    <h3 class="h4 fw-bold mb-2">Two intense sessions, one match focus</h3>
                                    <p class="text-secondary mb-0">Batting against spin on Tuesday, death-over bowling on Thursday, then a Saturday pressure simulation.</p>
                                </div>
                                <div class="mini-panel__value">3</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="about" class="container-fluid g-5">
            <div class="p-4 p-md-5">
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3 mb-4">
                    <div>
                        <p class="eyebrow mb-2">Team Identity</p>
                        <h2 class="section-title mb-2">Evergreen means consistent, prepared, and hard to break.</h2>
                        <p class="section-copy mb-0">We value smart cricket, high standards, and a team-first culture that keeps improving every season. Every session is designed to feel like match preparation, not just attendance.</p>
                    </div>
                    <a class="btn btn-secondary" href="#team">Meet the Core Group</a>
                </div>
                <div class="feature-grid">
                    <div class="feature-card">
                        <div class="feature-card__number">01</div>
                        <h3 class="h4 fw-bold mb-2">Structured growth</h3>
                        <p class="mb-0 text-secondary">Players work through batting, bowling, and fielding progressions with clear role expectations.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-card__number">02</div>
                        <h3 class="h4 fw-bold mb-2">Pressure-first training</h3>
                        <p class="mb-0 text-secondary">Sessions include chase simulations, over management, and match-intensity decision making.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-card__number">03</div>
                        <h3 class="h4 fw-bold mb-2">Club culture</h3>
                        <p class="mb-0 text-secondary">Discipline, communication, and calm execution matter as much as talent inside the squad.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="stories" class="container-fluid g-5">
            <div class="p-4 p-md-5">
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3 mb-4">
                    <div>
                        <p class="eyebrow mb-2">Club Stories</p>
                        <h2 class="section-title mb-2">A landing page with motion, not just blocks.</h2>
                        <p class="section-copy mb-0">The carousel gives the homepage a more dynamic heartbeat and lets you tell three different versions of the club in the first scroll.</p>
                    </div>
                </div>
                <div id="evergreenStoryCarousel" class="carousel slide story-carousel" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <div class="story-slide">
                                <div class="story-slide__content">
                                    <span class="story-slide__tag">Matchday Energy</span>
                                    <h3 class="story-slide__title">Calm starts. Ruthless finishes.</h3>
                                    <p class="story-slide__copy">Evergreen plays with shape, discipline, and a clear plan from the first over to the closing spell.</p>
                                </div>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="story-slide" style="background: linear-gradient(135deg, rgba(17, 17, 17, 0.18), rgba(17, 17, 17, 0.5)), linear-gradient(135deg, #734b21 0%, #a66a2a 52%, #d69b52 100%);">
                                <div class="story-slide__content">
                                    <span class="story-slide__tag">Training Rhythm</span>
                                    <h3 class="story-slide__title">Sessions built to feel like overs that matter.</h3>
                                    <p class="story-slide__copy">Batting blocks, bowling plans, and game-speed fielding drills are organized around real match situations.</p>
                                </div>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="story-slide" style="background: linear-gradient(135deg, rgba(17, 17, 17, 0.12), rgba(17, 17, 17, 0.46)), linear-gradient(135deg, #1a2a6c 0%, #2b5876 58%, #4e4376 100%);">
                                <div class="story-slide__content">
                                    <span class="story-slide__tag">Club Culture</span>
                                    <h3 class="story-slide__title">A team identity people remember after one visit.</h3>
                                    <p class="story-slide__copy">New players see standards quickly: punctuality, intent, and a team-first mindset are part of the environment.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button class="carousel-control-prev justify-content-start" type="button" data-bs-target="#evergreenStoryCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next justify-content-end" type="button" data-bs-target="#evergreenStoryCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                    <div class="carousel-indicators position-static mt-4 mb-0">
                        <button type="button" data-bs-target="#evergreenStoryCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                        <button type="button" data-bs-target="#evergreenStoryCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                        <button type="button" data-bs-target="#evergreenStoryCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                    </div>
                </div>
            </div>
        </section>

        <section id="gallery" class="container-fluid g-5">
            <div class="section-shell">
                <div class="mb-4">
                    <p class="eyebrow mb-2">Gallery</p>
                    <h2 class="section-title mb-2">Different moments, one clear club identity.</h2>
                    <p class="section-copy mb-0">This gives the homepage some atmosphere even without a full photo library yet. The layout is ready for real match and training images later.</p>
                </div>
                <div class="gallery-grid">
                    <article class="gallery-card gallery-card--wide">
                        <div class="gallery-card__meta">Session Focus</div>
                        <h3 class="gallery-card__title">High-tempo nets with match-style intent</h3>
                        <p class="gallery-card__copy">Sharp rotations, loud communication, and game-pressure batting plans define the weekly rhythm.</p>
                    </article>
                    <article class="gallery-card gallery-card--pitch">
                        <div class="gallery-card__meta">Pitch View</div>
                        <h3 class="gallery-card__title">Practice surfaces that reward discipline</h3>
                    </article>
                    <article class="gallery-card gallery-card--night">
                        <div class="gallery-card__meta">Evening Block</div>
                        <h3 class="gallery-card__title">Night sessions under lights and pressure</h3>
                    </article>
                    <article class="gallery-card gallery-card--locker">
                        <div class="gallery-card__meta">Club Routine</div>
                        <h3 class="gallery-card__title">From warm-up to post-session review, the details are part of the identity.</h3>
                    </article>
                </div>
            </div>
        </section>

        <section id="team" class="container-fluid g-5">
            <div class="section-shell">
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3 mb-4">
                    <div>
                        <p class="eyebrow mb-2">Team Members</p>
                        <h2 class="section-title mb-2">The people shaping the club on and off the field.</h2>
                        <p class="section-copy mb-0">A dedicated section for core team faces gives the homepage personality and makes it feel like a club, not a placeholder.</p>
                    </div>
                </div>
                <div class="team-grid">
                    <article class="team-card">
                        <div class="team-avatar">AK</div>
                        <h3 class="h5 fw-bold mb-1">Arif Khan</h3>
                        <div class="team-role mb-3">Captain and Opening Batter</div>
                        <p class="mb-0 text-secondary">Sets the tone with calm leadership, smart batting tempo, and strong standards in training.</p>
                        <div class="team-pill-list">
                            <span class="team-pill">Leadership</span>
                            <span class="team-pill">Top Order</span>
                        </div>
                    </article>
                    <article class="team-card">
                        <div class="team-avatar">RH</div>
                        <h3 class="h5 fw-bold mb-1">Raihan Hossain</h3>
                        <div class="team-role mb-3">Vice Captain and Pace Lead</div>
                        <p class="mb-0 text-secondary">Drives intensity in the bowling unit and keeps the group locked in during pressure overs.</p>
                        <div class="team-pill-list">
                            <span class="team-pill">New Ball</span>
                            <span class="team-pill">Death Overs</span>
                        </div>
                    </article>
                    <article class="team-card">
                        <div class="team-avatar">SN</div>
                        <h3 class="h5 fw-bold mb-1">Shahriar Nabil</h3>
                        <div class="team-role mb-3">Wicketkeeper and Game Reader</div>
                        <p class="mb-0 text-secondary">Keeps the field sharp, reads angles quickly, and lifts the team voice behind the stumps.</p>
                        <div class="team-pill-list">
                            <span class="team-pill">WK</span>
                            <span class="team-pill">Field Setup</span>
                        </div>
                    </article>
                    <article class="team-card">
                        <div class="team-avatar">TH</div>
                        <h3 class="h5 fw-bold mb-1">Tamim Hasan</h3>
                        <div class="team-role mb-3">All-Rounder and Fitness Standard</div>
                        <p class="mb-0 text-secondary">Brings balance across departments and pushes the squad to train with consistency.</p>
                        <div class="team-pill-list">
                            <span class="team-pill">All-Rounder</span>
                            <span class="team-pill">Fitness</span>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section id="fixtures" class="container-fluid g-5">
            <div class="section-shell">
                <div class="row g-4 align-items-center">
                    <div class="col-lg-5">
                        <p class="eyebrow mb-2">Upcoming Focus</p>
                        <h2 class="section-title mb-3">Ready for the next innings.</h2>
                        <p class="fs-5 mb-3">Evergreen Team trains to stay prepared, whether it is a local league fixture, a weekend friendly, or a knockout match under pressure.</p>
                        <div class="cta-strip">
                            <div>
                                <div class="fw-bold">Current mode</div>
                                <div class="text-secondary">Pre-season rhythm with match-pressure sessions</div>
                            </div>
                            <a class="btn btn-evergreen rounded-pill px-4" href="#join">Join the Next Session</a>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="info-card p-4 h-100">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                                <div>
                                    <div class="fw-bold">Evergreen Team vs City Strikers</div>
                                    <div class="text-secondary">Saturday, 4:00 PM</div>
                                </div>
                                <span class="badge text-bg-success rounded-pill px-3 py-2">Home</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                                <div>
                                    <div class="fw-bold">Evergreen Team vs Royal Smashers</div>
                                    <div class="text-secondary">Sunday, 10:00 AM</div>
                                </div>
                                <span class="badge text-bg-light border text-dark rounded-pill px-3 py-2">Away</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold">Training Camp</div>
                                    <div class="text-secondary">Wednesday, 6:30 PM</div>
                                </div>
                                <span class="badge text-bg-success rounded-pill px-3 py-2">Practice</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="testimonials" class="container-fluid g-5">
            <div class="section-shell">
                <div class="mb-4">
                    <p class="eyebrow mb-2">Testimonials</p>
                    <h2 class="section-title mb-2">Why players and supporters keep coming back.</h2>
                    <p class="section-copy mb-0">This section gives the homepage social proof and human voice, which the current landing page is missing.</p>
                </div>
                <div class="row g-4">
                    <div class="col-lg-4">
                        <article class="testimonial-card">
                            <p class="mb-4">The difference is the structure. Sessions do not feel random. Every drill feels connected to a match situation.</p>
                            <h3 class="h6 fw-bold mb-1">Sabbir Hossain</h3>
                            <div class="testimonial-role">Top-order player</div>
                        </article>
                    </div>
                    <div class="col-lg-4">
                        <article class="testimonial-card">
                            <p class="mb-4">Even as a newer player, you know what the standards are immediately. The team is welcoming without lowering expectations.</p>
                            <h3 class="h6 fw-bold mb-1">Nafis Ahmed</h3>
                            <div class="testimonial-role">Squad member</div>
                        </article>
                    </div>
                    <div class="col-lg-4">
                        <article class="testimonial-card">
                            <p class="mb-4">Matchday energy feels organized. The club identity is visible in the details, not just in the logo or colors.</p>
                            <h3 class="h6 fw-bold mb-1">Farhan Bin Yusuf</h3>
                            <div class="testimonial-role">Support staff volunteer</div>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <section id="join" class="container-fluid g-5">
            <div class="hero-card p-4 p-md-5">
                <div class="row align-items-center g-4 position-relative" style="z-index: 1;">
                    <div class="col-lg-5">
                        <p class="eyebrow mb-2">Join Evergreen Team</p>
                        <h2 class="section-title mb-3">Bring your game. We will build the rest.</h2>
                        <p class="fs-5 mb-3">Open to players who want consistent training, real teamwork, and a clear cricket identity built around growth.</p>
                        <p class="mb-0 text-secondary">Send your player details here. We will save your request with pending status for admin review.</p>
                    </div>
                    <div class="col-lg-7">
                        <div class="form-panel p-4 p-md-4">
                            <?php if (session()->getFlashdata('join_success')): ?>
                                <div class="alert alert-success"><?= esc(session()->getFlashdata('join_success')) ?></div>
                            <?php endif; ?>

                            <?php $joinErrors = session()->getFlashdata('join_errors') ?? []; ?>

                            <form action="<?= site_url('/join-us') ?>" method="post">
                                <?= csrf_field() ?>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label fw-semibold">Player Name *</label>
                                        <input type="text" class="form-control<?= isset($joinErrors['name']) ? ' is-invalid' : '' ?>" id="name" name="name" value="<?= esc(old('name')) ?>">
                                        <?php if (isset($joinErrors['name'])): ?>
                                            <div class="invalid-feedback"><?= esc($joinErrors['name']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label fw-semibold">Email</label>
                                        <input type="email" class="form-control<?= isset($joinErrors['email']) ? ' is-invalid' : '' ?>" id="email" name="email" value="<?= esc(old('email')) ?>">
                                        <?php if (isset($joinErrors['email'])): ?>
                                            <div class="invalid-feedback"><?= esc($joinErrors['email']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label fw-semibold">Phone *</label>
                                        <input type="text" class="form-control<?= isset($joinErrors['phone']) ? ' is-invalid' : '' ?>" id="phone" name="phone" value="<?= esc(old('phone')) ?>">
                                        <?php if (isset($joinErrors['phone'])): ?>
                                            <div class="invalid-feedback"><?= esc($joinErrors['phone']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="address" class="form-label fw-semibold">Address *</label>
                                        <input type="text" class="form-control<?= isset($joinErrors['address']) ? ' is-invalid' : '' ?>" id="address" name="address" value="<?= esc(old('address')) ?>">
                                        <?php if (isset($joinErrors['address'])): ?>
                                            <div class="invalid-feedback"><?= esc($joinErrors['address']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-evergreen w-100 d-flex align-items-center justify-content-center py-2">Send Player Details</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer-strip py-3">
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <span class="fw-semibold">Evergreen Team</span>
            <span class="small">Copyright &copy; 2026 Evergreen Team. All rights reserved.</span>
        </div>
    </footer>
    <button
        type="button"
        class="back-to-top"
        id="backToTopButton"
        aria-label="Back to top"
        title="Back to top">
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 19V5" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M6.75 10.25L12 5l5.25 5.25" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    </button>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@latest/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (() => {
            const backToTopButton = document.getElementById('backToTopButton');

            if (!backToTopButton) {
                return;
            }

            const toggleBackToTop = () => {
                backToTopButton.classList.toggle('is-visible', window.scrollY > 320);
            };

            backToTopButton.addEventListener('click', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth',
                });
            });

            toggleBackToTop();
            window.addEventListener('scroll', toggleBackToTop, {
                passive: true
            });
        })();
    </script>
</body>

</html>