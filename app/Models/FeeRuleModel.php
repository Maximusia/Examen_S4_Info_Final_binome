<?php

namespace App\Models;

use CodeIgniter\Model;

class FeeRuleModel extends Model
{
    protected $table = 'fee_rules';
    protected $primaryKey = 'id';
    protected $allowedFields = ['operation_type_id', 'min_amount', 'max_amount', 'fee'];
    protected $useTimestamps = false;
}