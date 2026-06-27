<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\MatchModel;

class Scoreboard extends BaseController
{
    private MatchModel $matches;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);
        helper(['url', 'scoreboard']);
        $this->matches = new MatchModel();
    }

    public function view(string $hash): string
    {
        $matchId = scoreboard_decode($hash);
        if ($matchId === null) {
            throw PageNotFoundException::forPageNotFound('Match not found.');
        }

        $match = $this->matches->findWithVenue($matchId);
        if ($match === null) {
            throw PageNotFoundException::forPageNotFound('Match not found.');
        }

        $matchNumber = $this->matches
            ->where('scheduled_at <', $match['scheduled_at'])
            ->countAllResults() + 1;

        $tossWinner   = (string) ($match['toss_winner'] ?? '');
        $tossDecision = (string) ($match['toss_decision'] ?? '');

        if ($tossWinner === 'team_a' && $tossDecision === 'bat') {
            $inns1BattingSide = 'team_a';
        } elseif ($tossWinner === 'team_a' && $tossDecision === 'bowl') {
            $inns1BattingSide = 'team_b';
        } elseif ($tossWinner === 'team_b' && $tossDecision === 'bat') {
            $inns1BattingSide = 'team_b';
        } elseif ($tossWinner === 'team_b' && $tossDecision === 'bowl') {
            $inns1BattingSide = 'team_a';
        } else {
            $inns1BattingSide = 'team_a';
        }

        $db = db_connect();

        $battingRows = $db->table('match_summary_batsmen msb')
            ->select('msb.*, p.name AS player_name, p2.name AS dismissal_by_name, p3.name AS fielder_name')
            ->join('players p', 'p.id = msb.player_id')
            ->join('players p2', 'p2.id = msb.dismissal_by', 'left')
            ->join('players p3', 'p3.id = msb.fielder_id', 'left')
            ->where('msb.match_id', $matchId)
            ->orderBy('msb.innings_number', 'ASC')
            ->orderBy('msb.sort_order', 'ASC')
            ->orderBy('msb.id', 'ASC')
            ->get()->getResultArray();

        $battingByInnings = [];
        foreach ($battingRows as $row) {
            $battingByInnings[(int) $row['innings_number']][] = $row;
        }

        $bowlingRows = $db->table('match_summary_bowlers msb')
            ->select('msb.*, p.name AS player_name')
            ->join('players p', 'p.id = msb.player_id')
            ->where('msb.match_id', $matchId)
            ->orderBy('msb.innings_number', 'ASC')
            ->orderBy('msb.sort_order', 'ASC')
            ->orderBy('msb.id', 'ASC')
            ->get()->getResultArray();

        $bowlingByInnings = [];
        foreach ($bowlingRows as $row) {
            // Skip bowlers who haven't delivered any overs
            if ($row['overs'] === null || (float) $row['overs'] <= 0.0) {
                continue;
            }
            $bowlingByInnings[(int) $row['innings_number']][] = $row;
        }

        $wicketRows = $db->table('match_summary_wickets msw')
            ->select('msw.*, p.name AS player_name')
            ->join('players p', 'p.id = msw.player_id', 'left')
            ->where('msw.match_id', $matchId)
            ->orderBy('msw.innings_number', 'ASC')
            ->orderBy('msw.id', 'ASC')
            ->get()->getResultArray();

        $wicketsByInnings = [];
        foreach ($wicketRows as $row) {
            $wicketsByInnings[(int) $row['innings_number']][] = $row;
        }

        $extrasRows = $db->table('match_summary_extras')
            ->where('match_id', $matchId)
            ->get()->getResultArray();

        $extrasByInnings = [];
        foreach ($extrasRows as $row) {
            $extrasByInnings[(int) $row['innings_number']] = $row;
        }

        $squadRows = $db->table('match_players mp')
            ->select('mp.player_id, mp.side, p.name AS player_name')
            ->join('players p', 'p.id = mp.player_id')
            ->where('mp.match_id', $matchId)
            ->orderBy('mp.batting_position', 'ASC')
            ->get()->getResultArray();

        $squadBySide = ['team_a' => [], 'team_b' => []];
        foreach ($squadRows as $row) {
            $side = $row['side'];
            if (array_key_exists($side, $squadBySide)) {
                $squadBySide[$side][] = [
                    'player_id'   => (int) $row['player_id'],
                    'player_name' => $row['player_name'],
                ];
            }
        }

        $inns1BowlingSide = $inns1BattingSide === 'team_a' ? 'team_b' : 'team_a';
        $innsSides = [
            1 => ['bat' => $inns1BattingSide,  'bowl' => $inns1BowlingSide],
            2 => ['bat' => $inns1BowlingSide,   'bowl' => $inns1BattingSide],
        ];

        $didNotBatByInnings  = [];
        $didNotBowlByInnings = [];

        foreach ([1, 2] as $inns) {
            $batSide  = $innsSides[$inns]['bat'];
            $bowlSide = $innsSides[$inns]['bowl'];

            $battedIds = array_map(
                static fn($r) => (int) $r['player_id'],
                $battingByInnings[$inns] ?? []
            );
            $bowledIds = array_map(
                static fn($r) => (int) $r['player_id'],
                $bowlingByInnings[$inns] ?? []
            );

            $didNotBatByInnings[$inns] = array_values(array_filter(
                $squadBySide[$batSide],
                static fn($p) => !in_array($p['player_id'], $battedIds, true)
            ));

            $didNotBowlByInnings[$inns] = array_values(array_filter(
                $squadBySide[$bowlSide],
                static fn($p) => !in_array($p['player_id'], $bowledIds, true)
            ));
        }

        return view('public/scoreboard', [
            'match'               => $match,
            'matchNumber'         => $matchNumber,
            'inns1BattingSide'    => $inns1BattingSide,
            'battingByInnings'    => $battingByInnings,
            'bowlingByInnings'    => $bowlingByInnings,
            'wicketsByInnings'    => $wicketsByInnings,
            'extrasByInnings'     => $extrasByInnings,
            'squadBySide'         => $squadBySide,
            'didNotBatByInnings'  => $didNotBatByInnings,
            'didNotBowlByInnings' => $didNotBowlByInnings,
        ]);
    }
}
