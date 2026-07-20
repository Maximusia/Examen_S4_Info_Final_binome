<?php

namespace App\Controllers;

use App\Models\PrefixModel;
use App\Models\FeeRuleModel;
use App\Models\UserModel;
use App\Models\TransactionModel;
use App\Models\OperationTypeModel;

class OperatorController extends BaseController
{
    private $prefixModel;
    private $feeRuleModel;
    private $userModel;
    private $transactionModel;
    private $operationTypeModel;

    public function __construct()
    {
        $this->prefixModel = new PrefixModel();
        $this->feeRuleModel = new FeeRuleModel();
        $this->userModel = new UserModel();
        $this->transactionModel = new TransactionModel();
        $this->operationTypeModel = new OperationTypeModel();
    }

    public function dashboard()
    {
        $data = [
            'totalUsers'        => $this->userModel->countAll(),
            'totalTransactions' => $this->transactionModel->countAll(),
            'totalFees'         => $this->transactionModel->getTotalFees(),
            'totalDeposits'     => $this->transactionModel->getTotalByType(1),
            'totalWithdrawals'  => $this->transactionModel->getTotalByType(2),
            'totalTransfers'    => $this->transactionModel->getTotalByType(3),
        ];

        return view('operator/dashboard', $data);
    }

    // ---------- PREFIXES ----------
    public function prefixes()
    {
        $prefixes = $this->prefixModel->findAll();
        return view('operator/prefixes', ['prefixes' => $prefixes]);
    }

    public function addPrefix()
    {
        $prefix = trim($this->request->getPost('prefix') ?? '');
        $prefix = preg_replace('/[^0-9]/', '', $prefix);

        if (empty($prefix) || strlen($prefix) < 2) {
            return redirect()->back()->with('error', 'Préfixe invalide (minimum 2 chiffres).');
        }

        $this->prefixModel->insert(['prefix' => $prefix]);
        return redirect()->to('/admin/prefixes')->with('success', "Préfixe {$prefix} ajouté.");
    }

    public function deletePrefix($id)
    {
        $this->prefixModel->delete($id);
        return redirect()->to('/admin/prefixes')->with('success', 'Préfixe supprimé.');
    }

    // ---------- FRAIS ----------
    public function fees()
    {
        $operationTypes = $this->operationTypeModel->findAll();
        $fees = [];

        foreach ($operationTypes as $type) {
            if ($type['code'] === 'retrait' || $type['code'] === 'transfer') {
                $fees[$type['code']] = $this->feeRuleModel
                    ->where('operation_type_id', $type['id'])
                    ->orderBy('min_amount', 'ASC')
                    ->findAll();
                $fees[$type['code'] . '_type_id'] = $type['id'];
            }
        }

        return view('operator/fees', ['fees' => $fees]);
    }

    public function updateFee($id)
    {
        $newFee = (int) $this->request->getPost('fee');

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
}