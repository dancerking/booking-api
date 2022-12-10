<?php

namespace App\Models;

use CodeIgniter\Model;
use PhpParser\Node\Stmt\Foreach_;

class TypeMappingModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'types_mapping';
    protected $primaryKey = 'type_mapping_id';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
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

    public function get_mapping_types(
        $host_id,
        $record_status
    ) {
        $query = $this->db->query(
            'SELECT type_mapping_main_code, type_mapping_code, type_mapping_name, type_mapping_lang, type_mapping_main_status
        FROM types_mapping
        WHERE type_mapping_host_id = ' .
                $host_id .
                ' AND type_mapping_main_status = ' .
                $record_status
        );
        $results = $query->getResult();
        return $results;
    }

    public function get_mapped_property_types($host_id)
    {
        $type_mapping_query = $this->db->query(
            'SELECT type_mapping_id AS mapping_id, type_mapping_main_code, type_mapping_code, type_mapping_name, type_mapping_lang FROM types_mapping WHERE type_mapping_host_id = ' .
                $host_id
        );
        $type_mapping_results = $type_mapping_query->getResult();
        foreach (
            $type_mapping_results
            as &$type_mapping_result
        ) {
            $main_type_query = $this->db->query(
                'SELECT main_type_id, main_type_code, main_type_name, main_type_lang FROM types_main WHERE main_type_code = "' .
                    $type_mapping_result->type_mapping_main_code .
                    '"'
            );
            $main_type_result = $main_type_query->getResult();
            $type_mapping_result->main_types = $main_type_result;
        }
        return $type_mapping_results;
    }

    public function get_mapped_types($host_id, $status)
    {
        $type_mapping_query = $this->db->query(
            'SELECT types_mapping.type_mapping_id AS mapping_id, types_mapping.type_mapping_main_code AS main_type_code, types_main.main_type_name FROM types_mapping LEFT JOIN types_main ON types_main.main_type_code = types_mapping.type_mapping_main_code AND types_main.main_type_lang = "it" WHERE types_mapping.type_mapping_host_id = ' .
                $host_id .
                ' AND types_mapping.type_mapping_main_status = ' .
                $status
        );
        $results = $type_mapping_query->getResult();
        return $results;
    }
}
