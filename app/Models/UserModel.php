<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['phone_number', 'balance'];
    protected $useTimestamps = false;

    protected $validationRules = [
        'phone_number' => 'required|min_length[10]|max_length[10]|is_unique[users.phone_number]',
        'balance' => 'required|numeric'
    ];

    protected $validationMessages = [
        'phone_number' => [
            'required' => 'Le numéro de téléphone est requis.',
            'min_length' => 'Le numéro de téléphone doit contenir exactement 10 chiffres.',
            'max_length' => 'Le numéro de téléphone doit contenir exactement 10 chiffres.',
            'is_unique' => 'Le numéro de téléphone existe déjà.'
        ],
        'balance' => [
            'required' => 'Balance is required.',
            'numeric' => 'Balance must be a numeric value.'
        ]
    ];


    // Ajouter un utilisateur avec validation du numéro de téléphone
    public function createUser($phoneNumber)
    {
        $phoneNumber = standardize_phone($phoneNumber); // Standardiser le numéro de téléphone
        if (!validate_mg_phone($phoneNumber)) {
            throw new \Exception('Numéro de téléphone invalide.');
        }
        // verifier si le numéro de téléphone existe déjà
        if ($this->findByPhone($phoneNumber)) {
            throw new \Exception('Le numéro de téléphone existe déjà.');
        }
        return $this->insert([
        'phone_number' => $phoneNumber, 
        'balance' => 0
        ]);

    }

    public function findByPhone($phoneNumber)
    {
        $phoneNumber = standardize_phone($phoneNumber); // Standardiser le numéro de téléphone
        return $this->where('phone_number', $phoneNumber)->first();
    }
    public function countAll()
    {
        return $this->countAllResults();
    }
}