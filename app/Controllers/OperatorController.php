<?php

namespace App\Controllers;

use App\Models\FeeRuleModel;
use App\Models\OperationTypeModel;
use App\Models\OperatorModel;
use App\Models\OperatorSettingModel;
use App\Models\PrefixModel;
use App\Models\TransactionModel;
use App\Models\UserModel;

class OperatorController extends BaseController
{
    private PrefixModel $prefixModel;
    private OperatorModel $operatorModel;
    private FeeRuleModel $feeRuleModel;
    private OperatorSettingModel $operatorSettingModel;
    private UserModel $userModel;
    private TransactionModel $transactionModel;
    private OperationTypeModel $operationTypeModel;

    public function __construct()
    {
        $this->prefixModel = new PrefixModel();
        $this->operatorModel = new OperatorModel();
        $this->feeRuleModel = new FeeRuleModel();
        $this->operatorSettingModel = new OperatorSettingModel();
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

        return view('operator/dashboard', [
            'total_users' => $this->userModel->countAll(),
            'total_transactions' => $this->transactionModel->countAll(),
            'total_fees' => $this->transactionModel->getTotalFees(),
            'total_prefixes' => $this->prefixModel->countAll(),
            'total_fee_rules' => $this->feeRuleModel->countAll(),
            'withdrawals_count' => count($withdrawalFees),
            'transfers_count' => count($transferFees),
            'deposits_volume' => $totalDeposits,
            'withdrawals_volume' => $totalWithdrawals,
            'transfers_volume' => $totalTransfers,
            'total_volume' => $totalDeposits + $totalWithdrawals + $totalTransfers,
        ]);
    }

    public function operators()
    {
        $ownOperator = $this->operatorModel->getOwnOperator();
        $externalOperators = $this->operatorModel->getExternalOperators();

        return view('operator/operators', [
            'own_operator' => $ownOperator,
            'external_operators' => $externalOperators,
            'total_operators' => ($ownOperator ? 1 : 0) + count($externalOperators),
            'external_operator_count' => count($externalOperators),
        ]);
    }

    public function addOperator()
    {
        $name = trim((string) ($this->request->getPost('name') ?? ''));
        $commissionPercent = $this->request->getPost('commission_percent');

        if ($name === '') {
            return redirect()->back()->withInput()->with('error', 'Le nom de l\'operateur est obligatoire.');
        }

        if (!is_numeric($commissionPercent) || (float) $commissionPercent < 0) {
            return redirect()->back()->withInput()->with('error', 'Le pourcentage de commission doit etre positif ou nul.');
        }

        if ($this->operatorModel->where('name', $name)->first()) {
            return redirect()->back()->withInput()->with('error', 'Cet operateur existe deja.');
        }

        $id = $this->operatorModel->createExternalOperator([
            'name' => $name,
            'commission_percent' => $commissionPercent,
        ]);

        if (!$id) {
            return redirect()->back()->withInput()->with('error', 'Impossible de creer cet operateur.');
        }

        return redirect()->to('/admin/operators')->with('success', 'Operateur externe ajoute.');
    }

    public function updateOperator($id)
    {
        $name = trim((string) ($this->request->getPost('name') ?? ''));
        $commissionPercent = $this->request->getPost('commission_percent');

        if ($name === '') {
            return redirect()->back()->withInput()->with('error', 'Le nom de l\'operateur est obligatoire.');
        }

        if (!is_numeric($commissionPercent) || (float) $commissionPercent < 0) {
            return redirect()->back()->withInput()->with('error', 'Le pourcentage de commission doit etre positif ou nul.');
        }

        if ($this->operatorModel->where('name', $name)->where('id !=', $id)->first()) {
            return redirect()->back()->withInput()->with('error', 'Ce nom est deja utilise.');
        }

        if (!$this->operatorModel->updateExternalOperator($id, [
            'name' => $name,
            'commission_percent' => $commissionPercent,
        ])) {
            return redirect()->back()->withInput()->with('error', 'Impossible de mettre a jour cet operateur.');
        }

        return redirect()->to('/admin/operators')->with('success', 'Operateur mis a jour.');
    }

    public function deleteOperator($id)
    {
        $operator = $this->operatorModel->findOperatorById($id);

        if (!$operator) {
            return redirect()->to('/admin/operators')->with('error', 'Operateur introuvable.');
        }

        if ((int) ($operator['is_own_operator'] ?? 0) === 1) {
            return redirect()->to('/admin/operators')->with('error', 'Impossible de supprimer notre propre operateur.');
        }

        $transactionCount = $this->transactionModel
            ->where('receiver_operator_id', $id)
            ->countAllResults();

        if ($transactionCount > 0) {
            return redirect()->to('/admin/operators')->with('error', 'Cet operateur est reference par des transactions.');
        }

        if (!$this->operatorModel->deleteExternalOperator($id)) {
            return redirect()->to('/admin/operators')->with('error', 'Suppression impossible.');
        }

        return redirect()->to('/admin/operators')->with('success', 'Operateur externe supprime.');
    }

    public function prefixes()
    {
        $operators = $this->operatorModel->findAll();
        $groupedPrefixes = $this->groupPrefixesByOperator($operators);
        $ownOperator = $this->operatorModel->getOwnOperator();
        $ownPrefixCount = $ownOperator ? count($this->prefixModel->getPrefixesByOperator($ownOperator['id'])) : 0;

        return view('operator/prefixes', [
            'operators' => $operators,
            'grouped_prefixes' => $groupedPrefixes,
            'own_operator' => $ownOperator,
            'own_prefix_count' => $ownPrefixCount,
            'total_prefixes' => $this->prefixModel->countAll(),
        ]);
    }

    public function addPrefix()
    {
        $prefix = trim((string) ($this->request->getPost('prefix') ?? ''));
        $operatorId = (int) ($this->request->getPost('operator_id') ?: 0);

        if (!$this->isValidPrefixFormat($prefix)) {
            return redirect()->back()->withInput()->with('error', 'Prefixe invalide. Utilisez 3 chiffres.');
        }

        $operator = $this->operatorModel->findOperatorById($operatorId);
        if (!$operator) {
            return redirect()->back()->withInput()->with('error', 'Operateur introuvable.');
        }

        if (!$this->prefixModel->addPrefixToOperator($operatorId, $prefix)) {
            return redirect()->back()->withInput()->with('error', 'Ce prefixe existe deja ou ne peut pas etre ajoute.');
        }

        return redirect()->to('/admin/prefixes')->with('success', 'Prefixe ajoute.');
    }

    public function updatePrefix($id)
    {
        $currentPrefix = $this->prefixModel->find($id);
        if (!$currentPrefix) {
            return redirect()->to('/admin/prefixes')->with('error', 'Prefixe introuvable.');
        }

        $prefix = trim((string) ($this->request->getPost('prefix') ?? ''));
        $operatorId = (int) ($this->request->getPost('operator_id') ?: ($currentPrefix['operator_id'] ?? 0));

        if (!$this->isValidPrefixFormat($prefix)) {
            return redirect()->back()->withInput()->with('error', 'Prefixe invalide. Utilisez 3 chiffres.');
        }

        if (!$this->operatorModel->findOperatorById($operatorId)) {
            return redirect()->back()->withInput()->with('error', 'Operateur introuvable.');
        }

        if ($this->prefixModel->where('prefix', $prefix)->where('id !=', $id)->first()) {
            return redirect()->back()->withInput()->with('error', 'Ce prefixe existe deja.');
        }

        if (!$this->prefixModel->update($id, [
            'prefix' => $prefix,
            'operator_id' => $operatorId,
        ])) {
            return redirect()->back()->withInput()->with('error', 'Impossible de mettre a jour ce prefixe.');
        }

        return redirect()->to('/admin/prefixes')->with('success', 'Prefixe mis a jour.');
    }

    public function deletePrefix($id)
    {
        $prefix = $this->prefixModel->find($id);
        if (!$prefix) {
            return redirect()->to('/admin/prefixes')->with('error', 'Prefixe introuvable.');
        }

        $ownOperator = $this->operatorModel->getOwnOperator();
        if ($ownOperator && (int) $prefix['operator_id'] === (int) $ownOperator['id']) {
            $ownPrefixCount = count($this->prefixModel->getPrefixesByOperator($ownOperator['id']));
            if ($ownPrefixCount <= 1) {
                return redirect()->to('/admin/prefixes')->with('error', 'Impossible de supprimer le dernier prefixe interne.');
            }
        }

        if (!$this->prefixModel->deletePrefix($id)) {
            return redirect()->to('/admin/prefixes')->with('error', 'Suppression impossible.');
        }

        return redirect()->to('/admin/prefixes')->with('success', 'Prefixe supprime.');
    }

    public function fees()
    {
        return view('operator/fees', [
            'withdrawal_fees' => $this->getFeeRulesByCode('retrait'),
            'transfer_fees' => $this->getFeeRulesByCode('transfer'),
            'other_operator_commission_percent' => $this->operatorSettingModel->getOtherOperatorCommissionPercent(),
            'same_operator_promo_percent' => $this->operatorSettingModel->getSameOperatorPromoPercent(),
        ]);
    }

    public function updateTransferSettings()
    {
        $otherOperatorCommissionPercent = $this->request->getPost('other_operator_commission_percent');
        $sameOperatorPromoPercent = $this->request->getPost('same_operator_promo_percent');

        if (!is_numeric($otherOperatorCommissionPercent) || (float) $otherOperatorCommissionPercent < 0) {
            return redirect()->back()->withInput()->with('error', 'La commission externe doit etre positive ou nulle.');
        }

        if (!is_numeric($sameOperatorPromoPercent) || (float) $sameOperatorPromoPercent < 0 || (float) $sameOperatorPromoPercent > 100) {
            return redirect()->back()->withInput()->with('error', 'La promotion interne doit etre comprise entre 0 et 100.');
        }

        if (!$this->operatorSettingModel->setOtherOperatorCommissionPercent($otherOperatorCommissionPercent)
            || !$this->operatorSettingModel->setSameOperatorPromoPercent($sameOperatorPromoPercent)) {
            return redirect()->back()->withInput()->with('error', 'Impossible de mettre a jour les parametres de transfert.');
        }

        return redirect()->to('/admin/fees')->with('success', 'Parametres de transfert mis a jour.');
    }

    public function updateFee($id)
    {
        $newFee = (int) $this->request->getPost('fee');
        $feeRule = $this->feeRuleModel->find($id);

        if (!$feeRule) {
            return redirect()->to('/admin/fees')->with('error', 'Bareme introuvable.');
        }

        if ($newFee < 0) {
            return redirect()->back()->with('error', 'Les frais ne peuvent pas etre negatifs.');
        }

        $this->feeRuleModel->update($id, ['fee' => $newFee]);
        return redirect()->to('/admin/fees')->with('success', 'Frais mis a jour.');
    }

    public function statistics()
    {
        $internalTransferFees = $this->transactionModel->getInternalTransferFees();
        $withdrawalFees = $this->transactionModel->getFeesByType(2);
        $externalTransferBaseFees = $this->transactionModel->getExternalTransferBaseFees();
        $externalCommissions = $this->transactionModel->getExternalCommissions();
        $amountsByOperator = $this->transactionModel->getAmountsByExternalOperator();
        $settlementByOperator = $this->transactionModel->getSettlementByExternalOperator();
        $externalTransfersCount = $this->transactionModel->countTransfersByExternal(true);
        $internalTransfersCount = $this->transactionModel->countTransfersByExternal(false);

        return view('operator/statistics', [
            'withdrawal_fees' => $withdrawalFees,
            'internal_transfer_fees' => $internalTransferFees,
            'external_transfer_base_fees' => $externalTransferBaseFees,
            'external_commissions' => $externalCommissions,
            'amounts_by_operator' => $amountsByOperator,
            'settlement_by_operator' => $settlementByOperator,
            'external_transfers_count' => $externalTransfersCount,
            'internal_transfers_count' => $internalTransfersCount,
        ]);
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
        return (bool) preg_match('/^\d{3}$/', $prefix);
    }

    /**
     * @param array<int, array<string, mixed>> $operators
     * @return array<int, array<int, array<string, mixed>>>
     */
    private function groupPrefixesByOperator(array $operators): array
    {
        $grouped = [];

        foreach ($operators as $operator) {
            $grouped[$operator['id']] = [];
        }

        $prefixes = $this->prefixModel->orderBy('prefix', 'ASC')->findAll();
        foreach ($prefixes as $prefix) {
            $operatorId = (int) $prefix['operator_id'];
            $grouped[$operatorId][] = $prefix;
        }

        return $grouped;
    }
}
