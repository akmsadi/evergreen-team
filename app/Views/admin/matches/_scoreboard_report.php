<?php if (($scoreboard['innings'] ?? []) !== []): ?>
    <style>
        .report-hero {
            background:
                radial-gradient(circle at top right, rgba(255, 255, 255, 0.2), transparent 36%),
                linear-gradient(135deg, #15394c 0%, #1f5d75 56%, #5ca0b8 100%);
            border: 1px solid rgba(21, 57, 76, 0.12);
            border-radius: 1.4rem;
            box-shadow: 0 18px 42px rgba(21, 57, 76, 0.14);
            color: #f7fcff;
            padding: 1.35rem 1.5rem;
        }

        .report-hero__title {
            color: #fff;
            font-size: clamp(1.35rem, 2.4vw, 1.9rem);
            margin-bottom: 0.4rem;
        }

        .report-hero__copy {
            color: rgba(247, 252, 255, 0.78);
            max-width: 42rem;
        }

        .report-hero__result {
            align-self: flex-start;
            background: rgba(255, 255, 255, 0.16);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 999px;
            color: #fff;
            font-weight: 700;
            padding: 0.7rem 1rem;
        }

        .innings-summary {
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.02), rgba(15, 23, 42, 0.06));
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 1.2rem;
            padding: 1rem;
        }

        .innings-summary__eyebrow {
            color: #4b5563;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .innings-summary__title {
            font-size: 1.2rem;
            margin-bottom: 0.35rem;
        }

        .innings-summary__meta {
            color: #6b7280;
        }

        .innings-summary__chips {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .innings-summary__chip {
            background: #fff;
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 1rem;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
            min-width: 122px;
            padding: 0.8rem 0.95rem;
        }

        .innings-summary__chip--dark {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border-color: rgba(15, 23, 42, 0.3);
        }

        .innings-summary__chip--success {
            background: linear-gradient(135deg, #14532d 0%, #15803d 100%);
            border-color: rgba(20, 83, 45, 0.3);
        }

        .innings-summary__chip--warning {
            background: linear-gradient(135deg, #854d0e 0%, #ca8a04 100%);
            border-color: rgba(133, 77, 14, 0.3);
        }

        .innings-summary__chip--dark .innings-summary__chip-label,
        .innings-summary__chip--dark .innings-summary__chip-value,
        .innings-summary__chip--success .innings-summary__chip-label,
        .innings-summary__chip--success .innings-summary__chip-value,
        .innings-summary__chip--warning .innings-summary__chip-label,
        .innings-summary__chip--warning .innings-summary__chip-value {
            color: #fff;
        }

        .innings-summary__chip-label {
            color: #6b7280;
            display: block;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            margin-bottom: 0.25rem;
            text-transform: uppercase;
        }

        .innings-summary__chip-value {
            color: #111827;
            display: block;
            font-size: 1rem;
            font-weight: 700;
            line-height: 1.2;
        }
    </style>
    <div class="card-surface">
        <div class="report-hero d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
            <div>
                <h2 class="report-hero__title fw-bold">Match Report</h2>
                <p class="report-hero__copy mb-0">Innings summary, batting cards, bowling cards, and recent deliveries for this match.</p>
            </div>
            <?php if (! empty($match['result_summary'])): ?>
                <div class="report-hero__result"><?= esc($match['result_summary']) ?></div>
            <?php endif; ?>
        </div>

        <div class="row g-4">
            <?php foreach ($scoreboard['innings'] as $innings): ?>
                <?php
                $extrasParts = [];
                $byes = (int) ($innings['byes'] ?? 0);
                $legByes = (int) ($innings['leg_byes'] ?? 0);
                $wides = (int) ($innings['wides'] ?? 0);
                $noBalls = (int) ($innings['no_balls'] ?? 0);
                $extrasTotal = (int) ($innings['extras'] ?? 0);

                if ($byes > 0) {
                    $extrasParts[] = 'b ' . $byes;
                }

                if ($legByes > 0) {
                    $extrasParts[] = 'lb ' . $legByes;
                }

                if ($wides > 0) {
                    $extrasParts[] = 'wd ' . $wides;
                }

                if ($noBalls > 0) {
                    $extrasParts[] = 'nb ' . $noBalls;
                }
                ?>
                <div class="col-12">
                    <div class="contributor-group h-100">
                        <div class="innings-summary d-flex flex-column flex-lg-row justify-content-between align-items-lg-start gap-3 mb-3">
                            <div>
                                <div class="innings-summary__eyebrow mb-1">Innings <?= esc((string) ($innings['innings_number'] ?? '')) ?></div>
                                <h3 class="innings-summary__title h5 fw-bold"><?= esc($innings['batting_label']) ?> scored <?= esc($innings['score_text']) ?></h3>
                                <p class="innings-summary__meta mb-0">Overs <?= esc($innings['overs_text']) ?> · Bowling side <?= esc($innings['bowling_label']) ?></p>
                                <p class="innings-summary__meta small mb-0">Extras <?= esc((string) $extrasTotal) ?><?= $extrasParts === [] ? '' : ' (' . esc(implode(', ', $extrasParts)) . ')' ?></p>
                            </div>
                            <div class="innings-summary__chips">
                                <div class="innings-summary__chip innings-summary__chip--dark">
                                    <span class="innings-summary__chip-label">Score</span>
                                    <span class="innings-summary__chip-value"><?= esc($innings['score_text']) ?></span>
                                </div>
                                <div class="innings-summary__chip">
                                    <span class="innings-summary__chip-label">Overs</span>
                                    <span class="innings-summary__chip-value"><?= esc($innings['overs_text']) ?></span>
                                </div>
                                <div class="innings-summary__chip">
                                    <span class="innings-summary__chip-label">Extras</span>
                                    <span class="innings-summary__chip-value"><?= esc((string) $extrasTotal) ?></span>
                                </div>
                                <?php if ($innings['target_runs'] !== null): ?>
                                    <div class="innings-summary__chip">
                                        <span class="innings-summary__chip-label">Target</span>
                                        <span class="innings-summary__chip-value"><?= esc((string) $innings['target_runs']) ?></span>
                                    </div>
                                <?php endif; ?>
                                <div class="innings-summary__chip <?= (int) ($innings['completed'] ?? 0) === 1 ? 'innings-summary__chip--success' : 'innings-summary__chip--warning' ?>">
                                    <span class="innings-summary__chip-label">Status</span>
                                    <span class="innings-summary__chip-value"><?= (int) ($innings['completed'] ?? 0) === 1 ? 'Completed' : 'Open' ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-lg-5">
                                <h4 class="h6 fw-bold mb-3">Batting Card</h4>
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Batter</th>
                                                <th>R</th>
                                                <th>B</th>
                                                <th>4s</th>
                                                <th>6s</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($innings['batting_card'] as $batter): ?>
                                                <tr>
                                                    <td><?= esc($batter['name']) ?></td>
                                                    <td><?= esc((string) $batter['runs']) ?></td>
                                                    <td><?= esc((string) $batter['balls']) ?></td>
                                                    <td><?= esc((string) $batter['fours']) ?></td>
                                                    <td><?= esc((string) $batter['sixes']) ?></td>
                                                    <td class="small"><?= esc($batter['dismissal']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <h4 class="h6 fw-bold mb-3">Bowling Card</h4>
                                <?php if ($innings['bowling_card'] === []): ?>
                                    <p class="text-secondary small mb-0">No bowling figures recorded.</p>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Bowler</th>
                                                    <th>O</th>
                                                    <th>R</th>
                                                    <th>W</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($innings['bowling_card'] as $bowler): ?>
                                                    <tr>
                                                        <td><?= esc($bowler['name']) ?></td>
                                                        <td><?= esc($bowler['overs']) ?></td>
                                                        <td><?= esc((string) $bowler['runs']) ?></td>
                                                        <td><?= esc((string) $bowler['wickets']) ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-lg-4">
                                <h4 class="h6 fw-bold mb-3">Recent Balls</h4>
                                <?php if ($innings['recent_balls'] === []): ?>
                                    <p class="text-secondary small mb-0">No deliveries logged yet.</p>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Ball</th>
                                                    <th>Detail</th>
                                                    <th>Score</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($innings['recent_balls'] as $ball): ?>
                                                    <?php $displayBallCode = max(0, ((int) ($ball['over_number'] ?? 1)) - 1) . '.' . (int) ($ball['ball_in_over'] ?? 1); ?>
                                                    <tr>
                                                        <td>
                                                            <div class="fw-semibold"><?= esc($displayBallCode) ?></div>
                                                            <div class="small text-secondary"><?= esc((string) ($ball['bowler_name'] ?? '')) ?></div>
                                                        </td>
                                                        <td>
                                                            <div><?= esc((string) ($ball['striker_name'] ?? '')) ?> scored <?= esc((string) ($ball['runs_bat'] ?? 0)) ?></div>
                                                            <?php if (! empty($ball['extra_type'])): ?>
                                                                <div class="small text-secondary">Extras: <?= esc((string) ($ball['extras'] ?? 0)) ?> <?= esc(str_replace('_', ' ', (string) $ball['extra_type'])) ?></div>
                                                            <?php endif; ?>
                                                            <?php if ((int) ($ball['is_wicket'] ?? 0) === 1): ?>
                                                                <div class="small text-danger">Wicket: <?= esc(str_replace('_', ' ', (string) ($ball['wicket_type'] ?? 'out'))) ?><?= ! empty($ball['dismissed_name']) ? ' - ' . esc((string) $ball['dismissed_name']) : '' ?></div>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?= esc((string) ($ball['score_after_ball'] ?? '')) ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>