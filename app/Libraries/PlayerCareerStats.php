<?php

namespace App\Libraries;

class PlayerCareerStats
{
    public function build(array $player, array $battingRows, array $bowlingRows, array $matchPlayerRows, array $matchSummaryRows, array $matchRows = [], array $battingDetailRows = [], array $bowlingDetailRows = [], array $fieldingRows = [], array $wicketRows = []): array
    {
        $battingRows = array_values(array_filter($battingRows, static fn(array $row): bool => !empty($row)));
        $bowlingRows = array_values(array_filter($bowlingRows, static fn(array $row): bool => !empty($row)));

        $battingRuns = array_sum(array_map(static fn(array $row): int => (int) ($row['runs'] ?? 0), $battingRows));
        $battingBalls = array_sum(array_map(static fn(array $row): int => (int) ($row['balls'] ?? 0), $battingRows));
        $battingFours = array_sum(array_map(static fn(array $row): int => (int) ($row['fours'] ?? 0), $battingRows));
        $battingSixes = array_sum(array_map(static fn(array $row): int => (int) ($row['sixes'] ?? 0), $battingRows));
        $isNotOutMode = static fn(string $mode): bool => in_array($mode, ['', 'not_out', 'retired_hurt'], true);
        $notOuts = 0;
        $dismissals = array_filter(array_map(static fn(array $row): ?string => $isNotOutMode((string) ($row['dismissal_mode'] ?? '')) ? null : (string) $row['dismissal_mode'], $battingRows));
        foreach ($battingRows as $row) {
            if ($isNotOutMode((string) ($row['dismissal_mode'] ?? ''))) {
                $notOuts++;
            }
        }

        $battingAverage = count($dismissals) > 0 ? round($battingRuns / count($dismissals), 2) : null;

        $battingStrikeRate = $battingBalls > 0 ? round(($battingRuns * 100) / $battingBalls, 2) : 0.0;
        $highestScore = 0;
        foreach ($battingRows as $row) {
            $highestScore = max($highestScore, (int) ($row['runs'] ?? 0));
        }

        $wickets = array_sum(array_map(static fn(array $row): int => (int) ($row['wickets'] ?? 0), $bowlingRows));
        $bowlingRuns = array_sum(array_map(static fn(array $row): int => (int) ($row['runs'] ?? 0), $bowlingRows));
        $ballsBowled = 0;
        foreach ($bowlingRows as $row) {
            $rowOvers = (float) ($row['overs'] ?? 0.0);
            $fullOvers = (int) $rowOvers;
            $extraBalls = (int) round(($rowOvers - $fullOvers) * 10);
            $ballsBowled += $fullOvers * 6 + $extraBalls;
        }
        $overs = intdiv($ballsBowled, 6) . '.' . ($ballsBowled % 6);
        $bowlingAverage = $wickets > 0 ? round($bowlingRuns / $wickets, 2) : 0.0;
        $economy = $ballsBowled > 0 ? round($bowlingRuns / ($ballsBowled / 6), 2) : 0.0;
        $bestBowling = '0/0';
        if ($bowlingRows !== []) {
            $best = null;
            foreach ($bowlingRows as $row) {
                $rowWickets = (int) ($row['wickets'] ?? 0);
                $rowRuns = (int) ($row['runs'] ?? 0);
                if ($best === null || $rowWickets > $best['wickets'] || ($rowWickets === $best['wickets'] && $rowRuns < $best['runs'])) {
                    $best = ['wickets' => $rowWickets, 'runs' => $rowRuns];
                }
            }
            if ($best !== null) {
                $bestBowling = $best['wickets'] . '/' . $best['runs'];
            }
        }

        $scoreboardMatchIds = array_merge(
            array_map(static fn(array $row): int => (int) ($row['match_id'] ?? 0), $battingRows),
            array_map(static fn(array $row): int => (int) ($row['match_id'] ?? 0), $bowlingRows),
        );
        $matchesPlayed = count(array_unique(array_filter($scoreboardMatchIds)));
        $matchesContributed = count(array_unique(array_map(static fn(array $row): int => (int) ($row['match_id'] ?? 0), $matchSummaryRows)));

        $detailBattingRows = array_values(array_filter($battingDetailRows, static fn(array $row): bool => !empty($row)));
        if ($detailBattingRows === [] && $battingRows !== []) {
            $detailBattingRows = array_map(static fn(array $row): array => [
                'runs' => (int) ($row['runs'] ?? 0),
                'balls' => (int) ($row['balls'] ?? 0),
                'fours' => (int) ($row['fours'] ?? 0),
                'sixes' => (int) ($row['sixes'] ?? 0),
                'dismissal_mode' => (string) ($row['dismissal_mode'] ?? ''),
            ], $battingRows);
        }

        $detailBowlingRows = array_values(array_filter($bowlingDetailRows, static fn(array $row): bool => !empty($row)));
        if ($detailBowlingRows === [] && $bowlingRows !== []) {
            $detailBowlingRows = array_map(static fn(array $row): array => [
                'overs' => (float) ($row['overs'] ?? 0.0),
                'maidens' => (int) ($row['maidens'] ?? 0),
                'runs' => (int) ($row['runs'] ?? 0),
                'wickets' => (int) ($row['wickets'] ?? 0),
            ], $bowlingRows);
        }

        $battingSummaryByMatch = [];
        foreach ($detailBattingRows as $row) {
            $matchId = (int) ($row['match_id'] ?? 0);
            if ($matchId === 0) {
                continue;
            }
            $runs = (int) ($row['runs'] ?? 0);
            $balls = (int) ($row['balls'] ?? 0);
            $fours = (int) ($row['fours'] ?? 0);
            $sixes = (int) ($row['sixes'] ?? 0);
            $notOut = (string) ($row['dismissal_mode'] ?? '') === '';
            $summary = $runs . ($notOut ? '*' : '') . ' (' . $balls . 'b';
            if ($fours > 0) {
                $summary .= ', ' . $fours . 'x4';
            }
            if ($sixes > 0) {
                $summary .= ', ' . $sixes . 'x6';
            }
            $summary .= ')';
            $battingSummaryByMatch[$matchId] = $summary;
        }

        $bowlingSummaryByMatch = [];
        foreach ($detailBowlingRows as $row) {
            $matchId = (int) ($row['match_id'] ?? 0);
            if ($matchId === 0) {
                continue;
            }
            $rowWickets = (int) ($row['wickets'] ?? 0);
            $rowRuns = (int) ($row['runs'] ?? 0);
            $rowOvers = $row['overs'] ?? 0;
            $bowlingSummaryByMatch[$matchId] = $rowWickets . '/' . $rowRuns . ' (' . $rowOvers . ' ov)';
        }

        $matchRows = array_map(static function (array $row) use ($battingSummaryByMatch, $bowlingSummaryByMatch): array {
            $matchId = (int) ($row['match_id'] ?? 0);
            $row['batting_summary'] = $battingSummaryByMatch[$matchId] ?? null;
            $row['bowling_summary'] = $bowlingSummaryByMatch[$matchId] ?? null;
            return $row;
        }, $matchRows);

        $fieldingRows = array_values(array_filter($fieldingRows, static fn(array $row): bool => !empty($row)));
        $fieldingModeCounts = array_count_values(array_map(static fn(array $row): string => (string) ($row['dismissal_mode'] ?? ''), $fieldingRows));
        $catches = $fieldingModeCounts['caught'] ?? 0;
        $stumpings = $fieldingModeCounts['stumped'] ?? 0;
        $runOuts = $fieldingModeCounts['run_out'] ?? 0;

        return [
            'player' => $player,
            'matches_played' => $matchesPlayed,
            'matches_contributed' => $matchesContributed,
            'batting' => [
                'innings' => count($battingRows),
                'runs' => $battingRuns,
                'balls' => $battingBalls,
                'fours' => $battingFours,
                'sixes' => $battingSixes,
                'not_outs' => $notOuts,
                'dismissals' => count($dismissals),
                'average' => $battingAverage,
                'strike_rate' => $battingStrikeRate,
                'highest_score' => $highestScore,
                'dismissal_breakdown' => array_count_values($dismissals),
            ],
            'bowling' => [
                'innings' => count($bowlingRows),
                'overs' => $overs,
                'maidens' => array_sum(array_map(static fn(array $row): int => (int) ($row['maidens'] ?? 0), $bowlingRows)),
                'runs' => $bowlingRuns,
                'wickets' => $wickets,
                'average' => $bowlingAverage,
                'economy' => $economy,
                'best_bowling' => $bestBowling,
            ],
            'match_rows' => array_values(array_filter($matchRows, static fn(array $row): bool => !empty($row))),
            'batting_rows' => $detailBattingRows,
            'bowling_rows' => $detailBowlingRows,
            'fielding' => [
                'catches' => $catches,
                'stumpings' => $stumpings,
                'run_outs' => $runOuts,
                'dismissals' => count($fieldingRows),
            ],
            'fielding_rows' => $fieldingRows,
            'wicket_rows' => array_values(array_filter($wicketRows, static fn(array $row): bool => !empty($row))),
        ];
    }
}
