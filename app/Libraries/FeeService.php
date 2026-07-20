<?php

namespace App\Libraries;

use App\Models\FeeRuleModel;

class FeeService
{
    private $feeRuleModel;

    public function __construct()
    {
        $this->feeRuleModel = new FeeRuleModel();
    }

    public function calculate($operationCode, $amount)
    {
        return $this->feeRuleModel->getFeeByCodeAndAmount($operationCode, (int) $amount);
    }
}
