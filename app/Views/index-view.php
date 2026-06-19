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
        <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
        <style>
            .hero-slider {
                width: 100%;
                height: 600px;
            }

            .hero-slider .swiper-slide {
                position: relative;
                overflow: hidden;
            }

            .hero-slider .swiper-slide img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }

            .hero-slider .slide-content {
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

            .hero-slider .slide-content h2 {
                font-family: 'Bebas Neue', sans-serif;
                font-size: 4rem;
                letter-spacing: 2px;
                text-transform: uppercase;
                margin-bottom: 10px;
            }

            .hero-slider .slide-content p {
                font-family: 'Poppins', sans-serif;
                font-size: 1.2rem;
            }

            .hero-slider .swiper-button-prev,
            .hero-slider .swiper-button-next {
                color: #fff;
            }

            .hero-slider .swiper-pagination-bullet-active {
                background: #28a745;
            }

            @media (max-width: 768px) {
                .hero-slider {
                    height: 400px;
                }

                .hero-slider .slide-content h2 {
                    font-size: 2.5rem;
                }

                .hero-slider .slide-content p {
                    font-size: 1rem;
                }
            }

            /** Team slider */
            .team-slider-container {
                max-width: 1600px;
                margin: 0 auto;
                position: relative;
                padding: 0 15px;
                box-sizing: border-box;
            }

            .team-slider-container .swiper {
                width: 100%;
                padding-top: 40px;
                padding-bottom: 40px;
            }

            /* BASE STYLE: Mobile First Card Dimensions */
            .team-slider-container .swiper-slide {
                background-position: center;
                background-size: cover;
                width: 260px;
                /* Slimmer width for mobile screens */
                height: 380px;
                /* Shorter height for mobile screens */
                border-radius: 16px;
                overflow: hidden;
                box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
                position: relative;
                transition: transform 0.5s ease, opacity 0.5s ease;
                opacity: 0.35;
            }

            /* TABLET RESPONSIVENESS */
            @media (min-width: 768px) {
                .team-slider-container .swiper-slide {
                    width: 340px;
                    height: 480px;
                    border-radius: 20px;
                }
            }

            /* DESKTOP / WIDESCREEN RESPONSIVENESS (Up to 1600px) */
            @media (min-width: 1200px) {
                .team-slider-container .swiper-slide {
                    width: 420px;
                    height: 580px;
                    border-radius: 24px;
                }
            }

            .team-slider-container .swiper-slide-active {
                opacity: 1;
            }

            .team-slider-container .card-content {
                width: 100%;
                height: 100%;
                background: linear-gradient(to top, rgba(0, 0, 0, 0.85), transparent 60%);
                display: flex;
                flex-direction: column;
                justify-content: flex-end;
                padding: 20px;
                box-sizing: border-box;
                color: white;
            }

            /* Responsive typography */
            @media (min-width: 768px) {
                .team-slider-container .card-content {
                    padding: 30px;
                }
            }

            .team-slider-container .card-content h3 {
                margin: 0 0 8px 0;
                font-size: 1.2rem;
                text-transform: uppercase;
                letter-spacing: 1px;
                font-weight: 700;
            }

            @media (min-width: 768px) {
                .team-slider-container .card-content h3 {
                    font-size: 1.6rem;
                }
            }

            .team-slider-container .card-content p {
                margin: 0;
                font-size: 0.9rem;
                opacity: 0.8;
            }

            @media (min-width: 768px) {
                .team-slider-container .card-content p {
                    font-size: 1.05rem;
                }
            }

            .team-slider-container .swiper-button-next,
            .team-slider-container .swiper-button-prev {
                color: #fff;
                transform: scale(0.9);
            }

            @media (min-width: 768px) {

                .team-slider-container .swiper-button-next,
                .team-slider-container .swiper-button-prev {
                    transform: scale(1.3);
                }
            }
        </style>
    </head>

    <body>
        <nav class="navbar navbar-expand-lg py-3" style="border-bottom: 5px solid #1f7a3d;">
            <div class="container-fluid g-5">
                <div class="content-div d-flex w-100">
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
                            <a class="btn text-uppercase" href="#join">Join</a>
                            <?php if (session()->get('is_admin')): ?>
                                <a class="btn text-uppercase" href="<?= site_url('/admin/dashboard') ?>">Dashboard</a>
                            <?php else: ?>
                                <a class="btn text-uppercase" href="<?= site_url('/admin/login') ?>">Admin</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <main id="home" class="bg-body-secondary">
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
                <div class="content-div py-4 py-md-5">
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

            <section id="teams" class="container-fluid g-5 bg-body-tertiary">
                <div class="content-div py-4 py-md-5">
                    <h2 class="section-title mb-2">Meet the teams</h2>
                    <p class="section-copy mb-3">From the nets to match day, these are the teams driving EverGreen forward.</p>
                    <div class="row">
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

            <section id="members" class="container-fluid g-5">
                <div class="content-div py-4 py-md-5 team-slider-container">
                    <h2 class="section-title mb-2">Team members</h2>
                    <p class="section-copy mb-3">Meet the individuals who make EverGreen Cricket Club stronger every day.</p>
                    <div class="swiper mySwiper">
                        <div class="swiper-wrapper">
                            <?php foreach ($players as $player): ?>
                                <div class="swiper-slide" style="background-image: url('<?= base_url('assets/images/empty-male.png') ?>');">
                                    <div class="card-content">
                                        <p>Player</p>
                                        <h3><?= $player['name'] ?></h3>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <!-- Navigation Arrows -->
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                    </div>
                </div>
            </section>

            <section id="gallery" class="container-fluid g-5 bg-body-tertiary">
                <div class="content-div py-4 py-md-5">
                    <h2 class="section-title mb-2">Event gallery</h2>
                    <p class="section-copy mb-3">A visual showcase of our matches, tournaments, celebrations, and team spirit.</p>

                    <?php if (!empty($events)): ?>
                        <div class="row g-0 event-gallery-shell">
                            <div class="col-lg-9 order-2 order-lg-1 event-images-pane">
                                <div class="event-images-grid" id="eventImagesGrid"></div>
                                <p class="text-secondary mt-3" id="eventNoImages" style="display:none;">No images uploaded for this event yet.</p>
                            </div>

                            <div class="col-lg-3 order-1 order-lg-2 event-list-pane ps-lg-4 mb-4 mb-lg-0">
                                <p class="event-list-heading">Events</p>
                                <div class="event-list" id="eventList">
                                    <?php foreach ($events as $i => $event): ?>
                                        <button class="event-list-item<?= $i === 0 ? ' active' : '' ?>" data-event-index="<?= $i ?>" type="button">
                                            <span class="event-list-title"><?= esc($event['title']) ?></span>
                                            <span class="event-list-meta"><?= count($event['images']) ?> photo<?= count($event['images']) !== 1 ? 's' : '' ?></span>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="text-secondary">No events have been added yet.</p>
                    <?php endif; ?>
                </div>
            </section>

            <section id="join" class="container-fluid g-5">
                <div class="content-div py-4 py-md-5">
                    <div class="row g-4 position-relative" style="z-index: 1;">
                        <div class="col-lg-6">
                            <p class="eyebrow mb-2">Join Evergreen Team</p>
                            <h2 class="section-title mb-3">Bring your game. We will build the rest.</h2>
                            <p class="mb-0">Open to players who want consistent training, real teamwork, and a clear cricket identity built around growth.</p>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-panel p-4 p-md-4">
                                <?php if (session()->getFlashdata('join_success')): ?>
                                    <div class="alert alert-success"><?= esc(session()->getFlashdata('join_success')) ?></div>
                                <?php endif; ?>

                                <?php $joinErrors = session()->getFlashdata('join_errors') ?? []; ?>

                                <p class="mb-3 text-secondary">Send your player details here. We will save your request with pending status for admin review.</p>
                                <form action="<?= site_url('/join-us') ?>" method="post">
                                    <?= csrf_field() ?>

                                    <div class="mb-4">
                                        <label for="name" class="form-label">Name *</label>
                                        <input type="text" class="form-control<?= isset($joinErrors['name']) ? ' is-invalid' : '' ?>" id="name" name="name" value="<?= esc(old('name')) ?>">
                                        <?php if (isset($joinErrors['name'])): ?>
                                            <div class="invalid-feedback"><?= esc($joinErrors['name']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="mb-4 d-none">
                                        <label for="email" class="form-label fw-semibold">Email</label>
                                        <input type="email" class="form-control<?= isset($joinErrors['email']) ? ' is-invalid' : '' ?>" id="email" name="email" value="<?= esc(old('email')) ?>">
                                        <?php if (isset($joinErrors['email'])): ?>
                                            <div class="invalid-feedback"><?= esc($joinErrors['email']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="mb-4">
                                        <label for="phone" class="form-label fw-semibold">Phone *</label>
                                        <input type="text" class="form-control<?= isset($joinErrors['phone']) ? ' is-invalid' : '' ?>" id="phone" name="phone" value="<?= esc(old('phone')) ?>">
                                        <?php if (isset($joinErrors['phone'])): ?>
                                            <div class="invalid-feedback"><?= esc($joinErrors['phone']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="mb-4">
                                        <label for="address" class="form-label fw-semibold">Address *</label>
                                        <input type="text" class="form-control<?= isset($joinErrors['address']) ? ' is-invalid' : '' ?>" id="address" name="address" value="<?= esc(old('address')) ?>">
                                        <?php if (isset($joinErrors['address'])): ?>
                                            <div class="invalid-feedback"><?= esc($joinErrors['address']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <button type="submit" class="btn btn-evergreen w-100 d-flex align-items-center justify-content-center py-2">Send Player Details</button>

                                </form>
                            </div>
                        </div>
                    </div>
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

        <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
        <script>
            var eventsGalleryData = <?= json_encode(array_map(function ($e) {
                return [
                    'id' => $e['id'],
                    'title' => $e['title'],
                    'images' => array_map(function ($img) {
                        return [
                            'src' => base_url($img['image_path']),
                            'caption' => $img['caption'] ?? '',
                        ];
                    }, $e['images']),
                ];
            }, $events), JSON_HEX_TAG | JSON_HEX_AMP) ?>;
        </script>
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

            const swiper = new Swiper(".mySwiper", {
                effect: "coverflow",
                grabCursor: true,
                centeredSlides: true,
                slidesPerView: "auto",
                loop: true,

                // Responsive 3D parameter adjustments
                breakpoints: {
                    // Mobile / default settings (under 768px wide)
                    320: {
                        coverflowEffect: {
                            rotate: 0,
                            stretch: -15,     // Looser pull for smaller cards
                            depth: 100,       // Less depth so side cards stay visible on mobile layout
                            modifier: 1,
                            slideShadows: false,
                        }
                    },
                    // Tablet layouts (768px and up)
                    768: {
                        coverflowEffect: {
                            rotate: 0,
                            stretch: -30,
                            depth: 130,
                            modifier: 1.1,
                            slideShadows: false,
                        }
                    },
                    // Widescreen layouts (1200px and up)
                    1200: {
                        coverflowEffect: {
                            rotate: 0,
                            stretch: -40,     // Tight stacking for large 420px cards
                            depth: 160,       // Full 3D push back
                            modifier: 1.2,
                            slideShadows: false,
                        }
                    }
                },

                navigation: {
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev",
                },
            });

        </script>
        <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
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

                const pad = n => String(n).padStart(2, '0');

                const updateCountdown = () => {
                    const nextMatchDate = new Date(parseInt(nextMatchDateEl.dataset.ts));
                    const now = new Date();
                    const difference = nextMatchDate - now;

                    if (difference <= 0) {
                        daysEl.textContent = '00';
                        hoursEl.textContent = '00';
                        minsEl.textContent = '00';
                        secsEl.textContent = '00';
                        return;
                    }

                    daysEl.textContent = pad(Math.floor(difference / (1000 * 60 * 60 * 24)));
                    hoursEl.textContent = pad(Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)));
                    minsEl.textContent = pad(Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60)));
                    secsEl.textContent = pad(Math.floor((difference % (1000 * 60)) / 1000));
                };

                updateCountdown();
                setInterval(updateCountdown, 1000);
            })();
        </script>
        <script>
            (() => {
                const events = window.eventsGalleryData;
                if (!Array.isArray(events) || !events.length) return;

                const grid = document.getElementById('eventImagesGrid');
                const noImages = document.getElementById('eventNoImages');
                const listItems = document.querySelectorAll('.event-list-item');
                let lightbox = null;

                function renderEvent(index) {
                    const event = events[index];
                    grid.innerHTML = '';

                    if (!event.images.length) {
                        noImages.style.display = '';
                        if (lightbox) { lightbox.destroy(); lightbox = null; }
                        return;
                    }

                    noImages.style.display = 'none';

                    event.images.forEach((img) => {
                        const a = document.createElement('a');
                        a.href = img.src;
                        a.className = 'event-gallery-link glightbox';
                        a.dataset.gallery = 'event-' + event.id;
                        a.dataset.description = img.caption || '';
                        a.dataset.title = img.caption || '';

                        const el = document.createElement('img');
                        el.src = img.src;
                        el.alt = img.caption || event.title;
                        el.loading = 'lazy';

                        a.appendChild(el);
                        grid.appendChild(a);
                    });

                    if (lightbox) lightbox.destroy();
                    lightbox = GLightbox({
                        selector: '.event-gallery-link',
                        loop: true,
                        touchNavigation: true,
                    });
                }

                listItems.forEach((btn, i) => {
                    btn.addEventListener('click', () => {
                        listItems.forEach(b => b.classList.remove('active'));
                        btn.classList.add('active');
                        renderEvent(i);
                    });
                });

                renderEvent(0);
            })();
        </script>
    </body>

</html>