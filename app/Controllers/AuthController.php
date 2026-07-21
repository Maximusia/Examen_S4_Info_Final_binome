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

        $prefixModel = new PrefixModel();
        $operator = $prefixModel->findOperatorByPhone($phone);

        if (!$operator) {
            return redirect()->back()->with('error', 'Numéro non autorisé.');
        }

        $userModel = new UserModel();
        $user = $userModel->findByPhone($phone);

        if (!$user) {
            if (!$prefixModel->isOwnOperatorPhone($phone)) {
                return redirect()->back()->with('error', 'Ce numéro externe ne peut pas être créé automatiquement.');
            }

            $userId = $userModel->insert([
                'phone_number' => $phone,
                'balance' => 0,
                'savings_balance' => 0,
                'savings_percent' => 0,
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

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth/login');
    }
}
