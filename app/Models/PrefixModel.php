<?php

namespace App\Models;

use CodeIgniter\Model;

class PrefixModel extends Model
{
    protected $table = 'prefixes';
    protected $primaryKey = 'id';
    protected $allowedFields = ['prefix', 'is_operator'];
    protected $useTimestamps = false;

    public function isValidPrefix($phone)
    {
        return $this->getMatchingPrefix($phone) !== null;
    }

    public function getOperatorPrefixes(): array
    {
        return $this->where('is_operator', 1)
            ->orderBy('prefix', 'ASC')
            ->findAll();
    }

    public function getOtherOperatorPrefixes(): array
    {
        return $this->where('is_operator', 0)
            ->orderBy('prefix', 'ASC')
            ->findAll();
    }

    public function isOtherOperator($phone): bool
    {
        $prefix = $this->getMatchingPrefix($phone);

        return $prefix !== null && (int) $prefix['is_operator'] === 0;
    }

    public function isOwnOperator($phone): bool
    {
        $prefix = $this->getMatchingPrefix($phone);

        return $prefix !== null && (int) $prefix['is_operator'] === 1;
    }

    private function getMatchingPrefix($phone): ?array
    {
        $phone = trim((string) $phone);

        if ($phone === '') {
            return null;
        }

        $prefixes = $this->orderBy('LENGTH(prefix)', 'DESC', false)
            ->orderBy('prefix', 'ASC')
            ->findAll();

        foreach ($prefixes as $prefix) {
            if (strpos($phone, $prefix['prefix']) === 0) {
                return $prefix;
            }
        }

        return null;
    }
}
