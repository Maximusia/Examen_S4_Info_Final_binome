<?php

use App\Libraries\FeeService;
use App\Libraries\TransferFeeService;

if (!function_exists('calculate_fee')) {
    function calculate_fee($operationCode, $amount)
    {
        $feeService = new FeeService();
        return $feeService->calculate($operationCode, $amount);
    }
}

if (!function_exists('calculate_transfer_fee')) {
    function calculate_transfer_fee($amount, $receiverPhone)
    {
        $service = new TransferFeeService();
        $details = $service->calculate((int) $amount, (string) $receiverPhone);
        return $details['total_transfer_fee'];
    }
}
