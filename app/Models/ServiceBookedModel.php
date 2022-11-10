<?php

namespace App\Models;

use CodeIgniter\Model;

class ServiceBookedModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'service_booked';
    protected $primaryKey       = 'service_booked_id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'service_booked_id',
        'service_booked_host',
        'service_booked_booking_id',
        'service_booked_service_id',
        'service_booked_from',
        'service_booked_to',
        'service_booked_qty',
        'service_booked_value',
        'service_booked_registration_date',
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
