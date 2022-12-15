<?php

namespace App\Models;

use CodeIgniter\Model;

class TypeAvailabilityModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'type_availability';
    protected $primaryKey = 'type_availability_id';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'type_availability_id',
        'type_availability_host_id',
        'type_availability_day',
        'type_availability_code',
        'type_availability_qty',
        'type_availability_msa',
        'type_availability_coa',
        'type_availability_cod',
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

    public function get_availability_types(
        $host_id,
        $type_availability_code,
        $from,
        $to
    ) {
        $query = $this->db->query(
            'SELECT type_availability_code, type_availability_day, type_availability_qty, type_availability_msa, type_availability_coa, type_availability_cod
        FROM type_availability
        WHERE type_availability_code = ' .
                $type_availability_code .
                ' AND type_availability_day >= "' .
                $from .
                '" AND type_availability_day <= "' .
                $to .
                '" AND type_availability_host_id = ' .
                $host_id .
                ' GROUP BY type_availability_day'
        );
        $results = $query->getResult();
        return $results;
    }

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
