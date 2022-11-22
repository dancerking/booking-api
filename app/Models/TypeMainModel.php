<?php

namespace App\Models;

use CodeIgniter\Model;

class TypeMainModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'types_main';
    protected $primaryKey = 'main_type_id';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'main_type_id',
        'main_type_code',
        'main_type_name',
        'main_type_lang',
        'main_type_status',
        'main_type_activation',
        'main_type_last_update',
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

    public function get_type_main($record_status)
    {
        $query = $this->db->query(
            'SELECT main_type_code, main_type_name, main_type_lang FROM types_main WHERE main_type_status = ' .
                $record_status
        );
        $results = $query->getResult();
        return $results;
    }
}
