<?php

namespace App\Models;

use CodeIgniter\Model;

class PromosModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'promos';
    protected $primaryKey       = 'promo_id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'promo_id',
        'promo_host_id',
        'promo_code',
        'promo_booking_from',
        'promo_booking_to',
        'promo_rate',
        'promo_arrival',
        'promo_departure',
        'promo_percentage',
        'promo_status',
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}
