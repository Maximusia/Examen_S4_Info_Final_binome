<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\UserModel;
use App\Models\PrefixModel;

class AuthController extends Controller
{
    // Affiche le formulaire de connexion
    public function index()
    {
        // Si déjà connecté, redirige vers le dashboard
        if ($this->session->isLoggedIn()) {
            $this->redirect('/dashboard');
        }
        
        $this->render('auth/login');
    }

    // Traite l'authentification
    public function login()
    {
        $phone = $_POST['phone'] ?? '';
        $phone = trim($phone);

        // 1. Vérifier que le numéro n'est pas vide
        if (empty($phone)) {
            $_SESSION['error'] = "Veuillez entrer un numéro de téléphone.";
            $this->redirect('/auth/login');
            return;
        }

        // 2. Vérifier que le numéro commence par un préfixe autorisé
        $prefixModel = new PrefixModel();
        $isValid = $prefixModel->isValidPrefix($phone);
        
        if (!$isValid) {
            $_SESSION['error'] = "Ce numéro de téléphone n'est pas autorisé. Utilisez un préfixe valide (033, 037, ...).";
            $this->redirect('/auth/login');
            return;
        }

        // 3. Chercher l'utilisateur ou le créer
        $userModel = new UserModel();
        $user = $userModel->findByPhone($phone);
        
        if (!$user) {
            // Créer un nouvel utilisateur avec solde à 0
            $userId = $userModel->insert([
                'phone_number' => $phone,
                'balance' => 0
            ]);
            
            if (!$userId) {
                $_SESSION['error'] = "Erreur lors de la création du compte. Veuillez réessayer.";
                $this->redirect('/auth/login');
                return;
            }
            
            // Récupérer l'utilisateur fraîchement créé
            $user = $userModel->find($userId);
        }

        // 4. Ouvrir la session
        $this->session->login($user['id']);
        
        // 5. Rediriger vers le dashboard
        $_SESSION['success'] = "Bienvenue " . $user['phone_number'] . " !";
        $this->redirect('/dashboard');
    }

    // Déconnexion
    public function logout()
    {
        $this->session->logout();
        $this->redirect('/auth/login');
    }
}
