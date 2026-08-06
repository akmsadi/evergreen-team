<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Libraries\PlayerCareerStats;
use App\Models\PlayerModel;

class PublicPlayers extends BaseController
{
    private PlayerModel $players;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);
        helper(['url', 'scoreboard']);
        $this->players = new PlayerModel();
    }

    public function profile(int $playerId): string
    {
        $player = $this->players->find($playerId);
        if ($player === null) {
            throw PageNotFoundException::forPageNotFound('Player not found.');
        }

        $db = db_connect();
        $battingRows = $db->table('match_summary_batsmen msb')
            ->select('msb.runs, msb.balls, msb.fours, msb.sixes, msb.dismissal_mode, msb.match_id')
            ->where('msb.player_id', $playerId)
            ->orderBy('msb.match_id', 'ASC')
            ->get()->getResultArray();

        $bowlingRows = $db->table('match_summary_bowlers msbow')
            ->select('msbow.overs, msbow.maidens, msbow.runs, msbow.wickets, msbow.match_id')
            ->where('msbow.player_id', $playerId)
            ->orderBy('msbow.match_id', 'ASC')
            ->get()->getResultArray();

        $matchPlayerRows = $db->table('match_players mp')
            ->select('mp.match_id')
            ->where('mp.player_id', $playerId)
            ->get()->getResultArray();

        $matchSummaryRows = $db->table('match_summary_batsmen msb')
            ->select('msb.match_id')
            ->where('msb.player_id', $playerId)
            ->get()->getResultArray();

        $matchRows = $db->table('match_players mp')
            ->select('mp.match_id, CONCAT(m.team_name, " vs ", m.opponent_name) AS match_name, m.scheduled_at AS match_date')
            ->join('matches m', 'm.id = mp.match_id')
            ->where('mp.player_id', $playerId)
            ->orderBy('m.scheduled_at', 'ASC')
            ->get()->getResultArray();

        $battingDetailRows = $db->table('match_summary_batsmen msb')
            ->select('msb.match_id, msb.runs, msb.balls, msb.fours, msb.sixes, msb.dismissal_mode, msb.dismissal_by, msb.fielder_id, CONCAT(m.team_name, " vs ", m.opponent_name) AS match_name, m.scheduled_at AS match_date, p.name AS player_name, p2.name AS dismissal_by_name, p3.name AS fielder_name')
            ->join('matches m', 'm.id = msb.match_id')
            ->join('players p', 'p.id = msb.player_id')
            ->join('players p2', 'p2.id = msb.dismissal_by', 'left')
            ->join('players p3', 'p3.id = msb.fielder_id', 'left')
            ->where('msb.player_id', $playerId)
            ->orderBy('msb.match_id', 'ASC')
            ->get()->getResultArray();

        $bowlingDetailRows = $db->table('match_summary_bowlers msb')
            ->select('msb.match_id, msb.overs, msb.maidens, msb.runs, msb.wickets, CONCAT(m.team_name, " vs ", m.opponent_name) AS match_name, m.scheduled_at AS match_date, p.name AS player_name')
            ->join('matches m', 'm.id = msb.match_id')
            ->join('players p', 'p.id = msb.player_id')
            ->where('msb.player_id', $playerId)
            ->orderBy('msb.match_id', 'ASC')
            ->get()->getResultArray();

        $fieldingRows = $db->table('match_summary_batsmen msb')
            ->select('msb.match_id, msb.dismissal_mode, CONCAT(m.team_name, " vs ", m.opponent_name) AS match_name, m.scheduled_at AS match_date, p.name AS batsman_name, p2.name AS bowler_name')
            ->join('matches m', 'm.id = msb.match_id')
            ->join('players p', 'p.id = msb.player_id')
            ->join('players p2', 'p2.id = msb.dismissal_by', 'left')
            ->where('msb.fielder_id', $playerId)
            ->whereIn('msb.dismissal_mode', ['caught', 'run_out', 'stumped'])
            ->orderBy('msb.match_id', 'ASC')
            ->get()->getResultArray();

        $wicketRows = $db->table('match_summary_wickets msw')
            ->select('msw.match_id, msw.score_at_dismissal, msw.over_at_dismissal, CONCAT(m.team_name, " vs ", m.opponent_name) AS match_name, m.scheduled_at AS match_date, p.name AS player_name')
            ->join('matches m', 'm.id = msw.match_id')
            ->join('players p', 'p.id = msw.player_id', 'left')
            ->where('msw.player_id', $playerId)
            ->orderBy('msw.match_id', 'ASC')
            ->get()->getResultArray();

        foreach ($bowlingDetailRows as &$row) {
            $overs = (float) ($row['overs'] ?? 0.0);
            $runs = (int) ($row['runs'] ?? 0);
            $row['economy'] = $overs > 0 ? round($runs / $overs, 2) : 0.0;
        }
        unset($row);

        $stats = (new PlayerCareerStats())->build(
            $player,
            $battingRows,
            $bowlingRows,
            $matchPlayerRows,
            $matchSummaryRows,
            $matchRows,
            $battingDetailRows,
            $bowlingDetailRows,
            $fieldingRows,
            $wicketRows,
        );

        return view('public/player-profile', [
            'player' => $player,
            'stats' => $stats,
        ]);
    }
}
