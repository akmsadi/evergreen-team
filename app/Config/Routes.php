<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
$routes->post('/join-us', 'Home::joinUs');
$routes->get('/admin/login', 'AdminAuth::login');
$routes->post('/admin/login', 'AdminAuth::attemptLogin');
$routes->get('/admin/dashboard', 'AdminAuth::dashboard');
$routes->get('/admin/logout', 'AdminAuth::logout');
$routes->get('/admin/venues', 'AdminVenues::index');
$routes->get('/admin/venues/(:num)/edit', 'AdminVenues::edit/$1');
$routes->post('/admin/venues', 'AdminVenues::store');
$routes->post('/admin/venues/(:num)/update', 'AdminVenues::update/$1');
$routes->post('/admin/venues/(:num)/delete', 'AdminVenues::delete/$1');
$routes->get('/admin/matches', 'AdminMatches::index');
$routes->get('/admin/matches/create', 'AdminMatches::create');
$routes->get('/admin/matches/(:num)/edit', 'AdminMatches::edit/$1');
$routes->get('/admin/matches/(:num)/start', 'AdminMatches::start/$1');
$routes->get('/admin/matches/(:num)/scoreboard', 'AdminMatches::scoreboard/$1');
$routes->get('/admin/matches/(:num)', 'AdminMatches::show/$1');
$routes->post('/admin/matches', 'AdminMatches::store');
$routes->post('/admin/matches/(:num)/start', 'AdminMatches::storeStart/$1');
$routes->post('/admin/matches/(:num)/update', 'AdminMatches::update/$1');
$routes->post('/admin/matches/(:num)/delete', 'AdminMatches::delete/$1');
$routes->post('/admin/matches/(:num)/innings', 'AdminMatches::storeInnings/$1');
$routes->post('/admin/matches/(:num)/balls', 'AdminMatches::storeBall/$1');
$routes->post('/admin/matches/(:num)/contributors', 'AdminMatches::updateContributors/$1');
$routes->post('/admin/matches/(:num)/expenses', 'AdminMatches::storeExpense/$1');
$routes->post('/admin/matches/(:num)/expenses/(:num)/update', 'AdminMatches::updateExpense/$1/$2');
$routes->post('/admin/matches/(:num)/expenses/(:num)/delete', 'AdminMatches::deleteExpense/$1/$2');
$routes->post('/admin/matches/(:num)/clear-scoreboard', 'AdminMatches::clearScoreboard/$1');
$routes->get('/admin/players', 'AdminPlayers::index');
$routes->get('/admin/accounts', 'AdminPlayers::accounts');
$routes->get('/admin/players/create', 'AdminPlayers::create');
$routes->get('/admin/players/(:num)/edit', 'AdminPlayers::edit/$1');
$routes->post('/admin/players', 'AdminPlayers::store');
$routes->post('/admin/players/(:num)/update', 'AdminPlayers::update/$1');
$routes->post('/admin/players/(:num)/delete', 'AdminPlayers::delete/$1');
$routes->post('/admin/players/deposits', 'AdminPlayers::storeDeposit');
$routes->post('/admin/players/deposits/(:num)/update', 'AdminPlayers::updateDeposit/$1');
$routes->post('/admin/players/deposits/(:num)/delete', 'AdminPlayers::deleteDeposit/$1');
$routes->get('/admin/backup', 'AdminAuth::backupDatabase');
