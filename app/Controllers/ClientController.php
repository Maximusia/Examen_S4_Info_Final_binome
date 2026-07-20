<?php

namespace App\Controllers;

use App\Libraries\TransferService;
use App\Models\FeeRuleModel;
use App\Models\OperationTypeModel;
use App\Models\PrefixModel;
use App\Models\TransactionModel;
use App\Models\UserModel;

class ClientController extends BaseController
{
    private UserModel $userModel;
    private TransactionModel $transactionModel;
    private FeeRuleModel $feeRuleModel;
    private OperationTypeModel $operationTypeModel;
    private PrefixModel $prefixModel;
    private TransferService $transferService;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->transactionModel = new TransactionModel();
        $this->feeRuleModel = new FeeRuleModel();
        $this->operationTypeModel = new OperationTypeModel();
        $this->prefixModel = new PrefixModel();
        $this->transferService = new TransferService();
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
            'user' => $user,
            'balance' => $user['balance'],
            'stats' => $stats,
        ]);
    }

    public function balance()
    {
        return redirect()->to('/dashboard');
    }

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
            'user_id' => $user['id'],
            'operation_type_id' => 1,
            'amount' => $amount,
            'fee' => 0,
            'receiver_user_id' => null,
        ]);

        return redirect()->to('/dashboard')->with('success', "Depot de {$amount} Ar effectue. Nouveau solde : {$newBalance} Ar");
    }

    public function withdraw()
    {
        $user = $this->getCurrentUser();

        return view('client/withdraw', [
            'balance' => $user['balance'],
            'withdrawal_fees' => $this->getFeeRulesByCode('retrait'),
        ]);
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
            'user_id' => $user['id'],
            'operation_type_id' => 2,
            'amount' => $amount,
            'fee' => $fee,
            'receiver_user_id' => null,
        ]);

        return redirect()->to('/dashboard')->with('success', "Retrait de {$amount} Ar effectue. Frais : {$fee} Ar. Nouveau solde : {$newBalance} Ar");
    }

    public function transfer()
    {
        $user = $this->getCurrentUser();

        return view('client/transfer', [
            'balance' => $user['balance'],
            'transfer_fees' => $this->getFeeRulesByCode('transfer'),
            'own_prefixes' => $this->prefixModel->getOwnOperatorPrefixes(),
            'external_prefixes' => $this->prefixModel->getExternalOperatorPrefixes(),
        ]);
    }

    public function doTransfer()
    {
        $sender = $this->getCurrentUser();
        $receiverInput = trim((string) ($this->request->getPost('receiver_phones') ?? $this->request->getPost('receiver_phone') ?? ''));
        $amount = (int) $this->request->getPost('amount');
        $includeWithdrawalFee = $this->request->getPost('include_withdrawal_fee') ? true : false;

        if ($receiverInput === '' || $amount <= 0) {
            return redirect()->back()->with('error', 'Donnees invalides.');
        }

        try {
            $result = $this->transferService->transferMultiple(
                $sender,
                $receiverInput,
                $amount,
                $includeWithdrawalFee
            );

            return redirect()->to('/history')->with(
                'success',
                "Transfert multiple reussi. Reference : {$result['batch_reference']}. Total debite : {$result['total_debit']} Ar."
            );
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function history()
    {
        $user = $this->getCurrentUser();
        $transactions = $this->transactionModel->getUserHistory($user['id']);

        return view('client/history', ['transactions' => $transactions]);
    }

    private function getFeeRulesByCode(string $code): array
    {
        $operationType = $this->operationTypeModel->where('code', $code)->first();

        if (!$operationType) {
            return [];
        }

        return $this->feeRuleModel
            ->where('operation_type_id', $operationType['id'])
            ->orderBy('min_amount', 'ASC')
            ->findAll();
    }
}
