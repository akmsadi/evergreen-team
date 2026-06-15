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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@latest/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
    <style>
        .hero-slider {
            width: 100%;
            height: 600px;
        }

        .swiper-slide {
            position: relative;
            overflow: hidden;
        }

        .swiper-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .slide-content {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: rgba(0, 0, 0, .4);
            text-align: center;
            color: #fff;
            padding: 20px;
        }

        .slide-content h2 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 4rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .slide-content p {
            font-family: 'Poppins', sans-serif;
            font-size: 1.2rem;
        }

        .swiper-button-prev,
        .swiper-button-next {
            color: #fff;
        }

        .swiper-pagination-bullet-active {
            background: #28a745;
        }

        @media (max-width: 768px) {
            .hero-slider {
                height: 400px;
            }

            .slide-content h2 {
                font-size: 2.5rem;
            }

            .slide-content p {
                font-size: 1rem;
            }
        }

        .countdown-panel {
            position: relative;
            overflow: hidden;
        }

        .countdown-panel::before {
            content: "";
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;

            background: url('<?= base_url('assets/images/logo/challengers-logo.png') ?>') center/50% no-repeat;
            opacity: 0.08;
        }

        .countdown-panel>* {
            position: relative;
            z-index: 1;
        }

        .countdown-panel {
            border-radius: 8px;
            background: #fff;
            padding: 3rem 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .countdown-timer {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }

        .countdown-item {
            text-align: center;
            min-width: 60px;
        }

        .countdown-value {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 4rem;
            font-weight: 700;
            color: #1f7a3d;
            line-height: 1;
            margin-bottom: 0.35rem;
        }

        .countdown-label {
            font-family: 'Bebas Neue', sans-serif;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .countdown-sep {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 3rem;
            color: #1f7a3d;
            line-height: 1;
            align-self: flex-start;
            padding-top: 0.1rem;
        }

        @media (max-width: 768px) {
            .countdown-value {
                font-size: 2rem;
            }

            .countdown-sep {
                font-size: 1.75rem;
            }

            .countdown-item {
                min-width: 44px;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg py-3" style="border-bottom: 5px solid #1f7a3d;">
        <div class="container-fluid g-5">
            <a class="navbar-brand" href="#home">
                <img src="<?= base_url('assets/images/logo1.png') ?>" alt="Evergreen Team Logo" style="width: 180px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavMenu" aria-controls="mainNavMenu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="mainNavMenu">
                <div class="d-flex flex-column flex-lg-row gap-2 justify-content-end top-menu py-2 py-lg-0">
                    <a class="btn text-uppercase" href="#teams">Teams</a>
                    <a class="btn text-uppercase" href="#members">Members</a>
                    <a class="btn text-uppercase" href="#gallery">Gallery</a>
                    <a class="btn text-uppercase" href="#news">News</a>
                    <a class="btn text-uppercase" href="#join">Join Us</a>
                    <?php if (session()->get('is_admin')): ?>
                        <a class="btn text-uppercase" href="<?= site_url('/admin/dashboard') ?>">Dashboard</a>
                    <?php else: ?>
                        <a class="btn text-uppercase" href="<?= site_url('/admin/login') ?>">Admin</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <main id="home" class="pb-5 bg-body-secondary">
        <div class="swiper hero-slider">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <img class="w-100" src="<?= base_url('assets/images/slider/slider-image-1.jpg') ?>" alt="EverGreen Cricket Team">
                    <div class="slide-content">
                        <h2>EVERGREEN TEAM</h2>
                        <p>PASSION • PERFORMANCE • TEAMWORK</p>
                    </div>
                </div>

                <div class="swiper-slide">
                    <img class="w-100" src="<?= base_url('assets/images/slider/slider-image-2.jpg') ?>" alt="EverGreen Cricket Team">
                    <div class="slide-content">
                        <h2>MATCH DAY READY</h2>
                        <p>UNITED WE PLAY, UNITED WE WIN</p>
                    </div>
                </div>

                <div class="swiper-slide">
                    <img class="w-100" src="<?= base_url('assets/images/slider/slider-image-3.jpg') ?>" alt="EverGreen Cricket Team">
                    <div class="slide-content">
                        <h2>CHAMPIONS IN ACTION</h2>
                        <p>BUILDING A WINNING LEGACY</p>
                    </div>
                </div>

            </div>

            <div class="swiper-pagination"></div>

            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>

        <section class="container-fluid g-5">
            <div class="py-4 py-md-5">
                <div class="hero-grid position-relative" style="z-index: 1;">
                    <div>
                        <p class="eyebrow mb-3">Cricket. Commitment. Community.</p>
                        <h1 class="display-title mb-3">Play bold with<br><span class="accent-text">Evergreen Team</span></h1>
                        <p>We value smart cricket, high standards, and a team-first culture that keeps improving every season. Every session is designed to feel like match preparation, not just attendance.</p>
                        <div class="d-flex flex-wrap gap-3">
                            <a class="btn btn-outline-dark px-3" href="#join">Join the Squad</a>
                            <a class="btn btn-outline-dark px-3" href="#stories">Explore Stories</a>
                        </div>
                        <div class="mini-panel">
                            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start gap-4">
                                <div class="row g-3 align-self-end">
                                    <div class="col-6">
                                        <div class="stat-card p-3">
                                            <div class="stat-value"><?= esc((string) $playerCount) ?></div>
                                            <div class="small text-uppercase">Active Members</div>
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
                        </div>
                    </div>
                    <div class="countdown-panel text-center">
                        <?php if ($nextMatch): ?>
                            <div class="eyebrow mb-3">Next Match</div>
                            <h2 class="mb-2"><?= esc($nextMatch['team_name'] ?? 'Evergreen Team') ?> vs <?= esc($nextMatch['opponent_name'] ?? 'Opposition') ?></h2>
                            <p class="mb-1"><span id="nextMatchDate" data-ts="<?= strtotime($nextMatch['scheduled_at']) * 1000 ?>"><?= esc(date('M d, Y - g:i A', strtotime($nextMatch['scheduled_at']))) ?></span></p>
                            <p class="mb-3"><?= esc($nextMatch['venue'] ?? 'TBA') ?></p>
                            <div class="countdown-timer">
                                <div class="countdown-item">
                                    <div class="countdown-value" id="countdown-days">0</div>
                                    <div class="countdown-label">Days</div>
                                </div>
                                <div class="countdown-sep">:</div>
                                <div class="countdown-item">
                                    <div class="countdown-value" id="countdown-hours">0</div>
                                    <div class="countdown-label">Hrs</div>
                                </div>
                                <div class="countdown-sep">:</div>
                                <div class="countdown-item">
                                    <div class="countdown-value" id="countdown-mins">0</div>
                                    <div class="countdown-label">Min</div>
                                </div>
                                <div class="countdown-sep">:</div>
                                <div class="countdown-item">
                                    <div class="countdown-value" id="countdown-secs">0</div>
                                    <div class="countdown-label">Sec</div>
                                </div>
                            </div>
                        <?php elseif ($lastMatch): ?>
                            <div class="eyebrow mb-3">Last Match</div>
                            <h2 class="mb-2"><?= esc($lastMatch['team_name'] ?? 'Evergreen Team') ?> vs <?= esc($lastMatch['opponent_name'] ?? 'Opposition') ?></h2>
                            <p class="mb-1"><strong>Venue:</strong> <?= esc($lastMatch['venue'] ?? 'N/A') ?></p>
                            <p class="mb-1"><strong>Result:</strong> <?= esc($lastMatch['result_summary'] ?? 'Match result pending') ?></p>
                            <?php if (!empty($lastMatch['notes'])): ?>
                                <p class="mb-0"><?= esc($lastMatch['notes']) ?></p>
                            <?php endif; ?>
                        <?php else: ?>
                            <h3 class="h4 fw-bold mb-2">No upcoming matches</h3>
                            <p class="text-secondary mb-0">Stay tuned for the next scheduled fixture!</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <section id="teams" class="container-fluid g-5">
            <div class="py-4 py-md-5">
                <p class="eyebrow mb-3">Meet the teams</p>
                <h2 class="mb-3">From the nets to match day, these are the teams driving EverGreen forward.</h2>
                <div class="row ">
                    <div class="col-md-3">
                        <img src="<?= base_url('assets/images/teams/aliza.jpeg') ?>" alt="Aliza strike force" class="w-100 mb-3">
                        <p class="text-center">ALIZA strike force</p>
                    </div>
                    <div class="col-md-3">
                        <img src="<?= base_url('assets/images/teams/w-warriors.jpeg') ?>" alt="W-Warriors sports team" class="w-100 mb-3">
                        <p class="text-center">W-WARRIORS sports team</p>
                    </div>
                    <div class="col-md-3">
                        <img src="<?= base_url('assets/images/teams/v-bold.jpeg') ?>" alt="V-Bold" class="w-100 mb-3">
                        <p class="text-center">V-BOLD sports team</p>
                    </div>
                    <div class="col-md-3">
                        <img src="<?= base_url('assets/images/teams/w-avanta.jpeg') ?>" alt="Wichet wizards avanta" class="w-100 mb-3">
                        <p class="text-center">Wichet wizards AVANTA</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="stories" class="container-fluid g-5">
            <div class="py-4 py-md-5">
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
            <div class="hero-card py-4 py-md-5">
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

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script>
        new Swiper('.hero-slider', {
            loop: true,

            autoplay: {
                delay: 4000,
                disableOnInteraction: false
            },

            pagination: {
                el: '.swiper-pagination',
                clickable: true
            },

            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev'
            },

            effect: 'fade',
            fadeEffect: {
                crossFade: true
            },

            speed: 1000
        });
    </script>
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
    <script>
        (() => {
            // Initialize countdown timer for next match
            const daysEl = document.getElementById('countdown-days');
            const hoursEl = document.getElementById('countdown-hours');
            const minsEl = document.getElementById('countdown-mins');
            const secsEl = document.getElementById('countdown-secs');
            const nextMatchDateEl = document.getElementById('nextMatchDate');

            // Only run if countdown elements exist (next match is available)
            if (!daysEl || !nextMatchDateEl) {
                return;
            }

            const updateCountdown = () => {
                const nextMatchDate = new Date(parseInt(nextMatchDateEl.dataset.ts));
                const now = new Date();
                const difference = nextMatchDate - now;

                if (difference <= 0) {
                    // Match time has passed
                    daysEl.textContent = '0';
                    hoursEl.textContent = '0';
                    minsEl.textContent = '0';
                    secsEl.textContent = '0';
                    return;
                }

                const days = Math.floor(difference / (1000 * 60 * 60 * 24));
                const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const mins = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
                const secs = Math.floor((difference % (1000 * 60)) / 1000);

                const pad = n => String(n).padStart(2, '0');
                daysEl.textContent = pad(days);
                hoursEl.textContent = pad(hours);
                minsEl.textContent = pad(mins);
                secsEl.textContent = pad(secs);
            };

            // Update immediately and then every second
            updateCountdown();
            setInterval(updateCountdown, 1000);
        })();
    </script>
</body>

</html>