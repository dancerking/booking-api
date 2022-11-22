<?php

namespace App\Models;

use CodeIgniter\Model;

class ServiceModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'services';
    protected $primaryKey = 'service_id';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'service_id',
        'service_host_id',
        'service_mode',
        'service_mandatory',
        'service_mandatory_group_name',
        'service_mandatory_note',
        'service_vat_percentage',
        'service_status',
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

    public function get_service_data($host_id)
    {
        $service_query = $this->db->query(
            'SELECT service_id, service_mode, service_mandatory, service_mandatory_group_name, service_mandatory_note, service_vat_percentage, service_status
        FROM services
        WHERE service_host_id = ' . $host_id
        );
        $results = $service_query->getResult();
        foreach ($results as &$result) {
            $service_mapping_query = $this->db->query(
                'SELECT service_mapping_id, service_mapping_type, service_mapping_status FROM services_mapping WHERE service_mapping_host_id = ' .
                    $host_id .
                    ' AND service_mapping_code = ' .
                    $result->service_id
            );
            $service_mapping_result = $service_mapping_query->getResult();
            $result->service_mappings = $service_mapping_result;

            $service_lang_query = $this->db->query(
                'SELECT service_lang_name, service_lang_description, service_lang_note_label, service_lang_group_label, service_lang_lang FROM services_lang WHERE service_lang_host_id = ' .
                    $host_id .
                    ' AND service_lang_service_id = ' .
                    $result->service_id
            );
            $service_lang_result = $service_lang_query->getResult();
            $result->service_langs = $service_lang_result;
        }
        return $results;
    }

    public function is_existed_id($id)
    {
        $query = $this->db->query(
            'SELECT service_id FROM services WHERE service_id = ' .
                $id
        );
        $results = $query->getResult();
        return $results;
    }
}
