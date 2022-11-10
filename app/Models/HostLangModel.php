<?php

namespace App\Models;

use CodeIgniter\Model;

class HostLangModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'host_lang';
    protected $primaryKey       = 'host_lang_id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'host_lang_id',
        'host_id',
        'host_lang_code',
        'host_lang_name',
        'host_lang_subtitle',
        'host_short_description',
        'host_lang_long_description',
        'host_lang_booking_rules',
        'host_lang_property_rules',
        'host_lang_arrival_information',
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
