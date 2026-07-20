<?php

namespace App\Models;

use CodeIgniter\Model;

class PrefixModel extends Model
{
    protected $table = 'prefixes';
    protected $primaryKey = 'id';
    protected $allowedFields = ['prefix'];
    protected $useTimestamps = false;

    public function isValidPrefix($phone)
    {
        $prefixes = $this->findAll();
        foreach ($prefixes as $p) {
            if (strpos($phone, $p['prefix']) === 0) {
                return true;
            }
        }
        return false;
    }
}