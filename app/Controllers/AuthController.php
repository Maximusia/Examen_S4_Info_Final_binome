<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\PrefixModel;

class AuthController extends BaseController
{
    public function index()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    public function login()
    {
        $phone = trim($this->request->getPost('phone_number') ?? '');

        if (empty($phone)) {
            return redirect()->back()->with('error', 'Veuillez entrer un numéro.');
        }

        // Vérifier le préfixe
        $prefixModel = new PrefixModel();
        $isValid = $prefixModel->isValidPrefix($phone);

        if (!$isValid) {
            return redirect()->back()->with('error', 'Numéro non autorisé. Utilisez 033 ou 037.');
        }

        // Chercher ou créer l'utilisateur
        $userModel = new UserModel();
        $user = $userModel->where('phone_number', $phone)->first();

        if (!$user) {
            $userId = $userModel->insert([
                'phone_number' => $phone,
                'balance'      => 0,
            ]);
            $user = $userModel->find($userId);
        }

        // Ouvrir la session
        session()->set([
            'user_id'   => $user['id'],
            'logged_in' => true,
            'phone_number' => $user['phone_number'],
        ]);

        return redirect()->to('/dashboard')->with('success', 'Bienvenue ' . $user['phone_number']);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth/login');
    }
}
