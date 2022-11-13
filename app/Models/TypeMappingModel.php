<?php

namespace App\Models;

use CodeIgniter\Model;

class TypeMappingModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'types_mapping';
    protected $primaryKey       = 'type_mapping_id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'type_mapping_id',
        'type_mapping_host_id',
        'type_mapping_main_code',
        'type_mapping_code',
        'type_mapping_name',
        'type_mapping_description',
        'type_mapping_lang',
        'type_mapping_main_status',
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

    public function get_mapping_types($host_id, $record_status) {
        $query   = $this->db->query('SELECT type_mapping_main_code, type_mapping_code, type_mapping_name, type_mapping_lang, type_mapping_main_status
        FROM types_mapping
        WHERE type_mapping_host_id = ' . $host_id . ' AND type_mapping_main_status = ' . $record_status);
        $results = $query->getResult();
        return $results;
    }
}
