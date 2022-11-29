<?php

namespace App\Models;

use CodeIgniter\Model;

class ServiceCalendarModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'services_calendar';
    protected $primaryKey = 'service_price_id';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'service_price_id',
        'service_price_host_id',
        'service_price_code',
        'service_price_type',
        'service_price_day',
        'service_price',
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

    public function get_price_calendar(
        $host_id,
        $service_price_type,
        $servicefrom,
        $serviceto
    ) {
        $query = $this->db->query(
            'SELECT service_price_code, service_price_type, service_price_day
        FROM services_calendar
        WHERE service_price_type = ' .
                $service_price_type .
                ' AND service_price_day >= "' .
                $servicefrom .
                '" AND service_price_day <= "' .
                $serviceto .
                '" AND service_price_host_id = ' .
                $host_id
        );
        $results = $query->getResult();
        return $results;
    }

    public function multi_query_execute($multi_query)
    {
        $query = true;
        if ($multi_query != null) {
            $this->db->transStart();
            foreach ($multi_query as $single_query) {
                $query =
                    $query &&
                    $this->db->query($single_query);
            }
            $this->db->transComplete();
        }
        return $query;
    }
}
