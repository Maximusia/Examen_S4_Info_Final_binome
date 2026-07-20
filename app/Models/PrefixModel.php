<?php

namespace App\Models;
use CodeIgniter\Model;

class PrefixModel extends Model
{
    protected $table = 'prefixes';
    protected $primaryKey = 'id';

    protected $returnType = 'array';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'prefix'
    ];

    // Verifie si un prefixe est autorise.
    public function isValidPrefix(string $phone)
    {
        $prefix = substr($phone, 0, 3); // Récupère les 3 premiers caractères du numéro de téléphone
        return $this->where('prefix', $prefix)->first() > 0; // Vérifie si le préfixe existe dans la base de données
    }

    // Récupère tous les préfixes
    public function getAllPrefixes()
    {
        return $this->findAll();
    }
}