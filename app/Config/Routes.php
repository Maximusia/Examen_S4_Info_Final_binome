<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Redirection vers login
$routes->get('/', 'AuthController::index');

// ========== AUTH ==========
$routes->group('auth', function($routes) {
    $routes->get('login', 'AuthController::index');
    $routes->post('login', 'AuthController::login');
    $routes->get('logout', 'AuthController::logout');
});

// ========== CLIENT (protégé par auth) ==========
$routes->group('', ['filter' => 'auth'], function($routes) {
    $routes->get('dashboard', 'ClientController::dashboard');
    $routes->get('balance', 'ClientController::balance');

    // Dépôt
    $routes->get('deposit', 'ClientController::deposit');
    $routes->post('deposit', 'ClientController::doDeposit');

    // Retrait
    $routes->get('withdraw', 'ClientController::withdraw');
    $routes->post('withdraw', 'ClientController::doWithdraw');

    // Transfert
    $routes->get('transfer', 'ClientController::transfer');
    $routes->post('transfer', 'ClientController::doTransfer');

    // Historique
    $routes->get('history', 'ClientController::history');
});

// ========== OPÉRATEUR (protégé par auth + operator) ==========
$routes->group('admin', ['filter' => 'operator'], function($routes) {
    $routes->get('dashboard', 'OperatorController::dashboard');
    $routes->get('prefixes', 'OperatorController::prefixes');
    $routes->post('prefixes/add', 'OperatorController::addPrefix');
    $routes->post('prefixes/delete/(:num)', 'OperatorController::deletePrefix/$1');
    $routes->get('fees', 'OperatorController::fees');
    $routes->post('fees/update/(:num)', 'OperatorController::updateFee/$1');
    $routes->get('statistics', 'OperatorController::statistics');
});