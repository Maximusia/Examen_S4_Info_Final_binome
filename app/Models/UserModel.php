<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['phone_number', 'balance'];
    protected $useTimestamps = false;

    public function countAll()
    {
        return $this->countAllResults();
    }
}