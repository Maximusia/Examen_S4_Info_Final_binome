<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table = 'transactions';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id', 'operation_type_id', 'amount', 'fee',
        'receiver_user_id', 'status', 'created_at'
    ];
    protected $useTimestamps = false;
    protected $createdField = 'created_at';

    public function getUserHistory($userId)
    {
        return $this->select('transactions.*, operation_types.name as operation_name')
                    ->join('operation_types', 'operation_types.id = transactions.operation_type_id')
                    ->where('transactions.user_id', $userId)
                    ->orWhere('transactions.receiver_user_id', $userId)
                    ->orderBy('transactions.created_at', 'DESC')
                    ->findAll();
    }

    public function getUserStats($userId)
    {
        $deposits = $this->where('user_id', $userId)->where('operation_type_id', 1)->selectSum('amount')->get()->getRowArray();
        $withdrawals = $this->where('user_id', $userId)->where('operation_type_id', 2)->selectSum('amount')->get()->getRowArray();
        $transfersSent = $this->where('user_id', $userId)->where('operation_type_id', 3)->selectSum('amount')->get()->getRowArray();
        $transfersReceived = $this->where('receiver_user_id', $userId)->where('operation_type_id', 3)->selectSum('amount')->get()->getRowArray();

        return [
            'total_deposits' => $deposits['amount'] ?? 0,
            'total_withdrawals' => $withdrawals['amount'] ?? 0,
            'total_transfers_sent' => $transfersSent['amount'] ?? 0,
            'total_transfers_received' => $transfersReceived['amount'] ?? 0,
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

    public function getFeesByOperatorType($isOwnOperator)
    {
        $result = $this->selectSum('transactions.fee')
            ->join('users receiver', 'receiver.id = transactions.receiver_user_id', 'inner')
            ->join('prefixes', "prefixes.prefix = SUBSTR(receiver.phone_number, 1, 3)", 'inner', false)
            ->where('transactions.operation_type_id', 3)
            ->where('prefixes.is_operator', $isOwnOperator ? 1 : 0)
            ->get()
            ->getRowArray();

        return $result['fee'] ?? 0;
    }

    public function getAmountsByOperator(): array
    {
        return $this->select('prefixes.prefix, SUM(transactions.amount) AS total_amount', false)
            ->join('users receiver', 'receiver.id = transactions.receiver_user_id', 'inner')
            ->join('prefixes', "prefixes.prefix = SUBSTR(receiver.phone_number, 1, 3)", 'inner', false)
            ->where('transactions.operation_type_id', 3)
            ->groupBy('prefixes.prefix')
            ->orderBy('prefixes.prefix', 'ASC')
            ->findAll();
    }
}


