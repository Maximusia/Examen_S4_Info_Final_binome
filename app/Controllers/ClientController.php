<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\UserModel;
use App\Models\TransactionModel;

class ClientController extends Controller
{
    private $userModel;
    private $transactionModel;

    public function __construct()
    {
        parent::__construct();
        
        // Vérifie que l'utilisateur est connecté
        if (!$this->session->isLoggedIn()) {
            $this->redirect('/auth/login');
        }
        
        $this->userModel = new UserModel();
        $this->transactionModel = new TransactionModel();
    }

    // Dashboard - Affiche le solde et les menus
    public function dashboard()
    {
        $userId = $this->session->getUserId();
        $user = $this->userModel->find($userId);
        
        if (!$user) {
            $this->session->logout();
            $this->redirect('/auth/login');
        }

        // Récupère les statistiques rapides (optionnel)
        $stats = $this->transactionModel->getUserStats($userId);

        $this->render('client/dashboard', [
            'user' => $user,
            'balance' => $user['balance'],
            'stats' => $stats
        ]);
    }

    // Affiche le solde (peut être juste une redirection vers dashboard)
    public function balance()
    {
        $this->redirect('/dashboard');
    }

    // Historique des transactions
    public function history()
    {
        $userId = $this->session->getUserId();
        $transactions = $this->transactionModel->getUserHistory($userId);

        $this->render('client/history', [
            'transactions' => $transactions
        ]);
    }

    // Formulaire de dépôt
    public function deposit()
    {
        $this->render('client/deposit');
    }

    // Traitement du dépôt
    public function doDeposit()
    {
        $userId = $this->session->getUserId();
        $amount = (int) ($_POST['amount'] ?? 0);

        // Vérifier le montant
        if ($amount <= 0) {
            $_SESSION['error'] = "Le montant doit être supérieur à 0.";
            $this->redirect('/deposit');
            return;
        }

        // Ajouter au solde
        $user = $this->userModel->find($userId);
        $newBalance = $user['balance'] + $amount;
        $this->userModel->update($userId, ['balance' => $newBalance]);

        // Sauvegarder la transaction
        $this->transactionModel->insert([
            'user_id' => $userId,
            'operation_type_id' => 1, // Dépôt
            'amount' => $amount,
            'fee' => 0,
            'receiver_user_id' => null
        ]);

        $_SESSION['success'] = "Dépôt de " . number_format($amount) . " Ar effectué avec succès ! Nouveau solde : " . number_format($newBalance) . " Ar";
        $this->redirect('/dashboard');
    }

    // Formulaire de retrait
    public function withdraw()
    {
        $this->render('client/withdraw');
    }

    // Traitement du retrait
    public function doWithdraw()
    {
        $userId = $this->session->getUserId();
        $amount = (int) ($_POST['amount'] ?? 0);

        // Vérifier le montant
        if ($amount <= 0) {
            $_SESSION['error'] = "Le montant doit être supérieur à 0.";
            $this->redirect('/withdraw');
            return;
        }

        // Calculer les frais
        $fee = calculate_fee('withdraw', $amount);
        $total = $amount + $fee;

        // Vérifier le solde
        $user = $this->userModel->find($userId);
        if ($user['balance'] < $total) {
            $_SESSION['error'] = "Solde insuffisant. Vous avez besoin de " . number_format($total) . " Ar (montant + frais de " . number_format($fee) . " Ar).";
            $this->redirect('/withdraw');
            return;
        }

        // Déduire du solde
        $newBalance = $user['balance'] - $total;
        $this->userModel->update($userId, ['balance' => $newBalance]);

        // Sauvegarder la transaction
        $this->transactionModel->insert([
            'user_id' => $userId,
            'operation_type_id' => 2, // Retrait
            'amount' => $amount,
            'fee' => $fee,
            'receiver_user_id' => null
        ]);

        $_SESSION['success'] = "Retrait de " . number_format($amount) . " Ar effectué. Frais : " . number_format($fee) . " Ar. Nouveau solde : " . number_format($newBalance) . " Ar";
        $this->redirect('/dashboard');
    }

    // Formulaire de transfert
    public function transfer()
    {
        $this->render('client/transfer');
    }

    // Traitement du transfert
    public function doTransfer()
    {
        $userId = $this->session->getUserId();
        $receiverPhone = trim($_POST['receiver_phone'] ?? '');
        $amount = (int) ($_POST['amount'] ?? 0);

        // Vérifier le destinataire
        if (empty($receiverPhone)) {
            $_SESSION['error'] = "Veuillez entrer un numéro de destinataire.";
            $this->redirect('/transfer');
            return;
        }

        // Vérifier que le destinataire n'est pas l'expéditeur
        $user = $this->userModel->find($userId);
        if ($user['phone_number'] === $receiverPhone) {
            $_SESSION['error'] = "Vous ne pouvez pas vous transférer de l'argent à vous-même.";
            $this->redirect('/transfer');
            return;
        }

        // Vérifier que le destinataire existe
        $receiver = $this->userModel->findByPhone($receiverPhone);
        if (!$receiver) {
            $_SESSION['error'] = "Le numéro de destinataire n'existe pas dans le système.";
            $this->redirect('/transfer');
            return;
        }

        // Vérifier le montant
        if ($amount <= 0) {
            $_SESSION['error'] = "Le montant doit être supérieur à 0.";
            $this->redirect('/transfer');
            return;
        }

        // Calculer les frais
        $fee = calculate_fee('transfer', $amount);
        $total = $amount + $fee;

        // Vérifier le solde de l'expéditeur
        if ($user['balance'] < $total) {
            $_SESSION['error'] = "Solde insuffisant. Vous avez besoin de " . number_format($total) . " Ar (montant + frais de " . number_format($fee) . " Ar).";
            $this->redirect('/transfer');
            return;
        }

        // Débiter l'expéditeur
        $newSenderBalance = $user['balance'] - $total;
        $this->userModel->update($userId, ['balance' => $newSenderBalance]);

        // Créditer le destinataire
        $newReceiverBalance = $receiver['balance'] + $amount;
        $this->userModel->update($receiver['id'], ['balance' => $newReceiverBalance]);

        // Sauvegarder la transaction
        $this->transactionModel->insert([
            'user_id' => $userId,
            'operation_type_id' => 3, // Transfert
            'amount' => $amount,
            'fee' => $fee,
            'receiver_user_id' => $receiver['id']
        ]);

        $_SESSION['success'] = "Transfert de " . number_format($amount) . " Ar à " . $receiverPhone . " effectué. Frais : " . number_format($fee) . " Ar. Nouveau solde : " . number_format($newSenderBalance) . " Ar";
        $this->redirect('/dashboard');
    }
}