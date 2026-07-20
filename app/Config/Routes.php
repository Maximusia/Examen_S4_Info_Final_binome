<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ------------------ ROUTE PAR DÉFAUT ------------------
// Redirige vers la page de login si l'utilisateur n'est pas connecté
$routes->get('/', 'AuthController::index');


// =======================================================
// GROUPE 1 : AUTHENTIFICATION (pas de filtre)
// =======================================================
$routes->group('auth', function($routes) {
    // Page de connexion (GET) et traitement du formulaire (POST)
    $routes->get('login', 'AuthController::index');        // Affiche le formulaire
    $routes->post('login', 'AuthController::login');       // Traite la connexion
    $routes->get('logout', 'AuthController::logout');      // Déconnexion
});


// =======================================================
// GROUPE 2 : CLIENT (protégé par AuthMiddleware)
// =======================================================
$routes->group('', ['filter' => 'auth'], function($routes) {
    
    // Tableau de bord principal
    $routes->get('dashboard', 'ClientController::dashboard');
    
    // Solde (peut être une page ou juste un affichage sur le dashboard)
    $routes->get('balance', 'ClientController::balance');
    
    // Dépôt
    $routes->get('deposit', 'ClientController::deposit');      // Formulaire
    $routes->post('deposit', 'ClientController::doDeposit');  // Traitement
    
    // Retrait
    $routes->get('withdraw', 'ClientController::withdraw');
    $routes->post('withdraw', 'ClientController::doWithdraw');
    
    // Transfert
    $routes->get('transfer', 'ClientController::transfer');
    $routes->post('transfer', 'ClientController::doTransfer');
    
    // Historique des transactions
    $routes->get('history', 'ClientController::history');
    
    // *** Routes pour TransactionController (si tu veux séparer les actions) ***
    // Exemple : traiter un retrait via TransactionController au lieu de ClientController
    // $routes->post('transaction/withdraw', 'TransactionController::withdraw');
    // $routes->post('transaction/transfer', 'TransactionController::transfer');
    // (mais je te conseille de tout mettre dans ClientController pour plus de simplicité)
    
});


// =======================================================
// GROUPE 3 : OPÉRATEUR (ADMIN) - protégé par OperatorMiddleware
// =======================================================
$routes->group('admin', ['filter' => 'operator'], function($routes) {
    
    // Gestion des préfixes téléphoniques
    $routes->get('prefixes', 'OperatorController::prefixes');           // Affiche la liste
    $routes->post('prefixes/add', 'OperatorController::addPrefix');    // Ajoute un préfixe
    $routes->post('prefixes/delete/(:num)', 'OperatorController::deletePrefix/$1'); // Supprime (ID)
    
    // Gestion des barèmes de frais
    $routes->get('fees', 'OperatorController::fees');                   // Affiche le tableau
    $routes->post('fees/update/(:num)', 'OperatorController::updateFee/$1'); // Met à jour (ID de la règle)
    
});


// =======================================================
// ROUTES SUPPLÉMENTAIRES (optionnelles)
// =======================================================

// Si tu veux un accès direct à l'admin sans passer par /admin/login
// (tu peux ajouter une route pour un login admin spécifique)
// $routes->get('admin/login', 'OperatorController::login');

// Redirection si l'utilisateur tape une URL inexistante (optionnel)
// $routes->set404Override('AuthController::page404');
