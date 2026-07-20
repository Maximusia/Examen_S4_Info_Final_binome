<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class OperatorFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Vérifie que l'utilisateur est connecté
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth/login')->with('error', 'Veuillez vous connecter.');
        }

        // Récupère l'utilisateur
        $userModel = model('App\Models\UserModel');
        $user = $userModel->find(session()->get('user_id'));

        // Ici on considère que l'admin a le numéro 0330000000
        // Tu peux aussi ajouter un champ 'role' dans la table users
        if (!$user || $user['phone_number'] !== '0330000000') {
            return redirect()->to('/dashboard')->with('error', 'Accès réservé à l\'administrateur.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Rien
    }
}