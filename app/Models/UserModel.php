<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['phone_number', 'balance', 'savings_balance', 'savings_percent'];
    protected $useTimestamps = false;

    public function findByPhone($phone): ?array
    {
        $user = $this->where('phone_number', trim((string) $phone))->first();
        return $user ?: null;
    }

    public function updateBalance($userId, $newBalance): bool
    {
        if (!is_numeric($newBalance) || $newBalance < 0) {
            return false;
        }

        return (bool) $this->update($userId, ['balance' => (int) $newBalance]);
    }

    public function updateSavingsPreference($userId, $percent): bool
    {
        if (!is_numeric($percent)) {
            return false;
        }

        $percent = (int) round((float) $percent);
        if ($percent < 0 || $percent > 100) {
            return false;
        }

        return (bool) $this->update($userId, ['savings_percent' => $percent]);
    }

    public function creditSavings($userId, $amount): bool
    {
        $amount = (int) $amount;
        if ($amount <= 0) {
            return true;
        }

        $builder = $this->builder();
        $builder->set('savings_balance', "COALESCE(savings_balance, 0) + {$amount}", false)
            ->where('id', $userId);

        $builder->update();
        return $this->db->affectedRows() > 0;
    }

    public function debit($userId, $amount): bool
    {
        $amount = (int) $amount;
        if ($amount <= 0) {
            return false;
        }

        $builder = $this->builder();
        $builder->set('balance', "balance - {$amount}", false)
            ->where('id', $userId)
            ->where('balance >=', $amount);

        $builder->update();
        return $this->db->affectedRows() > 0;
    }

    public function credit($userId, $amount): bool
    {
        $amount = (int) $amount;
        if ($amount <= 0) {
            return false;
        }

        $builder = $this->builder();
        $builder->set('balance', "balance + {$amount}", false)
            ->where('id', $userId);

        $builder->update();
        return $this->db->affectedRows() > 0;
    }

    public function countAll()
    {
        return $this->countAllResults();
    }
}
