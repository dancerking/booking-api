<?php

namespace App\Models;

use CodeIgniter\Model;

class FilterModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'filters';
    protected $primaryKey       = 'filter_id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'filter_id',
        'filter_code',
        'filter_level',
        'filter_name',
        'filter_lang',
        'filter_status',
        'filter_activation',
        'filter_lastupdate',
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

    public function get_filters($record_status) {
        $db = \Config\Database::connect();
        $query   = $db->query('SELECT filter_code, filter_level, filter_name, filter_lang
        FROM filters
        WHERE filter_status = ' . $record_status);
        $results = $query->getResult();
        return $results;
    }
}
