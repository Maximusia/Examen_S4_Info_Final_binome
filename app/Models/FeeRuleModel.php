<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\OperationTypeModel;

class FeeRuleModel extends Model
{
    protected $table = 'fee_rules';
    protected $primaryKey = 'id';
    protected $allowedFields = ['operation_type_id', 'min_amount', 'max_amount', 'fee'];
    protected $useTimestamps = false;

    public function getFeeByCodeAndAmount(string $code, int $amount): int
    {
        $operationTypeModel = new OperationTypeModel();
        $operationType = $operationTypeModel->where('code', $code)->first();

        if (!$operationType) {
            throw new \InvalidArgumentException("Unknown operation code: {$code}");
        }

        $rule = $this->where('operation_type_id', $operationType['id'])
            ->where('min_amount <=', $amount)
            ->where('max_amount >=', $amount)
            ->first();

        if (!$rule) {
            throw new \RuntimeException("No fee rule matches amount {$amount} for code {$code}");
        }

        return (int) $rule['fee'];
    }
}
