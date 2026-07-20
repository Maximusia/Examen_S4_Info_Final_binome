<?php

use App\Libraries\FeeService;

if (!function_exists('calculate_fee')) {
    function calculate_fee($operationCode, $amount)
    {
        $feeService = new FeeService();
        return $feeService->calculate($operationCode, $amount);
    }
}