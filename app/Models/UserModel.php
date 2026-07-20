<?php

namespace App\Models;
use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $returnType = 'array';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'phone_number',
        'balance'
    ];

    // Recherche un utilisateur par son numéro de téléphone
    public function findByPhone(string $phone)
    {
        return $this->where('phone_number', $phone)->first();
    }

    // Met à jour le solde d'un utilisateur
    public function updateBalance(int $userId, int $balance)
    {
        return $this->update($userId, [
            'balance' => $balance
        ]);
    }

}