<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table = 'transactions';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id',
        'operation_type_id',
        'receiver_user_id',
        'receiver_phone',
        'receiver_operator_id',
        'amount',
        'base_fee',
        'promo_percent',
        'promo_amount',
        'base_fee_after_promo',
        'external_commission',
        'included_withdrawal_fee',
        'savings_percent',
        'savings_amount',
        'fee',
        'total_fee',
        'withdrawal_fee_included',
        'is_external',
        'batch_reference',
        'status',
        'created_at',
    ];
    protected $useTimestamps = false;
    protected $createdField = 'created_at';

    public function createTransferTransaction(array $data)
    {
        $payload = array_merge([
            'receiver_user_id' => null,
            'receiver_phone' => null,
            'receiver_operator_id' => null,
            'base_fee' => 0,
            'promo_percent' => 0,
            'promo_amount' => 0,
            'base_fee_after_promo' => 0,
            'external_commission' => 0,
            'included_withdrawal_fee' => 0,
            'savings_percent' => 0,
            'savings_amount' => 0,
            'fee' => 0,
            'total_fee' => 0,
            'withdrawal_fee_included' => 0,
            'is_external' => 0,
            'batch_reference' => null,
            'status' => 'completed',
        ], $data);

        $payload['fee'] = (int) ($payload['fee'] ?: $payload['total_fee'] ?: (
            (int) $payload['base_fee']
            + (int) $payload['external_commission']
            + (int) $payload['included_withdrawal_fee']
        ));
        $payload['total_fee'] = (int) ($payload['total_fee'] ?: $payload['fee']);

        return $this->insert($payload);
    }

    public function getUserHistory($userId)
    {
        return $this->select(
            'transactions.*,
             operation_types.name AS operation_type,
             operation_types.name AS operation_name,
             COALESCE(transactions.receiver_phone, receiver.phone_number) AS receiver_phone,
             receiver_operator.name AS receiver_operator_name,
             transactions.promo_percent,
             transactions.promo_amount,
             transactions.base_fee_after_promo,
             transactions.savings_percent,
             transactions.savings_amount',
            false
        )
            ->join('operation_types', 'operation_types.id = transactions.operation_type_id')
            ->join('users receiver', 'receiver.id = transactions.receiver_user_id', 'left')
            ->join('prefixes receiver_prefix', "receiver_prefix.prefix = SUBSTR(COALESCE(transactions.receiver_phone, receiver.phone_number), 1, 3)", 'left', false)
            ->join('operators receiver_operator', 'receiver_operator.id = COALESCE(transactions.receiver_operator_id, receiver_prefix.operator_id)', 'left', false)
            ->groupStart()
                ->where('transactions.user_id', $userId)
                ->orWhere('transactions.receiver_user_id', $userId)
            ->groupEnd()
            ->orderBy('transactions.created_at', 'DESC')
            ->findAll();
    }

    public function getUserStats($userId)
    {
        $deposits = $this->where('user_id', $userId)->where('operation_type_id', 1)->selectSum('amount')->get()->getRowArray();
        $withdrawals = $this->where('user_id', $userId)->where('operation_type_id', 2)->selectSum('amount')->get()->getRowArray();
        $transfersSent = $this->where('user_id', $userId)->where('operation_type_id', 3)->selectSum('amount')->get()->getRowArray();
        $transfersReceived = $this->where('receiver_user_id', $userId)->where('operation_type_id', 3)->selectSum('amount')->get()->getRowArray();
        $savings = $this->selectSum('savings_amount')
            ->where('receiver_user_id', $userId)
            ->where('operation_type_id', 3)
            ->get()
            ->getRowArray();

        return [
            'total_deposits' => $deposits['amount'] ?? 0,
            'total_withdrawals' => $withdrawals['amount'] ?? 0,
            'total_transfers_sent' => $transfersSent['amount'] ?? 0,
            'total_transfers_received' => $transfersReceived['amount'] ?? 0,
            'total_savings' => $savings['savings_amount'] ?? 0,
        ];
    }

    public function countAll()
    {
        return $this->countAllResults();
    }

    public function getTotalFees()
    {
        $result = $this->selectSum('fee')->get()->getRowArray();
        return $result['fee'] ?? 0;
    }

    public function getTotalByType($typeId)
    {
        $result = $this->where('operation_type_id', $typeId)->selectSum('amount')->get()->getRowArray();
        return $result['amount'] ?? 0;
    }

    public function getFeesByType($typeId)
    {
        $result = $this->where('operation_type_id', $typeId)->selectSum('fee')->get()->getRowArray();
        return $result['fee'] ?? 0;
    }

    public function getInternalTransferFees()
    {
        $result = $this->where('operation_type_id', 3)
            ->where('is_external', 0)
            ->selectSum('total_fee')
            ->get()
            ->getRowArray();

        return $result['total_fee'] ?? 0;
    }

    public function getExternalTransferBaseFees()
    {
        $result = $this->where('operation_type_id', 3)
            ->where('is_external', 1)
            ->selectSum('base_fee')
            ->get()
            ->getRowArray();

        return $result['base_fee'] ?? 0;
    }

    public function getExternalCommissions()
    {
        $result = $this->where('operation_type_id', 3)
            ->where('is_external', 1)
            ->selectSum('external_commission')
            ->get()
            ->getRowArray();

        return $result['external_commission'] ?? 0;
    }

    public function getAmountsByExternalOperator(): array
    {
        return $this->select(
            'operators.id AS operator_id,
             operators.name AS operator_name,
             SUM(transactions.amount + transactions.included_withdrawal_fee) AS total_amount,
             SUM(transactions.external_commission) AS total_commission,
             SUM(transactions.amount + transactions.included_withdrawal_fee + transactions.external_commission) AS total_to_settle',
            false
        )
            ->join('users receiver', 'receiver.id = transactions.receiver_user_id', 'left')
            ->join('prefixes receiver_prefix', "receiver_prefix.prefix = SUBSTR(COALESCE(transactions.receiver_phone, receiver.phone_number), 1, 3)", 'left', false)
            ->join('operators', 'operators.id = COALESCE(transactions.receiver_operator_id, receiver_prefix.operator_id)', 'left', false)
            ->where('transactions.operation_type_id', 3)
            ->where('transactions.is_external', 1)
            ->groupBy('operators.id')
            ->orderBy('operators.name', 'ASC')
            ->findAll();
    }

    public function getSettlementByExternalOperator(): array
    {
        return $this->select(
            'operators.id AS operator_id,
             operators.name AS operator_name,
             SUM(transactions.amount + transactions.included_withdrawal_fee) AS total_amount,
             SUM(transactions.external_commission) AS total_commission,
             SUM(transactions.amount + transactions.included_withdrawal_fee + transactions.external_commission) AS total_to_settle',
            false
        )
            ->join('users receiver', 'receiver.id = transactions.receiver_user_id', 'left')
            ->join('prefixes receiver_prefix', "receiver_prefix.prefix = SUBSTR(COALESCE(transactions.receiver_phone, receiver.phone_number), 1, 3)", 'left', false)
            ->join('operators', 'operators.id = COALESCE(transactions.receiver_operator_id, receiver_prefix.operator_id)', 'left', false)
            ->where('transactions.operation_type_id', 3)
            ->where('transactions.is_external', 1)
            ->groupBy('operators.id')
            ->orderBy('operators.name', 'ASC')
            ->findAll();
    }

    public function getTransactionsByBatch($batchReference): array
    {
        return $this->where('batch_reference', $batchReference)
            ->orderBy('created_at', 'ASC')
            ->findAll();
    }

    public function countTransfersByExternal($isExternal): int
    {
        return $this->where('operation_type_id', 3)
            ->where('is_external', $isExternal ? 1 : 0)
            ->countAllResults();
    }

    public function getFeesByOperatorType($isOwnOperator)
    {
        $result = $this->selectSum('transactions.total_fee')
            ->join('users receiver', 'receiver.id = transactions.receiver_user_id', 'left')
            ->join('prefixes receiver_prefix', "receiver_prefix.prefix = SUBSTR(COALESCE(transactions.receiver_phone, receiver.phone_number), 1, 3)", 'left', false)
            ->join('operators receiver_operator', 'receiver_operator.id = COALESCE(transactions.receiver_operator_id, receiver_prefix.operator_id)', 'left', false)
            ->where('transactions.operation_type_id', 3)
            ->where('transactions.is_external', $isOwnOperator ? 0 : 1)
            ->get()
            ->getRowArray();

        return $result['total_fee'] ?? 0;
    }

    public function getAmountsByOperator(): array
    {
        return $this->select(
            'prefixes.prefix,
             SUM(transactions.amount + transactions.included_withdrawal_fee) AS total_amount',
            false
        )
            ->join('users receiver', 'receiver.id = transactions.receiver_user_id', 'left')
            ->join('prefixes', "prefixes.prefix = SUBSTR(COALESCE(transactions.receiver_phone, receiver.phone_number), 1, 3)", 'left', false)
            ->where('transactions.operation_type_id', 3)
            ->groupBy('prefixes.prefix')
            ->orderBy('prefixes.prefix', 'ASC')
            ->findAll();
    }
}
