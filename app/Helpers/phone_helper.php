<?php

if (!function_exists('standardize_phone')) {
    function standardize_phone($phoneNumber)
    {
        return preg_replace('/[^0-9]/', '', $phoneNumber);
    }
}

if (!function_exists('validate_mg_phone')) {
    function validate_mg_phone($phoneNumber)
    {
        $phoneNumber = standardize_phone($phoneNumber);
        
        if (strlen($phoneNumber) !== 10) {
            return false;
        }
        
        if ($phoneNumber[0] !== '0') {
            return false;
        }
        
        $validPrefixes = ['020', '030', '031', '032', '033', '034', '037', '038', '039', '050'];
        $prefix = substr($phoneNumber, 0, 3);
        
        return in_array($prefix, $validPrefixes);
    }
}