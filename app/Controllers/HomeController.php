<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

use App\Models\PlayerModel;
use App\Models\MatchModel;
use App\Models\EventModel;
use App\Models\EventImageModel;
use App\Models\TeamModel;
use App\Models\TeamPlayerModel;
use App\Models\TeamAchievementModel;

class HomeController extends BaseController
{
    private PlayerModel $players;
    private MatchModel $matches;
    private EventModel $events;
    private EventImageModel $eventImages;
    private TeamModel $teams;
    private TeamPlayerModel $teamPlayers;
    private TeamAchievementModel $teamAchievements;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        helper(['form', 'url']);
        $this->players = new PlayerModel();
        $this->matches = new MatchModel();
        $this->events = new EventModel();
        $this->eventImages = new EventImageModel();
        $this->teams = new TeamModel();
        $this->teamPlayers = new TeamPlayerModel();
        $this->teamAchievements = new TeamAchievementModel();
    }

    public function index(): string
    {
        $lastMatch = null;
        $recentMatches = $this->matches->recentList(1);
        if (!empty($recentMatches)) {
            $lastMatch = $recentMatches[0];
        }
        $nextMatch = $this->matches->nextMatch();
        $players = $this->players->where('status', 'approved')->orderBy('id', 'RANDOM')->findAll(10);

        $eventsRaw = $this->events->orderedList();
        $events = [];
        foreach ($eventsRaw as $event) {
            $event['images'] = $this->eventImages->forEvent((int) $event['id']);
            $events[] = $event;
        }

        return view('index-view', [
            'playerCount' => $this->players->where('status', 'approved')->countAllResults(),
            'pendingCount' => $this->players->where('status', 'pending')->countAllResults(),
            'lastMatch' => $lastMatch,
            'nextMatch' => $nextMatch,
            'players' => $players,
            'events' => $events,
            'teams' => $this->teams->orderBy('name', 'ASC')->findAll(),
        ]);
    }

    public function joinUs()
    {
        $rules = [
            'name' => 'required|min_length[2]',
            'email' => 'permit_empty|valid_email',
            'phone' => 'required|min_length[7]',
            'address' => 'required|min_length[5]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to(site_url('/') . '#join')
                ->withInput()
                ->with('join_errors', $this->validator->getErrors());
        }

        $playerData = [
            'name' => (string) $this->request->getPost('name'),
            'email' => (string) $this->request->getPost('email'),
            'phone' => (string) $this->request->getPost('phone'),
            'address' => (string) $this->request->getPost('address'),
            'status' => 'pending',
        ];

        $this->players->insert($playerData);

        return redirect()->to(site_url('/') . '#join')->with('join_success', 'Thank you for your submission! We have received your details and will let you know once your request has been approved.');
    }

    public function team($team_slug)
    {
        $team = $this->teams->withCaptainAndViceCaptainBySlug($team_slug);
        $members = $this->teamPlayers->membersByTeam($team['id']);
        $achievements = $this->teamAchievements->where('team_id', $team['id'])->findAll();
        return view('team-view', ['team' => $team, 'members' => $members, 'achievements' => $achievements]);
    }
}
