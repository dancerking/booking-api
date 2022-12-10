<?php

namespace App\Models;

use CodeIgniter\Model;

class GuestTypeModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'guest_types';
    protected $primaryKey = 'guest_type_id';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'guest_type_id',
        'guest_type_code',
        'guest_type_name',
        'guest_type_lang',
        'guest_type_status',
        'guest_type_activation',
        'guest_type_lastupdate',
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

    public function get_guest_types($record_status)
    {
        $query = $this->db->query(
            'SELECT guest_type_name, guest_type_lang
        FROM guest_types
        WHERE guest_type_status = ' . $record_status
        );
        $results = $query->getResult();
        return $results;
    }
}
