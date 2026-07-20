<?php

namespace App\Models;

use CodeIgniter\Model;

class PrefixModel extends Model
{
    protected $table = 'prefixes';
    protected $primaryKey = 'id';
    protected $allowedFields = ['operator_id', 'prefix'];
    protected $useTimestamps = false;

    public function getPrefixesByOperator($operatorId): array
    {
        return $this->where('operator_id', $operatorId)
            ->orderBy('prefix', 'ASC')
            ->findAll();
    }

    public function getOwnOperatorPrefixes(): array
    {
        $operatorModel = new OperatorModel();
        $ownOperator = $operatorModel->getOwnOperator();

        if (!$ownOperator) {
            return [];
        }

        return $this->getPrefixesByOperator($ownOperator['id']);
    }

    public function getExternalOperatorPrefixes(): array
    {
        return $this->select('prefixes.*')
            ->join('operators', 'operators.id = prefixes.operator_id')
            ->where('operators.is_own_operator', 0)
            ->orderBy('prefixes.prefix', 'ASC')
            ->findAll();
    }

    public function findPrefixByPhone($phone): ?array
    {
        $phone = $this->normalizePhone($phone);
        if ($phone === null) {
            return null;
        }

        $prefix = substr($phone, 0, 3);
        if (!$this->prefixExists($prefix)) {
            return null;
        }

        return $this->where('prefix', $prefix)->first() ?: null;
    }

    public function findOperatorByPhone($phone): ?array
    {
        $prefix = $this->findPrefixByPhone($phone);
        if (!$prefix) {
            return null;
        }

        $operatorModel = new OperatorModel();
        return $operatorModel->findOperatorById($prefix['operator_id']);
    }

    public function isOwnOperatorPhone($phone): bool
    {
        $operator = $this->findOperatorByPhone($phone);
        return $operator !== null && (int) ($operator['is_own_operator'] ?? 0) === 1;
    }

    public function isExternalOperatorPhone($phone): bool
    {
        $operator = $this->findOperatorByPhone($phone);
        return $operator !== null && (int) ($operator['is_own_operator'] ?? 0) === 0;
    }

    public function prefixExists($prefix): bool
    {
        $prefix = trim((string) $prefix);
        return $prefix !== '' && (bool) $this->where('prefix', $prefix)->first();
    }

    public function addPrefixToOperator($operatorId, $prefix)
    {
        $prefix = trim((string) $prefix);
        if (!$this->isValidThreeDigitPrefix($prefix) || $this->prefixExists($prefix)) {
            return false;
        }

        return $this->insert([
            'operator_id' => (int) $operatorId,
            'prefix' => $prefix,
        ]);
    }

    public function deletePrefix($id): bool
    {
        return (bool) $this->delete($id);
    }

    public function isValidPrefix($phone)
    {
        return $this->findPrefixByPhone($phone) !== null;
    }

    public function getOperatorPrefixes(): array
    {
        return $this->getOwnOperatorPrefixes();
    }

    public function getOtherOperatorPrefixes(): array
    {
        return $this->getExternalOperatorPrefixes();
    }

    public function isOtherOperator($phone): bool
    {
        return $this->isExternalOperatorPhone($phone);
    }

    public function isOwnOperator($phone): bool
    {
        return $this->isOwnOperatorPhone($phone);
    }

    private function normalizePhone($phone): ?string
    {
        $phone = preg_replace('/\D+/', '', (string) $phone);
        return $phone !== '' ? $phone : null;
    }

    private function isValidThreeDigitPrefix(string $prefix): bool
    {
        return (bool) preg_match('/^\d{3}$/', $prefix);
    }
}
