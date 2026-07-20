<?php

namespace App\Models;
use CodeIgniter\Model;

class OperationTypeModel extends Model
{
    protected $table = 'operation_types';
    protected $primaryKey = 'id';

    protected $returnType = 'array';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'code',
        'name'
    ];

    // Rechercher un type d'opération par son code. Exemples : depot, retrait, transfert
    public function findByCode(string $code)
    {
        return $this->where('code', $code)->first();
    }

    // Récupère tous les types d'opérations
    public function getAllOperationTypes()
    {
        return $this->findAll();
    }
}