<?php

namespace App\Models;

use CodeIgniter\Model;

class RateMappingModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'rates_mapping';
    protected $primaryKey       = 'rate_mapping_id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'rate_mapping_id',
        'rate_mapping_host_id',
        'rate_mapping_rates_id',
        'rate_mapping_type_code',
        'rate_mapping_dowpayment_percentage',
        'rate_mapping_alt_fixed_price',
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

    public function delete_by($rate_id, $host_id) {
        $builder = $this->builder();
        $builder->where('rate_mapping_host_id', $host_id)
                ->where('rate_mapping_rates_id', $rate_id);
        return $builder->delete();
    }

    public function is_existed_data($host_id, $rate_id) {
        $query = $this->db->query('SELECT rate_mapping_id FROM rates_mapping WHERE rate_mapping_host_id = ' . $host_id . ' AND rate_mapping_rates_id = ' . $rate_id);
        $results = $query->getResult();
        return $results;
    }
}
