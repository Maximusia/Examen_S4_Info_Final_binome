<?php

namespace App\Core;

class Session
{
    // Le constructeur démarre la session si elle n'est pas déjà active
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Ajoute ou modifie une variable en session
    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    // Récupère une variable de session (ou null si inexistante)
    public function get($key)
    {
        return $_SESSION[$key] ?? null;
    }

    // Vérifie si une clé existe en session
    public function has($key)
    {
        return isset($_SESSION[$key]);
    }

    // Supprime une variable de session
    public function remove($key)
    {
        unset($_SESSION[$key]);
    }

    // Action : Connecte un utilisateur (stocke son ID et un flag)
    public function login($userId)
    {
        $this->set('user_id', $userId);
        $this->set('logged_in', true);
    }

    // Action : Déconnecte l'utilisateur (détruit la session)
    public function logout()
    {
        // Supprime toutes les variables
        $_SESSION = [];
        // Détruit la session côté serveur
        session_destroy();
        // Détruit le cookie de session côté client
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
    }

    // Vérifie si l'utilisateur est bien connecté
    public function isLoggedIn()
    {
        return $this->has('logged_in') && $this->get('logged_in') === true;
    }

    // Raccourci pour récupérer l'ID de l'utilisateur connecté
    public function getUserId()
    {
        return $this->get('user_id');
    }
}