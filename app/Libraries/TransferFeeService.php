<?php

namespace App\Libraries;

use App\Models\FeeRuleModel;
use App\Models\PrefixModel;
use App\Models\OperatorSettingModel;

class TransferFeeService
{
    private FeeRuleModel $feeRuleModel;
    private PrefixModel $prefixModel;
    private OperatorSettingModel $operatorSettingModel;

    public function __construct()
    {
        $this->feeRuleModel = new FeeRuleModel();
        $this->prefixModel = new PrefixModel();
        $this->operatorSettingModel = new OperatorSettingModel();
    }

    public function calculate(int $amount, string $receiverPhone): array
    {
        $baseFee = $this->feeRuleModel->getFeeByCodeAndAmount('transfer', $amount);
        $operator = $this->prefixModel->findOperatorByPhone($receiverPhone);

        if (!$operator) {
            throw new \RuntimeException('Impossible d\'identifier l\'opérateur du destinataire.');
        }

        $commissionPercent = (float) ($operator['commission_percent'] ?? 0);
        $isExternal = (int) ($operator['is_own_operator'] ?? 0) === 0;
        $externalCommission = $isExternal
            ? (int) round(($baseFee * $commissionPercent) / 100)
            : 0;
        $promoPercent = $isExternal ? 0 : (float) $this->operatorSettingModel->getSameOperatorPromoPercent();
        $promoAmount = $promoPercent > 0
            ? (int) round(($baseFee * $promoPercent) / 100)
            : 0;
        $promoAmount = min($promoAmount, $baseFee);
        $baseFeeAfterPromo = $baseFee - $promoAmount;

        return [
            'base_fee' => $baseFee,
            'promo_percent' => $promoPercent,
            'promo_amount' => $promoAmount,
            'base_fee_after_promo' => $baseFeeAfterPromo,
            'commission_percent' => $isExternal ? $commissionPercent : 0,
            'external_commission' => $externalCommission,
            'total_transfer_fee' => $baseFeeAfterPromo + $externalCommission,
            'is_external' => $isExternal,
            'receiver_operator_id' => (int) ($operator['id'] ?? 0),
            'receiver_operator_name' => $operator['name'] ?? null,
        ];
    }
}
