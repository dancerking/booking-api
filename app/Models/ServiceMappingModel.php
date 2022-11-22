<?php

namespace App\Models;

use CodeIgniter\Model;

class ServiceMappingModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'services_mapping';
    protected $primaryKey = 'service_mapping_id';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'service_mapping_id',
        'service_mapping_host_id',
        'service_mapping_code',
        'service_mapping_type',
        'service_mapping_status',
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

    public function is_existed_id($id)
    {
        $query = $this->db->query(
            'SELECT service_mapping_id FROM services_mapping WHERE service_mapping_id = ' .
                $id
        );
        $results = $query->getResult();
        return $results;
    }
}
