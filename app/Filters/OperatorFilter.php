<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class OperatorFilter implements FilterInterface
{
    private const OPERATOR_PHONE = '0330000000';

    public function before(RequestInterface $request, $arguments = null)
    {
        // Vérifie que l'utilisateur est connecté
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth/login')->with('error', 'Veuillez vous connecter.');
        }

        $isOperator = (bool) session()->get('is_operator');

        if (!$isOperator) {
            $userModel = model('App\Models\UserModel');
            $user = $userModel->find(session()->get('user_id'));

            $isOperator = $user && $user['phone_number'] === self::OPERATOR_PHONE;
        }

        if (!$isOperator) {
            return redirect()->to('/dashboard')->with('error', 'Accès réservé à l\'administrateur.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Rien
    }
}