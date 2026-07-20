<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\TransactionModel;

class ClientController extends BaseController
{
    private $userModel;
    private $transactionModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->transactionModel = new TransactionModel();
    }

    private function getCurrentUser()
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);
        if (!$user) {
            session()->destroy();
            return redirect()->to('/auth/login');
        }
        return $user;
    }

    public function dashboard()
    {
        $user = $this->getCurrentUser();
        $stats = $this->transactionModel->getUserStats($user['id']);

        return view('client/dashboard', [
            'user'    => $user,
            'balance' => $user['balance'],
            'stats'   => $stats,
        ]);
    }

    public function balance()
    {
        return redirect()->to('/dashboard');
    }

    // ---------- DEPOT ----------
    public function deposit()
    {
        return view('client/deposit');
    }

    public function doDeposit()
    {
        $user = $this->getCurrentUser();
        $amount = (int) $this->request->getPost('amount');

        if ($amount <= 0) {
            return redirect()->back()->with('error', 'Montant invalide.');
        }

        $newBalance = $user['balance'] + $amount;
        $this->userModel->update($user['id'], ['balance' => $newBalance]);

        $this->transactionModel->insert([
            'user_id'           => $user['id'],
            'operation_type_id' => 1, // Dépôt
            'amount'            => $amount,
            'fee'               => 0,
            'receiver_user_id'  => null,
        ]);

        return redirect()->to('/dashboard')->with('success', "Dépôt de {$amount} Ar effectué. Nouveau solde : {$newBalance} Ar");
    }

    // ---------- RETRAIT ----------
    public function withdraw()
    {
        return view('client/withdraw');
    }

    public function doWithdraw()
    {
        $user = $this->getCurrentUser();
        $amount = (int) $this->request->getPost('amount');

        if ($amount <= 0) {
            return redirect()->back()->with('error', 'Montant invalide.');
        }

        $fee = calculate_fee('retrait', $amount);
        $total = $amount + $fee;

        if ($user['balance'] < $total) {
            return redirect()->back()->with('error', "Solde insuffisant. Frais : {$fee} Ar. Total : {$total} Ar");
        }

        $newBalance = $user['balance'] - $total;
        $this->userModel->update($user['id'], ['balance' => $newBalance]);

        $this->transactionModel->insert([
            'user_id'           => $user['id'],
            'operation_type_id' => 2, // Retrait
            'amount'            => $amount,
            'fee'               => $fee,
            'receiver_user_id'  => null,
        ]);

        return redirect()->to('/dashboard')->with('success', "Retrait de {$amount} Ar effectué. Frais : {$fee} Ar. Nouveau solde : {$newBalance} Ar");
    }

    // ---------- TRANSFERT ----------
    public function transfer()
    {
        return view('client/transfer');
    }

    public function doTransfer()
    {
        $sender = $this->getCurrentUser();
        $receiverPhone = trim($this->request->getPost('receiver_phone') ?? '');
        $amount = (int) $this->request->getPost('amount');

        if (empty($receiverPhone) || $amount <= 0) {
            return redirect()->back()->with('error', 'Données invalides.');
        }

        if ($sender['phone_number'] === $receiverPhone) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas vous transférer à vous-même.');
        }

        $receiver = $this->userModel->where('phone_number', $receiverPhone)->first();
        if (!$receiver) {
            return redirect()->back()->with('error', 'Destinataire introuvable.');
        }

        $fee = calculate_fee('transfer', $amount);
        $total = $amount + $fee;

        if ($sender['balance'] < $total) {
            return redirect()->back()->with('error', "Solde insuffisant. Frais : {$fee} Ar. Total : {$total} Ar");
        }

        // Débiter l'expéditeur
        $newSenderBalance = $sender['balance'] - $total;
        $this->userModel->update($sender['id'], ['balance' => $newSenderBalance]);

        // Créditer le destinataire
        $newReceiverBalance = $receiver['balance'] + $amount;
        $this->userModel->update($receiver['id'], ['balance' => $newReceiverBalance]);

        // Sauvegarder la transaction
        $this->transactionModel->insert([
            'user_id'           => $sender['id'],
            'operation_type_id' => 3, // Transfert
            'amount'            => $amount,
            'fee'               => $fee,
            'receiver_user_id'  => $receiver['id'],
        ]);

        return redirect()->to('/dashboard')->with('success', "Transfert de {$amount} Ar à {$receiverPhone} effectué. Frais : {$fee} Ar.");
    }

    // ---------- HISTORIQUE ----------
    public function history()
    {
        $user = $this->getCurrentUser();
        $transactions = $this->transactionModel->getUserHistory($user['id']);

        return view('client/history', ['transactions' => $transactions]);
    }
}