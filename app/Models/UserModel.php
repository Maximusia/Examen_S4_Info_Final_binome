<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['phone_number', 'balance'];
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
