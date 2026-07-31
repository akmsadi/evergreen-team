<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\HomeController;
use App\Controllers\AdminAuth;
use App\Controllers\AdminVenues;
use App\Controllers\AdminMatches;
use App\Controllers\AdminPlayers;
use App\Controllers\AdminEvents;
use App\Controllers\AdminExpenses;
use App\Controllers\AdminTeams;
use App\Controllers\Scoreboard;

use App\Controllers\Api\Users as ApiUsers;
use App\Controllers\Api\Matches as ApiMatches;
use App\Controllers\Api\Players as ApiPlayers;
use App\Controllers\Api\Venues as ApiVenues;

/** @var RouteCollection $routes */

// Public routes
$routes->get('/', [HomeController::class, 'index']);
$routes->post('/join-us', [HomeController::class, 'joinUs']);
$routes->get('/team/(:segment)', [HomeController::class, 'team']);
$routes->get('/matches/(:segment)/scoreboard', [Scoreboard::class, 'view']);

// Auth routes
$routes->get('/admin/login', [AdminAuth::class, 'login']);
$routes->post('/admin/login', [AdminAuth::class, 'attemptLogin']);
$routes->get('/admin/dashboard', [AdminAuth::class, 'dashboard']);
$routes->get('/admin/logout', [AdminAuth::class, 'logout']);

// Venue routes
$routes->get('/admin/venues', [AdminVenues::class, 'index']);
$routes->get('/admin/venues/(:num)/edit', [AdminVenues::class, 'edit']);
$routes->post('/admin/venues', [AdminVenues::class, 'store']);
$routes->post('/admin/venues/(:num)/update', [AdminVenues::class, 'update']);
$routes->post('/admin/venues/(:num)/delete', [AdminVenues::class, 'delete']);

// Match routes
$routes->get('/admin/matches', [AdminMatches::class, 'index']);
$routes->get('/admin/matches/create', [AdminMatches::class, 'create']);
$routes->get('/admin/matches/(:num)/edit', [AdminMatches::class, 'edit']);
$routes->get('/admin/matches/(:num)', [AdminMatches::class, 'show']);
$routes->post('/admin/matches', [AdminMatches::class, 'store']);
$routes->post('/admin/matches/(:num)/update', [AdminMatches::class, 'update']);
$routes->post('/admin/matches/(:num)/update-status', [AdminMatches::class, 'updateStatus']);
$routes->post('/admin/matches/(:num)/delete', [AdminMatches::class, 'delete']);
$routes->post('/admin/matches/(:num)/permanent-delete', [AdminMatches::class, 'permanentDelete']);
$routes->post('/admin/matches/(:num)/innings', [AdminMatches::class, 'storeInnings']);
$routes->post('/admin/matches/(:num)/contributors', [AdminMatches::class, 'updateContributors']);
$routes->post('/admin/matches/(:num)/expenses', [AdminMatches::class, 'storeExpense']);
$routes->post('/admin/matches/(:num)/expenses/(:num)/update', [AdminMatches::class, 'updateExpense']);
$routes->post('/admin/matches/(:num)/expenses/(:num)/delete', [AdminMatches::class, 'deleteExpense']);
$routes->post('/admin/matches/(:num)/clear-scoreboard', [AdminMatches::class, 'clearScoreboard']);

// Match scoreboard routes
$routes->get('/admin/matches/(:num)/scoreboard', [AdminMatches::class, 'editScoreboard']);
$routes->get('/admin/matches/(:num)/scoreboard/preview', [AdminMatches::class, 'scoreboardPreview']);
$routes->post('/admin/matches/(:num)/complete', [AdminMatches::class, 'completeMatch']);
$routes->post('/admin/matches/(:num)/scoreboard/toss', [AdminMatches::class, 'saveToss']);
$routes->post('/admin/matches/(:num)/scoreboard/batting/(:num)/save', [AdminMatches::class, 'saveBatting']);
$routes->post('/admin/matches/(:num)/scoreboard/bowling/(:num)/save', [AdminMatches::class, 'saveBowling']);
$routes->post('/admin/matches/(:num)/scoreboard/wickets/(:num)/save', [AdminMatches::class, 'saveFallOfWickets']);

// General expense routes
$routes->get('/admin/expenses', [AdminExpenses::class, 'index']);
$routes->post('/admin/expenses', [AdminExpenses::class, 'store']);
$routes->post('/admin/expenses/(:num)/update', [AdminExpenses::class, 'update']);
$routes->post('/admin/expenses/(:num)/delete', [AdminExpenses::class, 'delete']);

// Player routes
$routes->get('/admin/players', [AdminPlayers::class, 'index']);
$routes->get('/admin/accounts', [AdminPlayers::class, 'accounts']);
$routes->get('/admin/deposits', [AdminPlayers::class, 'deposits']);
$routes->get('/admin/finance-overview', [AdminPlayers::class, 'financeOverview']);
$routes->get('/admin/players/create', [AdminPlayers::class, 'create']);
$routes->get('/admin/players/(:num)/edit', [AdminPlayers::class, 'edit']);
$routes->post('/admin/players', [AdminPlayers::class, 'store']);
$routes->post('/admin/players/(:num)/update', [AdminPlayers::class, 'update']);
$routes->post('/admin/players/(:num)/delete', [AdminPlayers::class, 'delete']);
$routes->post('/admin/players/deposits', [AdminPlayers::class, 'storeDeposit']);
$routes->post('/admin/players/deposits/(:num)/update', [AdminPlayers::class, 'updateDeposit']);
$routes->post('/admin/players/deposits/(:num)/delete', [AdminPlayers::class, 'deleteDeposit']);

// Event routes
$routes->get('/admin/events', [AdminEvents::class, 'index']);
$routes->get('/admin/events/(:num)/edit', [AdminEvents::class, 'edit']);
$routes->post('/admin/events', [AdminEvents::class, 'store']);
$routes->post('/admin/events/(:num)/update', [AdminEvents::class, 'update']);
$routes->post('/admin/events/(:num)/delete', [AdminEvents::class, 'delete']);
$routes->post('/admin/events/(:num)/images', [AdminEvents::class, 'storeImage']);
$routes->post('/admin/events/(:num)/images/(:num)/delete', [AdminEvents::class, 'deleteImage']);

// Team routes
$routes->get('/admin/teams', [AdminTeams::class, 'index']);
$routes->get('/admin/teams/create', [AdminTeams::class, 'create']);
$routes->get('/admin/teams/(:num)/edit', [AdminTeams::class, 'edit']);
$routes->post('/admin/teams', [AdminTeams::class, 'store']);
$routes->post('/admin/teams/(:num)/update', [AdminTeams::class, 'update']);
$routes->post('/admin/teams/(:num)/delete', [AdminTeams::class, 'delete']);
$routes->post('/admin/teams/(:num)/members', [AdminTeams::class, 'storeMember']);
$routes->post('/admin/teams/(:num)/members/(:num)/update', [AdminTeams::class, 'updateMember']);
$routes->post('/admin/teams/(:num)/members/(:num)/delete', [AdminTeams::class, 'deleteMember']);

// Handle CORS preflight for all API endpoints
$routes->options('api/(:any)', static function () {
    return service('response')->setStatusCode(204);
});
$routes->post('/api/login', [ApiUsers::class, 'login'], ['filter' => 'apikey']);
$routes->get('/api/matches', [ApiMatches::class, 'index'], ['filter' => 'apikey']);
$routes->get('/api/matches/scheduled', [ApiMatches::class, 'scheduled'], ['filter' => 'apikey']);
$routes->get('/api/players', [ApiPlayers::class, 'index'], ['filter' => 'apikey']);
$routes->post('/api/players', [ApiPlayers::class, 'saveList'], ['filter' => 'apikey']);
$routes->get('/api/venues', [ApiVenues::class, 'index'], ['filter' => 'apikey']);