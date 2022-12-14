<?php

namespace App\Models;

use CodeIgniter\Model;

class PromosMappingModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'promos_mapping';
    protected $primaryKey = 'promo_mapping_id';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'promo_mapping_id',
        'promo_mapping_host_id',
        'promo_mapping_code',
        'promo_mapping_type',
        'promo_mapping_status',
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    public function multi_query_execute($multi_query)
    {
        if ($multi_query != null) {
            $this->db->transStart();
            foreach ($multi_query as $single_query) {
                $this->db->query($single_query);
            }
            $this->db->transComplete();
            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                return false;
            } else {
                $this->db->transCommit();
                return true;
            }
        }
        return false;
    }
}
