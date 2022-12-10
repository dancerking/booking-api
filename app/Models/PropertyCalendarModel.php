<?php

namespace App\Models;

use CodeIgniter\Model;

class PropertyCalendarModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'property_availability';
    protected $primaryKey = 'property_a_id';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'property_a_id',
        'property_availability_host_id',
        'property_a_day',
        'property_code',
        'property_type_code',
        'property_availability',
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

    public function get_property_calendar(
        $host_id,
        $property_id
    ) {
        $query = $this->db->query(
            'SELECT property_a_day, property_code, property_type_code, property_availability
        FROM property_availability
        WHERE property_code = ' .
                $property_id .
                ' AND property_availability_host_id = ' .
                $host_id
        );
        $results = $query->getResult();
        return $results;
    }
}
