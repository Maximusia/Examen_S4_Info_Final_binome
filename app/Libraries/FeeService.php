<?php

namespace App\Libraries;

use App\Models\OperationTypeModel;
use App\Models\FeeRuleModel;

class FeeService
{
    private $operationTypeModel;
    private $feeRuleModel;

    public function __construct()
    {
        $this->operationTypeModel = new OperationTypeModel();
        $this->feeRuleModel = new FeeRuleModel();
    }

    public function calculate($operationCode, $amount)
    {
        $type = $this->operationTypeModel->where('code', $operationCode)->first();
        if (!$type) {
            return 0;
        }

        $rule = $this->feeRuleModel
            ->where('operation_type_id', $type['id'])
            ->where('min_amount <=', $amount)
            ->where('max_amount >=', $amount)
            ->first();

        return $rule ? $rule['fee'] : 0;
    }
}