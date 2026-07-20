<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'AuthController::index');

$routes->group('auth', function ($routes) {
    $routes->get('login', 'AuthController::index');
    $routes->post('login', 'AuthController::login');
    $routes->get('logout', 'AuthController::logout');
});

$routes->group('', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'TransactionController::dashboard');
    $routes->get('balance', 'TransactionController::balance');
    $routes->get('deposit', 'TransactionController::deposit');
    $routes->post('deposit', 'TransactionController::doDeposit');
    $routes->get('withdraw', 'TransactionController::withdraw');
    $routes->post('withdraw', 'TransactionController::doWithdraw');
    $routes->get('transfer', 'TransactionController::transfer');
    $routes->post('transfer', 'TransactionController::doTransfer');
    $routes->get('history', 'TransactionController::history');
});

$routes->group('admin', ['filter' => 'operator'], function ($routes) {
    $routes->get('dashboard', 'OperatorController::dashboard');
    $routes->get('operators', 'OperatorController::operators');
    $routes->post('operators/add', 'OperatorController::addOperator');
    $routes->post('operators/update/(:num)', 'OperatorController::updateOperator/$1');
    $routes->post('operators/delete/(:num)', 'OperatorController::deleteOperator/$1');
    $routes->get('prefixes', 'OperatorController::prefixes');
    $routes->post('prefixes/add', 'OperatorController::addPrefix');
    $routes->post('prefixes/update/(:num)', 'OperatorController::updatePrefix/$1');
    $routes->post('prefixes/delete/(:num)', 'OperatorController::deletePrefix/$1');
    $routes->get('fees', 'OperatorController::fees');
    $routes->post('fees/update/(:num)', 'OperatorController::updateFee/$1');
    $routes->get('statistics', 'OperatorController::statistics');
});
