<?php

namespace App\Controllers;

use App\Models\PrefixModel;
use App\Models\OperatorModel;
use App\Models\FeeRuleModel;
use App\Models\UserModel;
use App\Models\TransactionModel;
use App\Models\OperationTypeModel;

class OperatorController extends BaseController
{
    private $prefixModel;
    private $operatorModel;
    private $feeRuleModel;
    private $userModel;
    private $transactionModel;
    private $operationTypeModel;

    public function __construct()
    {
        $this->prefixModel = new PrefixModel();
        $this->operatorModel = new OperatorModel();
        $this->feeRuleModel = new FeeRuleModel();
        $this->userModel = new UserModel();
        $this->transactionModel = new TransactionModel();
        $this->operationTypeModel = new OperationTypeModel();
    }

    public function dashboard()
    {
        $withdrawalFees = $this->getFeeRulesByCode('retrait');
        $transferFees = $this->getFeeRulesByCode('transfer');
        $totalDeposits = $this->transactionModel->getTotalByType(1);
        $totalWithdrawals = $this->transactionModel->getTotalByType(2);
        $totalTransfers = $this->transactionModel->getTotalByType(3);

        $data = [
            'total_users'         => $this->userModel->countAll(),
            'total_transactions'  => $this->transactionModel->countAll(),
            'total_fees'          => $this->transactionModel->getTotalFees(),
            'total_prefixes'      => $this->prefixModel->countAll(),
            'total_fee_rules'     => $this->feeRuleModel->countAll(),
            'withdrawals_count'   => count($withdrawalFees),
            'transfers_count'     => count($transferFees),
            'deposits_volume'     => $totalDeposits,
            'withdrawals_volume'  => $totalWithdrawals,
            'transfers_volume'    => $totalTransfers,
            'total_volume'        => $totalDeposits + $totalWithdrawals + $totalTransfers,
        ];

        return view('operator/dashboard', $data);
    }

    // ---------- PREFIXES ----------
    public function prefixes()
    {
        $prefixes = $this->prefixModel->getOwnOperatorPrefixes();

        return view('operator/prefixes', [
            'prefixes' => $prefixes,
            'total_prefixes' => count($prefixes),
            'active_prefixes' => count($prefixes),
        ]);
    }

    public function addPrefix()
    {
        $prefix = trim($this->request->getPost('prefix') ?? '');
        $prefix = preg_replace('/[^0-9]/', '', $prefix);

        if (!$this->isValidPrefixFormat($prefix)) {
            return redirect()->back()->withInput()->with('error', 'Préfixe invalide. Utilisez 3 chiffres comme 033 ou 037.');
        }

        if ($this->prefixModel->where('prefix', $prefix)->first()) {
            return redirect()->back()->withInput()->with('error', 'Ce préfixe existe déjà.');
        }

        $ownOperator = $this->operatorModel->getOwnOperator();
        if (!$ownOperator) {
            return redirect()->back()->withInput()->with('error', 'Opérateur principal introuvable.');
        }

        if (!$this->prefixModel->addPrefixToOperator($ownOperator['id'], $prefix)) {
            return redirect()->back()->withInput()->with('error', 'Impossible d\'ajouter ce préfixe.');
        }
        return redirect()->to('/admin/prefixes')->with('success', "Préfixe {$prefix} ajouté.");
    }

    public function updatePrefix($id)
    {
        $prefix = trim($this->request->getPost('prefix') ?? '');
        $prefix = preg_replace('/[^0-9]/', '', $prefix);
        $currentPrefix = $this->prefixModel->find($id);

        if (!$currentPrefix) {
            return redirect()->to('/admin/prefixes')->with('error', 'Préfixe introuvable.');
        }

        if (!$this->isValidPrefixFormat($prefix)) {
            return redirect()->back()->withInput()->with('error', 'Préfixe invalide. Utilisez 3 chiffres comme 033 ou 037.');
        }

        if ($this->prefixModel
            ->where('prefix', $prefix)
            ->where('id !=', $id)
            ->first()) {
            return redirect()->back()->withInput()->with('error', 'Ce préfixe existe déjà.');
        }

        $currentPrefix = $this->prefixModel->find($id);
        $ownOperator = $this->operatorModel->getOwnOperator();
        if (!$ownOperator) {
            return redirect()->back()->withInput()->with('error', 'Opérateur principal introuvable.');
        }

        if (!$this->prefixModel->update($id, [
            'prefix' => $prefix,
            'operator_id' => $currentPrefix['operator_id'] ?? $ownOperator['id'],
        ])) {
            return redirect()->back()->withInput()->with('error', 'Impossible de mettre à jour ce préfixe.');
        }

        return redirect()->to('/admin/prefixes')->with('success', "Préfixe {$prefix} mis à jour.");
    }

    public function deletePrefix($id)
    {
        if (!$this->prefixModel->find($id)) {
            return redirect()->to('/admin/prefixes')->with('error', 'Préfixe introuvable.');
        }

        $this->prefixModel->deletePrefix($id);
        return redirect()->to('/admin/prefixes')->with('success', 'Préfixe supprimé.');
    }

    // ---------- FRAIS ----------
    public function fees()
    {
        $withdrawalFees = $this->getFeeRulesByCode('retrait');
        $transferFees = $this->getFeeRulesByCode('transfer');

        return view('operator/fees', [
            'withdrawal_fees' => $withdrawalFees,
            'transfer_fees' => $transferFees,
        ]);
    }

    public function updateFee($id)
    {
        $newFee = (int) $this->request->getPost('fee');
        $feeRule = $this->feeRuleModel->find($id);

        if (!$feeRule) {
            return redirect()->to('/admin/fees')->with('error', 'Barème introuvable.');
        }

        if ($newFee < 0) {
            return redirect()->back()->with('error', 'Les frais ne peuvent pas être négatifs.');
        }

        $this->feeRuleModel->update($id, ['fee' => $newFee]);
        return redirect()->to('/admin/fees')->with('success', 'Frais mis à jour.');
    }

    // ---------- STATISTIQUES ----------
    public function statistics()
    {
        // Redirige vers le dashboard ou affiche une vue détaillée
        return redirect()->to('/admin/dashboard');
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

    private function isValidPrefixFormat(string $prefix): bool
    {
        return (bool) preg_match('/^0\d{2}$/', $prefix);
    }
}
