<?= $this->extend('admin/default') ?>

<?= $this->section('title') ?>Start Match<?= $this->endSection() ?>

<?= $this->section('admin_nav') ?>matches<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .wizard-step[hidden] {
        display: none !important;
    }

    .wizard-progress {
        display: grid;
        gap: 0.75rem;
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .step-tile {
        background: rgba(25, 135, 84, 0.08);
        border: 1px solid rgba(25, 135, 84, 0.18);
        border-radius: 1rem;
        padding: 1rem;
    }

    .step-tile.active {
        background: rgba(25, 135, 84, 0.16);
        border-color: rgba(25, 135, 84, 0.35);
    }

    .wizard-summary {
        background: rgba(15, 23, 42, 0.03);
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 1rem;
        padding: 1rem;
    }

    .scoreboard-hero {
        background:
            radial-gradient(circle at top right, rgba(255, 255, 255, 0.28), transparent 34%),
            linear-gradient(140deg, #123524 0%, #1f6f4a 58%, #4aa36f 100%);
        border: 1px solid rgba(18, 53, 36, 0.16);
        border-radius: 1.5rem;
        box-shadow: 0 24px 50px rgba(18, 53, 36, 0.18);
        color: #f7fff9;
        padding: 1.5rem;
    }

    .scoreboard-hero__eyebrow {
        color: rgba(247, 255, 249, 0.72);
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .scoreboard-hero__title {
        color: #fff;
        font-size: clamp(1.6rem, 3vw, 2.2rem);
        line-height: 1.1;
        margin: 0.35rem 0 0.9rem;
    }

    .scoreboard-hero__chips {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .scoreboard-hero__chip {
        background: rgba(255, 255, 255, 0.14);
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 1rem;
        min-width: 120px;
        padding: 0.9rem 1rem;
    }

    .scoreboard-hero__chip-label {
        color: rgba(247, 255, 249, 0.72);
        display: block;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        margin-bottom: 0.3rem;
        text-transform: uppercase;
    }

    .scoreboard-hero__chip-value {
        color: #fff;
        display: block;
        font-size: 1.1rem;
        font-weight: 700;
        line-height: 1.2;
    }

    .scoreboard-hero__meta {
        background: rgba(8, 22, 15, 0.24);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 1.25rem;
        min-width: min(100%, 260px);
        padding: 1rem 1.1rem;
    }

    .scoreboard-hero__meta-label {
        color: rgba(247, 255, 249, 0.72);
        display: block;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        margin-bottom: 0.15rem;
        text-transform: uppercase;
    }

    .scoreboard-hero__meta-value {
        color: #fff;
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 0.6rem;
    }

    .scoreboard-hero__meta-value:last-child {
        margin-bottom: 0;
    }

    .score-entry-meta {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
        gap: 0.75rem;
    }

    .score-meta-tile {
        background: #fff;
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 1rem;
        padding: 0.85rem 1rem;
    }

    .score-meta-tile .label {
        color: #6c757d;
        display: block;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        margin-bottom: 0.3rem;
        text-transform: uppercase;
    }

    .score-outcome-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .score-outcome-grid .btn {
        min-width: 3.25rem;
    }

    .score-outcome-grid .btn.active {
        box-shadow: inset 0 0 0 2px rgba(255, 255, 255, 0.25);
    }

    .score-entry-advanced summary {
        color: #198754;
        cursor: pointer;
        font-weight: 600;
        list-style: none;
    }

    .score-entry-advanced summary::-webkit-details-marker {
        display: none;
    }

    .wizard-toast-stack {
        position: fixed;
        right: 1.5rem;
        bottom: 1.5rem;
        z-index: 1080;
        display: grid;
        gap: 0.75rem;
        width: min(360px, calc(100vw - 2rem));
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php $startErrors = session()->getFlashdata('start_errors') ?? []; ?>
<?php $hasPlayerErrors = isset($startErrors['striker_player_id']) || isset($startErrors['non_striker_player_id']) || isset($startErrors['bowler_player_id']); ?>
<?php $startCompleted = ! empty($startCompleted); ?>
<?php $initialWizardStep = (int) ($initialWizardStep ?? ($startCompleted ? 3 : ($hasPlayerErrors ? 2 : 1))); ?>
<?php
$initialTossWinnerSide = (string) old('toss_winner_side');

if ($initialTossWinnerSide === '') {
    $storedTossWinner = (string) ($match['toss_winner'] ?? '');

    $initialTossWinnerSide = match ($storedTossWinner) {
        'team_a', (string) ($match['team_name'] ?? '') => 'team_a',
        'team_b', (string) ($match['opponent_name'] ?? '') => 'team_b',
        default => '',
    };
}

$initialTossDecision = (string) old('toss_decision', (string) ($match['toss_decision'] ?? ''));
if (! in_array($initialTossDecision, ['bat', 'bowl'], true)) {
    $initialTossDecision = '';
}

$tossWinnerLabel = match ((string) ($match['toss_winner'] ?? '')) {
    'team_a' => (string) ($match['team_name'] ?? 'Team A'),
    'team_b' => (string) ($match['opponent_name'] ?? 'Team B'),
    default => trim((string) ($match['toss_winner'] ?? '')) !== '' ? (string) $match['toss_winner'] : '-',
};
$tossDecisionLabel = match ((string) ($match['toss_decision'] ?? '')) {
    'bat' => 'Bat first',
    'bowl' => 'Bowl first',
    default => '-',
};
$playersBySide = [
    'team_a' => [],
    'team_b' => [],
];
foreach ($participants as $participant) {
    $playersBySide[(string) $participant['side']][] = [
        'id' => (int) $participant['player_id'],
        'name' => (string) $participant['name'],
    ];
}
?>

<div class="panel">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-6">
        <div>
            <h1 class="h2 fw-bold mb-2">Start <?= esc($match['team_name']) ?> vs <?= esc($match['opponent_name']) ?></h1>
            <p class="text-secondary mb-0">Set the toss result, choose the opening players, then score the innings from the same wizard without reloading the page.</p>
        </div>
        <a href="<?= site_url('/admin/matches/' . $match['id']) ?>" class="btn btn-light border">Back to Match</a>
    </div>

    <div class="card-surface mb-6">
        <h2 class="h4 fw-bold mb-3">Match Details</h2>
        <div class="row g-3">
            <div class="col-md-4 col-xl-2">
                <div class="border rounded-3 h-100 p-3">
                    <div class="text-secondary small text-uppercase mb-1">Type</div>
                    <div class="fw-semibold"><?= esc(ucwords(str_replace('_', ' ', (string) ($match['match_type'] ?? '-')))) ?></div>
                </div>
            </div>
            <div class="col-md-4 col-xl-2">
                <div class="border rounded-3 h-100 p-3">
                    <div class="text-secondary small text-uppercase mb-1">Overs</div>
                    <div class="fw-semibold"><?= esc((string) ($match['format_overs'] ?? '-')) ?></div>
                </div>
            </div>
            <div class="col-md-4 col-xl-2">
                <div class="border rounded-3 h-100 p-3">
                    <div class="text-secondary small text-uppercase mb-1">Venue</div>
                    <div class="fw-semibold"><?= esc($match['venue'] ?: '-') ?></div>
                </div>
            </div>
            <div class="col-md-4 col-xl-2">
                <div class="border rounded-3 h-100 p-3">
                    <div class="text-secondary small text-uppercase mb-1">Scheduled</div>
                    <div class="fw-semibold"><?= esc(getFormattedDate($match['scheduled_at'] ?? null, '-')) ?></div>
                </div>
            </div>
            <div class="col-md-4 col-xl-2">
                <div class="border rounded-3 h-100 p-3">
                    <div class="text-secondary small text-uppercase mb-1">Toss Winner</div>
                    <div class="fw-semibold" data-match-toss-winner><?= esc($tossWinnerLabel) ?></div>
                </div>
            </div>
            <div class="col-md-4 col-xl-2">
                <div class="border rounded-3 h-100 p-3">
                    <div class="text-secondary small text-uppercase mb-1">Decision</div>
                    <div class="fw-semibold" data-match-toss-decision><?= esc($tossDecisionLabel) ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="wizard-progress mb-6">
        <div class="step-tile<?= $initialWizardStep === 1 ? ' active' : '' ?>" data-step-indicator="1">
            <div class="fw-bold">Toss Setup</div>
            <div class="small text-secondary">Winner and decision</div>
        </div>
        <div class="step-tile<?= $initialWizardStep === 2 ? ' active' : '' ?>" data-step-indicator="2">
            <div class="fw-bold">Opening Players</div>
            <div class="small text-secondary">Batters and first bowler</div>
        </div>
        <div class="step-tile<?= $initialWizardStep === 3 ? ' active' : '' ?>" data-step-indicator="3">
            <div class="fw-bold">Live Scoreboard</div>
            <div class="small text-secondary">Score ball by ball</div>
        </div>
    </div>

    <div data-start-match-wizard>
        <form action="<?= $formAction ?>" method="post" data-start-match-form data-initial-step="<?= esc((string) $initialWizardStep) ?>">
            <?= csrf_field() ?>

            <section class="wizard-step" data-wizard-step="1" <?= $initialWizardStep === 1 ? '' : 'hidden' ?>>
                <div class="row g-4">
                    <div class="col-lg-7">
                        <div class="card-surface">
                            <h2 class="h4 fw-bold mb-3">Toss Result</h2>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="toss_winner_side" class="form-label fw-semibold">Toss Winner</label>
                                    <select class="form-select<?= isset($startErrors['toss_winner_side']) ? ' is-invalid' : '' ?>" id="toss_winner_side" name="toss_winner_side" data-toss-winner>
                                        <option value="">Select toss winner</option>
                                        <?php foreach ($sideLabels as $sideKey => $sideLabel): ?>
                                            <option value="<?= esc($sideKey) ?>" <?= $initialTossWinnerSide === $sideKey ? 'selected' : '' ?>><?= esc($sideLabel) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($startErrors['toss_winner_side'])): ?>
                                        <div class="invalid-feedback"><?= esc($startErrors['toss_winner_side']) ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <label for="toss_decision" class="form-label fw-semibold">Toss Decision</label>
                                    <select class="form-select<?= isset($startErrors['toss_decision']) ? ' is-invalid' : '' ?>" id="toss_decision" name="toss_decision" data-toss-decision>
                                        <option value="">Select decision</option>
                                        <option value="bat" <?= $initialTossDecision === 'bat' ? 'selected' : '' ?>>Bat first</option>
                                        <option value="bowl" <?= $initialTossDecision === 'bowl' ? 'selected' : '' ?>>Bowl first</option>
                                    </select>
                                    <?php if (isset($startErrors['toss_decision'])): ?>
                                        <div class="invalid-feedback"><?= esc($startErrors['toss_decision']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="wizard-summary h-100">
                            <div class="small text-uppercase text-secondary fw-semibold mb-2">Preview</div>
                            <p class="mb-2"><strong>Batting first:</strong> <span data-batting-side-label>Choose toss details</span></p>
                            <p class="mb-0"><strong>Bowling first:</strong> <span data-bowling-side-label>Choose toss details</span></p>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="button" class="btn btn-primary" data-wizard-next>Next: Opening Players</button>
                </div>
            </section>

            <section class="wizard-step" data-wizard-step="2" <?= $initialWizardStep === 2 ? '' : 'hidden' ?>>
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="card-surface">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h2 class="h4 fw-bold mb-0">Opening Selections</h2>
                                <button type="button" class="btn btn-light border btn-sm" data-wizard-back>Back</button>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="striker_player_id" class="form-label fw-semibold">Opening Batter</label>
                                    <select class="form-select<?= isset($startErrors['striker_player_id']) ? ' is-invalid' : '' ?>" id="striker_player_id" name="striker_player_id" data-opening-striker>
                                        <option value="">Select opening batter</option>
                                    </select>
                                    <?php if (isset($startErrors['striker_player_id'])): ?>
                                        <div class="invalid-feedback"><?= esc($startErrors['striker_player_id']) ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <label for="non_striker_player_id" class="form-label fw-semibold">Non-striker</label>
                                    <select class="form-select<?= isset($startErrors['non_striker_player_id']) ? ' is-invalid' : '' ?>" id="non_striker_player_id" name="non_striker_player_id" data-opening-non-striker>
                                        <option value="">Select non-striker</option>
                                    </select>
                                    <?php if (isset($startErrors['non_striker_player_id'])): ?>
                                        <div class="invalid-feedback"><?= esc($startErrors['non_striker_player_id']) ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <label for="bowler_player_id" class="form-label fw-semibold">Opening Bowler</label>
                                    <select class="form-select<?= isset($startErrors['bowler_player_id']) ? ' is-invalid' : '' ?>" id="bowler_player_id" name="bowler_player_id" data-opening-bowler>
                                        <option value="">Select opening bowler</option>
                                    </select>
                                    <?php if (isset($startErrors['bowler_player_id'])): ?>
                                        <div class="invalid-feedback"><?= esc($startErrors['bowler_player_id']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="wizard-summary h-100">
                            <div class="small text-uppercase text-secondary fw-semibold mb-2">First Innings</div>
                            <p class="mb-2"><strong>Batting:</strong> <span data-batting-side-label>Choose toss details</span></p>
                            <p class="mb-3"><strong>Bowling:</strong> <span data-bowling-side-label>Choose toss details</span></p>
                            <p class="small text-secondary mb-0">Submitting this step creates innings 1 with AJAX, then opens step 3 with your selected starters prefilled.</p>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary" data-start-submit><?= $startCompleted ? 'Refresh Scoreboard' : 'Start Scoreboard' ?></button>
                </div>
            </section>

        </form>

        <section class="wizard-step" data-wizard-step="3" <?= $initialWizardStep === 3 ? '' : 'hidden' ?>>
            <div data-scoreboard-container>
                <?= $wizardScoreboardHtml ?>
            </div>
        </section>
    </div>
</div>

<div
    class="wizard-toast-stack"
    data-wizard-alerts
    data-initial-success="<?= esc((string) (session()->getFlashdata('success') ?? '')) ?>"
    data-initial-error="<?= esc((string) (session()->getFlashdata('error') ?? '')) ?>"></div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var wizard = document.querySelector('[data-start-match-wizard]');
        var form = wizard ? wizard.querySelector('[data-start-match-form]') : null;

        if (!form) {
            return;
        }

        var sideLabels = <?= json_encode($sideLabels, JSON_THROW_ON_ERROR) ?>;
        var playersBySide = <?= json_encode($playersBySide, JSON_THROW_ON_ERROR) ?>;
        var tossWinnerInput = form.querySelector('[data-toss-winner]');
        var tossDecisionInput = form.querySelector('[data-toss-decision]');
        var strikerInput = form.querySelector('[data-opening-striker]');
        var nonStrikerInput = form.querySelector('[data-opening-non-striker]');
        var bowlerInput = form.querySelector('[data-opening-bowler]');
        var stepOne = wizard.querySelector('[data-wizard-step="1"]');
        var stepTwo = wizard.querySelector('[data-wizard-step="2"]');
        var stepThree = wizard.querySelector('[data-wizard-step="3"]');
        var nextButton = form.querySelector('[data-wizard-next]');
        var backButton = form.querySelector('[data-wizard-back]');
        var submitButton = form.querySelector('[data-start-submit]');
        var alertsContainer = document.querySelector('[data-wizard-alerts]');
        var scoreboardContainer = wizard.querySelector('[data-scoreboard-container]');
        var stepIndicators = wizard.closest('.panel').querySelectorAll('[data-step-indicator]');
        var currentStep = Number(form.dataset.initialStep || 1);
        var oldSelections = {
            striker: <?= json_encode((string) old('striker_player_id'), JSON_THROW_ON_ERROR) ?>,
            nonStriker: <?= json_encode((string) old('non_striker_player_id'), JSON_THROW_ON_ERROR) ?>,
            bowler: <?= json_encode((string) old('bowler_player_id'), JSON_THROW_ON_ERROR) ?>,
        };

        function setSubmitting(button, submitting, label) {
            if (!button) {
                return;
            }

            if (submitting) {
                button.dataset.originalLabel = button.textContent;
                button.textContent = label;
            } else if (button.dataset.originalLabel) {
                button.textContent = button.dataset.originalLabel;
            }

            button.disabled = submitting;
        }

        function renderAlert(type, message) {
            if (!alertsContainer) {
                return;
            }

            if (!message) {
                alertsContainer.innerHTML = '';
                return;
            }

            var toastElement = document.createElement('div');
            toastElement.className = 'toast align-items-center border-0 text-bg-' + type;
            toastElement.setAttribute('role', 'alert');
            toastElement.setAttribute('aria-live', 'assertive');
            toastElement.setAttribute('aria-atomic', 'true');
            toastElement.innerHTML = '' +
                '<div class="d-flex">' +
                '  <div class="toast-body"></div>' +
                '  <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>' +
                '</div>';
            toastElement.querySelector('.toast-body').textContent = message;
            alertsContainer.appendChild(toastElement);

            var toast = new bootstrap.Toast(toastElement, {
                autohide: true,
                delay: type === 'success' ? 2500 : 4000,
            });

            toastElement.addEventListener('hidden.bs.toast', function() {
                toastElement.remove();
            });

            toast.show();
        }

        function updateCsrf(payload) {
            if (!payload.csrfToken || !payload.csrfHash) {
                return;
            }

            document.querySelectorAll('input[name="' + payload.csrfToken + '"]').forEach(function(input) {
                input.value = payload.csrfHash;
            });
        }

        function setFieldError(fieldName, message) {
            var field = form.querySelector('[name="' + fieldName + '"]');

            if (!field) {
                return;
            }

            field.classList.toggle('is-invalid', Boolean(message));

            var feedback = field.parentElement.querySelector('.invalid-feedback');
            if (feedback) {
                feedback.textContent = message || '';
            }
        }

        function clearStartErrors() {
            ['toss_winner_side', 'toss_decision', 'striker_player_id', 'non_striker_player_id', 'bowler_player_id'].forEach(function(fieldName) {
                setFieldError(fieldName, '');
            });
        }

        function applyStartErrors(errors) {
            clearStartErrors();

            Object.keys(errors || {}).forEach(function(fieldName) {
                setFieldError(fieldName, errors[fieldName]);
            });
        }

        function postForm(url, formData) {
            return fetch(url, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            }).then(function(response) {
                return response.json().then(function(payload) {
                    payload.httpStatus = response.status;
                    return payload;
                });
            });
        }

        function getSides() {
            var tossWinner = tossWinnerInput.value;
            var tossDecision = tossDecisionInput.value;

            if (!tossWinner || !tossDecision) {
                return {
                    batting: '',
                    bowling: ''
                };
            }

            var oppositeSide = tossWinner === 'team_a' ? 'team_b' : 'team_a';

            return tossDecision === 'bat' ? {
                batting: tossWinner,
                bowling: oppositeSide
            } : {
                batting: oppositeSide,
                bowling: tossWinner
            };
        }

        function syncSummary() {
            var sides = getSides();
            var battingLabel = sideLabels[sides.batting] || 'Choose toss details';
            var bowlingLabel = sideLabels[sides.bowling] || 'Choose toss details';
            var tossWinnerLabel = tossWinnerInput.value ?
                (sideLabels[tossWinnerInput.value] || 'Choose toss details') :
                '-';
            var tossDecisionLabel = tossDecisionInput.value === 'bat' ?
                'Bat first' :
                (tossDecisionInput.value === 'bowl' ? 'Bowl first' : '-');

            document.querySelectorAll('[data-batting-side-label]').forEach(function(node) {
                node.textContent = battingLabel;
            });

            document.querySelectorAll('[data-bowling-side-label]').forEach(function(node) {
                node.textContent = bowlingLabel;
            });

            document.querySelectorAll('[data-match-toss-winner]').forEach(function(node) {
                node.textContent = tossWinnerLabel;
            });

            document.querySelectorAll('[data-match-toss-decision]').forEach(function(node) {
                node.textContent = tossDecisionLabel;
            });
        }

        function replaceOptions(select, players, placeholder, selectedValue) {
            select.innerHTML = '';
            select.append(new Option(placeholder, ''));

            players.forEach(function(player) {
                select.append(new Option(player.name, String(player.id), false, String(player.id) === selectedValue));
            });

            if (!players.some(function(player) {
                    return String(player.id) === selectedValue;
                })) {
                select.value = '';
            }
        }

        function syncPlayerOptions() {
            var sides = getSides();
            var battingPlayers = playersBySide[sides.batting] || [];
            var bowlingPlayers = playersBySide[sides.bowling] || [];

            replaceOptions(strikerInput, battingPlayers, 'Select opening batter', strikerInput.value || oldSelections.striker);
            replaceOptions(nonStrikerInput, battingPlayers, 'Select non-striker', nonStrikerInput.value || oldSelections.nonStriker);
            replaceOptions(bowlerInput, bowlingPlayers, 'Select opening bowler', bowlerInput.value || oldSelections.bowler);

            oldSelections.striker = '';
            oldSelections.nonStriker = '';
            oldSelections.bowler = '';
        }

        function showStep(stepNumber) {
            stepOne.hidden = stepNumber !== 1;
            stepTwo.hidden = stepNumber !== 2;
            stepThree.hidden = stepNumber !== 3;
            currentStep = stepNumber;

            stepIndicators.forEach(function(indicator) {
                indicator.classList.toggle('active', indicator.dataset.stepIndicator === String(stepNumber));
            });
        }

        function initBallEntryForms(root) {
            root.querySelectorAll('[data-wizard-ball-form]').forEach(function(ballForm) {
                if (ballForm.dataset.initialized === '1') {
                    return;
                }

                ballForm.dataset.initialized = '1';

                var runsInput = ballForm.querySelector('[data-field="runs_bat"]');
                var extrasInput = ballForm.querySelector('[data-field="extras"]');
                var extraTypeInput = ballForm.querySelector('[data-field="extra_type"]');
                var strikerField = ballForm.querySelector('[data-field="striker_player_id"]');
                var nonStrikerField = ballForm.querySelector('[name="non_striker_player_id"]');
                var wicketToggle = ballForm.querySelector('[data-field="is_wicket"]');
                var wicketTypeInput = ballForm.querySelector('[data-field="wicket_type"]');
                var dismissedInput = ballForm.querySelector('[data-field="dismissed_player_id"]');
                var wicketFieldGroups = ballForm.querySelectorAll('[data-wicket-fields]');
                var outcomeButtons = ballForm.querySelectorAll('[data-outcome-button]');
                var swapBattersButton = ballForm.querySelector('[data-swap-batters]');
                var editBallIdInput = ballForm.querySelector('[data-edit-ball-id]');
                var editModeNote = ballForm.querySelector('[data-edit-mode-note]');
                var cancelEditButton = ballForm.querySelector('[data-cancel-edit-ball]');
                var submitButton = ballForm.querySelector('button[type="submit"]');

                function syncEditState() {
                    var editing = Boolean(editBallIdInput && editBallIdInput.value);

                    if (submitButton) {
                        submitButton.textContent = editing ? 'Update Last Delivery' : 'Save Delivery';
                    }

                    if (editModeNote) {
                        editModeNote.classList.toggle('d-none', !editing);
                    }

                    if (cancelEditButton) {
                        cancelEditButton.classList.toggle('d-none', !editing);
                    }
                }

                function syncWicketState() {
                    var enabled = wicketToggle.checked;

                    wicketFieldGroups.forEach(function(group) {
                        group.hidden = !enabled;
                        group.querySelectorAll('select').forEach(function(field) {
                            field.disabled = !enabled;
                        });
                    });

                    if (!enabled) {
                        wicketTypeInput.value = '';
                        dismissedInput.value = '';
                    } else if (!dismissedInput.value) {
                        dismissedInput.value = strikerField.value || ballForm.dataset.defaultDismissed || '';
                    }
                }

                function setActiveButton(activeButton) {
                    outcomeButtons.forEach(function(button) {
                        button.classList.toggle('active', button === activeButton);
                    });
                }

                function ensureSelectOption(select, value, label) {
                    if (!select || !value) {
                        return;
                    }

                    var hasOption = Array.from(select.options).some(function(option) {
                        return option.value === value;
                    });

                    if (!hasOption) {
                        select.append(new Option(label || value, value, false, false));
                    }
                }

                outcomeButtons.forEach(function(button) {
                    button.addEventListener('click', function() {
                        runsInput.value = button.dataset.runsBat || '0';
                        extrasInput.value = button.dataset.extras || '0';
                        extraTypeInput.value = button.dataset.extraType || '';
                        wicketToggle.checked = button.dataset.wicket === '1';

                        if (wicketToggle.checked && !wicketTypeInput.value) {
                            wicketTypeInput.value = 'bowled';
                        }

                        if (wicketToggle.checked) {
                            dismissedInput.value = strikerField.value || ballForm.dataset.defaultDismissed || '';
                        }

                        syncWicketState();
                        setActiveButton(button);
                    });
                });

                strikerField.addEventListener('change', function() {
                    if (wicketToggle.checked) {
                        dismissedInput.value = strikerField.value || '';
                    }
                });

                wicketToggle.addEventListener('change', function() {
                    setActiveButton(null);
                    syncWicketState();
                });

                if (swapBattersButton && strikerField && nonStrikerField) {
                    swapBattersButton.addEventListener('click', function() {
                        var strikerValue = strikerField.value;
                        strikerField.value = nonStrikerField.value;
                        nonStrikerField.value = strikerValue;

                        if (wicketToggle.checked) {
                            dismissedInput.value = strikerField.value || '';
                        }
                    });
                }

                root.querySelectorAll('[data-edit-last-ball]').forEach(function(button) {
                    button.addEventListener('click', function() {
                        if (editBallIdInput) {
                            editBallIdInput.value = button.dataset.ballId || '';
                        }

                        strikerField.value = button.dataset.strikerPlayerId || '';
                        nonStrikerField.value = button.dataset.nonStrikerPlayerId || '';

                        var bowlerField = ballForm.querySelector('[name="bowler_player_id"]');
                        if (bowlerField) {
                            ensureSelectOption(bowlerField, button.dataset.bowlerPlayerId || '', button.dataset.bowlerName || '');
                            bowlerField.value = button.dataset.bowlerPlayerId || '';
                        }

                        runsInput.value = button.dataset.runsBat || '0';
                        extrasInput.value = button.dataset.extras || '0';
                        extraTypeInput.value = button.dataset.extraType || '';
                        wicketToggle.checked = button.dataset.isWicket === '1';
                        wicketTypeInput.value = button.dataset.wicketType || '';
                        dismissedInput.value = button.dataset.dismissedPlayerId || '';

                        var fielderInput = ballForm.querySelector('[name="fielder_player_id"]');
                        if (fielderInput) {
                            fielderInput.value = button.dataset.fielderPlayerId || '';
                        }

                        var commentaryInput = ballForm.querySelector('[name="commentary"]');
                        if (commentaryInput) {
                            commentaryInput.value = button.dataset.commentary || '';
                        }

                        setActiveButton(null);
                        syncWicketState();
                        syncEditState();
                        ballForm.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    });
                });

                if (cancelEditButton) {
                    cancelEditButton.addEventListener('click', function() {
                        window.location.reload();
                    });
                }

                syncEditState();

                ballForm.addEventListener('submit', function(event) {
                    event.preventDefault();
                    setSubmitting(ballForm.querySelector('button[type="submit"]'), true, 'Saving...');
                    renderAlert('', '');

                    postForm(ballForm.action, new FormData(ballForm)).then(function(payload) {
                        updateCsrf(payload);

                        if (!payload.success) {
                            renderAlert('danger', 'Fix the highlighted scoring fields and try again.');
                        } else {
                            renderAlert('success', payload.message || 'Ball added to the scoreboard.');
                        }

                        if (payload.redirectUrl) {
                            window.location.assign(payload.redirectUrl);
                            return;
                        }

                        if (typeof payload.scoreboardHtml === 'string') {
                            scoreboardContainer.innerHTML = payload.scoreboardHtml;
                            initBallEntryForms(scoreboardContainer);
                        }
                    }).catch(function() {
                        renderAlert('danger', 'Unable to update the scoreboard right now.');
                    }).finally(function() {
                        setSubmitting(ballForm.querySelector('button[type="submit"]'), false, 'Saving...');
                    });
                });

                syncWicketState();
            });
        }

        nextButton.addEventListener('click', function() {
            if (!tossWinnerInput.value || !tossDecisionInput.value) {
                tossWinnerInput.reportValidity();
                tossDecisionInput.reportValidity();
                return;
            }

            syncSummary();
            syncPlayerOptions();
            showStep(2);
        });

        backButton.addEventListener('click', function() {
            showStep(1);
        });

        form.addEventListener('submit', function(event) {
            if (currentStep !== 2) {
                return;
            }

            event.preventDefault();
            clearStartErrors();
            renderAlert('', '');
            setSubmitting(submitButton, true, 'Starting...');

            postForm(form.action, new FormData(form)).then(function(payload) {
                updateCsrf(payload);

                if (!payload.success) {
                    applyStartErrors(payload.errors || {});
                    showStep(Number(payload.step || 1));
                    renderAlert('danger', 'Fix the highlighted fields and try again.');
                    return;
                }

                scoreboardContainer.innerHTML = payload.scoreboardHtml || '';
                initBallEntryForms(scoreboardContainer);
                showStep(3);
                renderAlert('success', payload.message || 'Match started.');
            }).catch(function() {
                renderAlert('danger', 'Unable to start the scoreboard right now.');
            }).finally(function() {
                setSubmitting(submitButton, false, 'Starting...');
            });
        });

        tossWinnerInput.addEventListener('change', function() {
            syncSummary();
            syncPlayerOptions();
        });

        tossDecisionInput.addEventListener('change', function() {
            syncSummary();
            syncPlayerOptions();
        });

        syncSummary();
        syncPlayerOptions();
        initBallEntryForms(scoreboardContainer);
        showStep(currentStep);

        if (alertsContainer) {
            if (alertsContainer.dataset.initialSuccess) {
                renderAlert('success', alertsContainer.dataset.initialSuccess);
            }

            if (alertsContainer.dataset.initialError) {
                renderAlert('danger', alertsContainer.dataset.initialError);
            }
        }
    });
</script>
<?= $this->endSection() ?>