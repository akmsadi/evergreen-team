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

    <style {csp-style-nonce}>
        :root {
            --evergreen: #1f7a3d;
            --evergreen-deep: forestgreen;
            --evergreen-soft: #dff3e5;
            --pitch: #f5f0e6;
            --ink: #111111;
        }

        body {
            color: var(--ink);
            font-family: "Barlow", sans-serif;
            min-height: 100vh;
        }

        .navbar-brand {
            letter-spacing: 0.08em;
        }

        .hero-card,
        .info-card,
        .stat-card {
            backdrop-filter: blur(8px);
            background-color: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(31, 122, 61, 0.14);
            border-radius: 1.5rem;
            box-shadow: 0 18px 45px rgba(20, 83, 45, 0.08);
        }

        .hero-card {
            background-color: rgba(236, 246, 238, 0.98);
            overflow: hidden;
            position: relative;
        }

        .hero-card::after {
            content: none;
            inset: 0;
            pointer-events: none;
            position: absolute;
        }

        .eyebrow {
            color: var(--evergreen);
            font-size: 0.85rem;
            font-weight: 800;
            letter-spacing: 0.16em;
            text-transform: uppercase;
        }

        .display-title {
            font-size: clamp(2.8rem, 7vw, 5.5rem);
            font-weight: 800;
            line-height: 0.95;
        }

        .accent-text {
            color: var(--evergreen);
        }

        .btn-evergreen {
            background-color: var(--evergreen);
            border-color: var(--evergreen);
            color: #fff;
        }

        .btn-evergreen:hover,
        .btn-evergreen:focus {
            background-color: var(--evergreen-deep);
            border-color: var(--evergreen-deep);
            color: #fff;
        }

        .btn-outline-evergreen {
            border-color: var(--evergreen);
            color: var(--evergreen);
        }

        .btn-outline-evergreen:hover,
        .btn-outline-evergreen:focus {
            background-color: var(--evergreen-soft);
            border-color: var(--evergreen);
            color: var(--ink);
        }

        .pitch-mark {
            background-color: rgba(252, 251, 247, 0.98);
            border: 1px solid rgba(17, 17, 17, 0.08);
            border-radius: 1.5rem;
            min-height: 100%;
            position: relative;
        }

        .pitch-mark::before,
        .pitch-mark::after {
            background-color: rgba(31, 122, 61, 0.22);
            border-radius: 999px;
            content: "";
            left: 50%;
            position: absolute;
            transform: translateX(-50%);
            width: 72%;
        }

        .stat-value {
            color: var(--evergreen-deep);
            font-size: 2.2rem;
            font-weight: 800;
            line-height: 1;
        }

        .section-title {
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 700;
        }

        .info-card ul {
            margin-bottom: 0;
            padding-left: 1.1rem;
        }

        .footer-strip {
            background-color: var(--evergreen-deep);
            color: #fff;
        }

        .form-panel {
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(31, 122, 61, 0.12);
            border-radius: 1.25rem;
            box-shadow: 0 18px 40px rgba(20, 83, 45, 0.08);
        }

        .hero-grid {
            display: grid;
            gap: 1.25rem;
            grid-template-columns: minmax(0, 1.35fr) minmax(300px, 0.95fr);
        }

        .hero-side-stack {
            display: grid;
            gap: 1rem;
        }

        .mini-panel {
            background-color: rgba(244, 250, 245, 0.96);
            border: 1px solid rgba(31, 122, 61, 0.12);
            border-radius: 1.4rem;
            box-shadow: 0 16px 34px rgba(20, 83, 45, 0.07);
            padding: 1.2rem;
        }

        .mini-panel__value {
            color: var(--evergreen-deep);
            font-size: 1.85rem;
            font-weight: 800;
            line-height: 1;
        }

        .hero-badge-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: 1.5rem;
        }

        .hero-badge {
            background: rgba(31, 122, 61, 0.09);
            border: 1px solid rgba(31, 122, 61, 0.1);
            border-radius: 999px;
            color: var(--evergreen-deep);
            font-size: 0.85rem;
            font-weight: 700;
            padding: 0.65rem 1rem;
        }

        .section-shell {
            background-color: rgba(239, 248, 241, 0.96);
            border: 1px solid rgba(31, 122, 61, 0.12);
            border-radius: 1.75rem;
            box-shadow: 0 18px 44px rgba(20, 83, 45, 0.07);
            overflow: hidden;
            padding: 1.5rem;
        }

        .section-shell--dark {
            background-color: rgba(239, 248, 241, 0.96);
            border-color: rgba(31, 122, 61, 0.12);
            color: var(--ink);
        }

        .section-shell--dark .eyebrow,
        .section-shell--dark .text-secondary,
        .section-shell--dark .section-copy,
        .section-shell--dark .testimonial-role,
        .section-shell--dark .timeline-meta {
            color: #52606d !important;
        }

        .section-shell--dark .timeline-step {
            background: rgba(31, 122, 61, 0.12);
            border-color: rgba(31, 122, 61, 0.18);
            color: var(--evergreen-deep);
        }

        .section-copy {
            color: #52606d;
            font-size: 1.05rem;
            max-width: 42rem;
        }

        .feature-grid {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.86);
            border: 1px solid rgba(31, 122, 61, 0.08);
            border-radius: 1.25rem;
            padding: 1.2rem;
        }

        .feature-card__number {
            color: var(--evergreen-deep);
            display: inline-flex;
            font-size: 0.9rem;
            font-weight: 800;
            margin-bottom: 0.75rem;
        }

        .story-carousel .carousel-item {
            min-height: 100%;
        }

        .story-slide {
            background: linear-gradient(135deg, rgba(17, 17, 17, 0.18), rgba(17, 17, 17, 0.55)), linear-gradient(135deg, #2d8b57 0%, #123524 100%);
            border-radius: 1.5rem;
            min-height: 360px;
            overflow: hidden;
            padding: 1.75rem;
            position: relative;
        }

        .story-slide::before {
            background: radial-gradient(circle at 20% 20%, rgba(255, 255, 255, 0.18), transparent 28%);
            content: "";
            inset: 0;
            position: absolute;
        }

        .story-slide__content {
            display: flex;
            flex-direction: column;
            height: 100%;
            justify-content: flex-end;
            position: relative;
            z-index: 1;
        }

        .story-slide__tag {
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 999px;
            color: #fff;
            display: inline-flex;
            font-size: 0.8rem;
            font-weight: 700;
            margin-bottom: 1rem;
            padding: 0.45rem 0.9rem;
        }

        .story-slide__title {
            color: #fff;
            font-size: clamp(1.8rem, 4vw, 3rem);
            font-weight: 800;
            line-height: 1;
            margin-bottom: 0.8rem;
            max-width: 22rem;
        }

        .story-slide__copy {
            color: rgba(255, 255, 255, 0.84);
            font-size: 1.03rem;
            margin-bottom: 0;
            max-width: 28rem;
        }

        .gallery-grid {
            display: grid;
            gap: 1rem;
            grid-template-columns: 1.3fr 0.9fr 1.1fr;
            grid-template-rows: repeat(2, minmax(190px, 1fr));
        }

        .gallery-card {
            border-radius: 1.4rem;
            color: #fff;
            overflow: hidden;
            padding: 1.25rem;
            position: relative;
        }

        .gallery-card::after {
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.04), rgba(0, 0, 0, 0.48));
            content: "";
            inset: 0;
            position: absolute;
        }

        .gallery-card>* {
            position: relative;
            z-index: 1;
        }

        .gallery-card--wide {
            background: linear-gradient(135deg, #0f5132 0%, #1f7a3d 55%, #62b47f 100%);
            grid-column: span 1;
            grid-row: span 2;
        }

        .gallery-card--pitch {
            background: linear-gradient(135deg, #734b21 0%, #b67a32 58%, #edd298 100%);
        }

        .gallery-card--night {
            background: linear-gradient(135deg, #112d4e 0%, #205375 54%, #3282b8 100%);
        }

        .gallery-card--locker {
            background: linear-gradient(135deg, #263238 0%, #455a64 55%, #78909c 100%);
            grid-column: span 2;
        }

        .gallery-card__meta {
            color: rgba(255, 255, 255, 0.76);
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .gallery-card__title {
            font-size: 1.5rem;
            font-weight: 800;
            line-height: 1.05;
            margin-top: 0.5rem;
        }

        .gallery-card__copy {
            color: rgba(255, 255, 255, 0.82);
            margin-bottom: 0;
            max-width: 18rem;
        }

        .team-grid,
        .testimonial-grid,
        .timeline-grid {
            display: grid;
            gap: 1rem;
        }

        .team-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .team-card,
        .testimonial-card,
        .timeline-card {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(31, 122, 61, 0.1);
            border-radius: 1.35rem;
            padding: 1.25rem;
        }

        .team-avatar {
            align-items: center;
            background: linear-gradient(135deg, rgba(31, 122, 61, 0.18), rgba(31, 122, 61, 0.35));
            border-radius: 1rem;
            color: var(--evergreen-deep);
            display: inline-flex;
            font-size: 1.35rem;
            font-weight: 800;
            height: 3rem;
            justify-content: center;
            margin-bottom: 1rem;
            width: 3rem;
        }

        .team-role,
        .testimonial-role,
        .timeline-meta {
            color: #66737f;
            font-size: 0.92rem;
        }

        .team-pill-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .team-pill {
            background: rgba(31, 122, 61, 0.08);
            border-radius: 999px;
            color: var(--evergreen-deep);
            font-size: 0.78rem;
            font-weight: 700;
            padding: 0.45rem 0.75rem;
        }

        .testimonial-card {
            height: 100%;
            position: relative;
        }

        .testimonial-card::before {
            color: rgba(31, 122, 61, 0.16);
            content: "\201C";
            font-size: 4rem;
            font-weight: 800;
            line-height: 1;
            position: absolute;
            right: 1rem;
            top: 0.35rem;
        }

        .testimonial-card p {
            position: relative;
            z-index: 1;
        }

        .timeline-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .timeline-step {
            align-items: center;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 999px;
            color: #fff;
            display: inline-flex;
            font-size: 0.75rem;
            font-weight: 800;
            margin-bottom: 1rem;
            padding: 0.45rem 0.8rem;
        }

        .cta-strip {
            align-items: center;
            background: linear-gradient(135deg, rgba(31, 122, 61, 0.14), rgba(31, 122, 61, 0.04));
            border: 1px solid rgba(31, 122, 61, 0.1);
            border-radius: 1.5rem;
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: space-between;
            padding: 1.4rem 1.5rem;
        }

        main#home {
            padding-bottom: 7rem !important;
        }

        .back-to-top {
            align-items: center;
            background: linear-gradient(135deg, var(--evergreen) 0%, #2b8150 100%);
            border: 0;
            border-radius: 999px;
            bottom: 5rem;
            box-shadow: 0 10px 18px rgba(20, 83, 45, 0.22);
            color: #fff;
            cursor: pointer;
            display: inline-flex;
            height: 1.625rem;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            position: fixed;
            right: 1.5rem;
            transform: translateY(0.8rem);
            transition: opacity 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
            width: 1.625rem;
            z-index: 1030;
        }

        .back-to-top:hover,
        .back-to-top:focus-visible {
            background: linear-gradient(135deg, var(--evergreen-deep) 0%, #236c43 100%);
            box-shadow: 0 12px 20px rgba(20, 83, 45, 0.28);
            color: #fff;
            outline: none;
        }

        .back-to-top.is-visible {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0);
        }

        .back-to-top svg {
            height: 0.625rem;
            width: 0.625rem;
        }

        @media (max-width: 991.98px) {

            .hero-grid,
            .feature-grid,
            .gallery-grid,
            .team-grid,
            .timeline-grid {
                grid-template-columns: 1fr;
            }

            .gallery-grid {
                grid-template-rows: auto;
            }

            .gallery-card--wide,
            .gallery-card--locker {
                grid-column: auto;
                grid-row: auto;
            }
        }

        @media (max-width: 767.98px) {
            .story-slide {
                min-height: 300px;
            }

            .section-shell,
            .hero-card,
            .info-card,
            .form-panel {
                border-radius: 1.25rem;
            }

            .back-to-top {
                bottom: 4rem;
                height: 1.5rem;
                right: 1rem;
                width: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg py-3">
        <div class="container">
            <a class="navbar-brand fw-bold text-black" href="#home">EVERGREEN TEAM</a>
            <div class="d-flex gap-2 flex-wrap justify-content-end">
                <a class="btn btn-sm btn-outline-evergreen rounded-pill px-3" href="#about">About</a>
                <a class="btn btn-sm btn-outline-evergreen rounded-pill px-3" href="#stories">Stories</a>
                <a class="btn btn-sm btn-outline-evergreen rounded-pill px-3" href="#gallery">Gallery</a>
                <a class="btn btn-sm btn-outline-evergreen rounded-pill px-3" href="#team">Team</a>
                <a class="btn btn-sm btn-outline-evergreen rounded-pill px-3" href="#testimonials">Testimonials</a>
                <a class="btn btn-sm btn-evergreen rounded-pill px-3" href="#join">Join Us</a>
                <?php if (session()->get('is_admin')): ?>
                    <a class="btn btn-sm btn-outline-evergreen rounded-pill px-3" href="<?= site_url('/admin/dashboard') ?>">Dashboard</a>
                <?php else: ?>
                    <a class="btn btn-sm btn-outline-evergreen rounded-pill px-3" href="<?= site_url('/admin/login') ?>">Admin Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <main id="home" class="pb-5">
        <section class="container py-4 py-lg-5">
            <div class="hero-card p-4 p-md-5">
                <div class="hero-grid position-relative" style="z-index: 1;">
                    <div>
                        <p class="eyebrow mb-3">Cricket. Commitment. Community.</p>
                        <h1 class="display-title mb-3">Play bold with <span class="accent-text">Evergreen Team</span></h1>
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

        <section id="about" class="container py-4 py-lg-5">
            <div class="section-shell">
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3 mb-4">
                    <div>
                        <p class="eyebrow mb-2">Team Identity</p>
                        <h2 class="section-title mb-2">Evergreen means consistent, prepared, and hard to break.</h2>
                        <p class="section-copy mb-0">We value smart cricket, high standards, and a team-first culture that keeps improving every season. Every session is designed to feel like match preparation, not just attendance.</p>
                    </div>
                    <a class="btn btn-outline-evergreen rounded-pill px-4" href="#team">Meet the Core Group</a>
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

        <section id="stories" class="container py-4 py-lg-5">
            <div class="section-shell--dark section-shell p-4 p-md-5">
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3 mb-4">
                    <div>
                        <p class="eyebrow mb-2">Club Stories</p>
                        <h2 class="section-title mb-2">A landing page with motion, not just blocks.</h2>
                        <p class="section-copy mb-0">The carousel gives the homepage a more dynamic heartbeat and lets you tell three different versions of the club in the first scroll.</p>
                    </div>
                </div>
                <div id="evergreenStoryCarousel" class="carousel slide story-carousel" data-bs-ride="carousel">
                    <div class="carousel-indicators position-static mt-0 mb-4 justify-content-start">
                        <button type="button" data-bs-target="#evergreenStoryCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                        <button type="button" data-bs-target="#evergreenStoryCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                        <button type="button" data-bs-target="#evergreenStoryCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                    </div>
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
                </div>
            </div>
        </section>

        <section id="gallery" class="container py-4 py-lg-5">
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

        <section id="team" class="container py-4 py-lg-5">
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

        <section id="fixtures" class="container py-4 py-lg-5">
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

        <section id="testimonials" class="container py-4 py-lg-5">
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

        <section id="join" class="container py-4 py-lg-5">
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