<?php

namespace App\Models;

use CodeIgniter\Model;

class FilterMappingModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'filters_mapping';
    protected $primaryKey       = 'filter_mapping_id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'filter_mapping_id',
        'filter_mapping_host_id',
        'filter_mapping_code',
        'filter_mapping_level',
        'filter_mapping_type',
        'filter_mapping_status',
        'filter_mapping_activation',
        'filter_mapping_lastupdate',
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

    public function get_mapped_filters($host_id, $record_status) {
        $db = \Config\Database::connect();
        $query   = $db->query('SELECT filter_mapping_code, filter_mapping_level, filter_mapping_type
        FROM filters_mapping
        WHERE filter_mapping_status = ' . $record_status . ' AND filter_mapping_host_id=' . $host_id);
        $results = $query->getResult();
        return $results;
    }
}
