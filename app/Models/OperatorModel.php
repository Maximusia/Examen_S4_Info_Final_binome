<?php

namespace App\Models;

use CodeIgniter\Model;

class OperatorModel extends Model
{
    protected $table = 'operators';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name',
        'is_own_operator',
        'commission_percent',
    ];
    protected $useTimestamps = false;

    public function getOwnOperator(): ?array
    {
        return $this->where('is_own_operator', 1)->first();
    }

    public function getExternalOperators(): array
    {
        return $this->where('is_own_operator', 0)
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    public function findOperatorById($id): ?array
    {
        $operator = $this->find($id);
        return $operator ?: null;
    }

    public function createExternalOperator(array $data)
    {
        $payload = $this->normalizeExternalOperatorData($data);

        if ($payload === null) {
            return false;
        }

        return $this->insert($payload);
    }

    public function updateExternalOperator($id, array $data): bool
    {
        $operator = $this->findOperatorById($id);
        if (!$operator || (int) ($operator['is_own_operator'] ?? 0) === 1) {
            return false;
        }

        $payload = $this->normalizeExternalOperatorData($data);
        if ($payload === null) {
            return false;
        }

        return (bool) $this->update($id, $payload);
    }

    public function deleteExternalOperator($id): bool
    {
        $operator = $this->findOperatorById($id);
        if (!$operator || (int) ($operator['is_own_operator'] ?? 0) === 1) {
            return false;
        }

        return (bool) $this->delete($id);
    }

    public function updateCommissionPercent($operatorId, $percent): bool
    {
        $operator = $this->findOperatorById($operatorId);
        if (!$operator || (int) ($operator['is_own_operator'] ?? 0) === 1) {
            return false;
        }

        $percent = $this->normalizeCommissionPercent($percent);
        if ($percent === null) {
            return false;
        }

        return (bool) $this->update($operatorId, ['commission_percent' => $percent]);
    }

    private function normalizeExternalOperatorData(array $data): ?array
    {
        if (!isset($data['name']) || trim((string) $data['name']) === '') {
            return null;
        }

        $commissionPercent = $this->normalizeCommissionPercent($data['commission_percent'] ?? 0);
        if ($commissionPercent === null) {
            return null;
        }

        return [
            'name' => trim((string) $data['name']),
            'is_own_operator' => 0,
            'commission_percent' => $commissionPercent,
        ];
    }

    private function normalizeCommissionPercent($value): ?float
    {
        if (!is_numeric($value)) {
            return null;
        }

        $value = (float) $value;
        if ($value < 0) {
            return null;
        }

        return $value;
    }
}
