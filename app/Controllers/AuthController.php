<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\PrefixModel;

class AuthController extends BaseController
{
    private const OPERATOR_PHONE = '0330000000';

    public function index()
    {
        if (session()->get('logged_in')) {
            if (session()->get('is_operator')) {
                return redirect()->to('/admin/dashboard');
            }

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
            'is_operator' => $user['phone_number'] === self::OPERATOR_PHONE,
        ]);

        $redirectTo = $user['phone_number'] === self::OPERATOR_PHONE ? '/admin/dashboard' : '/dashboard';

        return redirect()->to($redirectTo)->with('success', 'Bienvenue ' . $user['phone_number']);
    }

    // validate le numéro de téléphone et créer un utilisateur si le numéro est valide
    public function validate_mg_phone($phoneNumber){
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber); // Supprimer les caractères non numériques

        // verifier qu'il a bien 10 chiffres
        if (strlen($phoneNumber) !== 10) {
            return false; // Le numéro doit contenir exactement 10 chiffres
        }
        
        // verifier que le numéro commence par 033 ou 037
        if (!preg_match('/^(033|037)/', $phoneNumber)) {
            return false; // Le numéro doit commencer par 033 ou 037
        }
        return true;
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth/login');
    }
}
