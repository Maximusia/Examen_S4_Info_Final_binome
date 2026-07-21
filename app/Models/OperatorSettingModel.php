<?php

namespace App\Models;

use CodeIgniter\Model;

class OperatorSettingModel extends Model
{
    protected $table = 'operator_settings';
    protected $primaryKey = 'id';
    protected $allowedFields = ['key', 'value'];
    protected $useTimestamps = false;

    public function getOtherOperatorCommissionPercent(): int
    {
        $setting = $this->where('key', 'other_operator_commission_percent')->first();

        if (!$setting || !isset($setting['value']) || $setting['value'] === '') {
            return 2;
        }

        return (int) $setting['value'];
    }

    public function setOtherOperatorCommissionPercent($value): bool
    {
        $payload = [
            'key'   => 'other_operator_commission_percent',
            'value' => (string) $value,
        ];

        $existing = $this->where('key', $payload['key'])->first();

        if ($existing) {
            return (bool) $this->update($existing['id'], $payload);
        }

        return (bool) $this->insert($payload);
    }
    public function getSameOperatorPromoPercent(): float
    {
        $setting = $this->where('key', 'same_operator_promo_percent')->first();

        if (!$setting || !isset($setting['value']) || $setting['value'] === '') {
            return 0;
        }

        return (float) $setting['value'];
    }

    public function setSameOperatorPromoPercent($value): bool
    {
        $payload = [
            'key' => 'same_operator_promo_percent',
            'value' => (string) $value,
        ];

        $existing = $this->where('key', $payload['key'])->first();
        if ($existing) {
            return (bool) $this->update($existing['id'], $payload);
        }

        return (bool) $this->insert($payload);
    }
}
