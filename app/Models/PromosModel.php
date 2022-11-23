<?php

namespace App\Models;

use CodeIgniter\Model;

class PromosModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'promos';
    protected $primaryKey = 'promo_id';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'promo_id',
        'promo_host_id',
        'promo_code',
        'promo_booking_from',
        'promo_booking_to',
        'promo_rate',
        'promo_arrival',
        'promo_departure',
        'promo_percentage',
        'promo_status',
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

    public function get_promos_per_type(
        $host_id,
        $promo_mapping_type
    ) {
        $query = $this->db->query(
            'SELECT promos.promo_id, promos.promo_rate, promos.promo_booking_to, promos.promo_booking_from, promos.promo_arrival, promos.promo_departure, promos.promo_percentage, promos.promo_status,
        promos_mapping.promo_mapping_id, promos_mapping.promo_mapping_status, promos_mapping.promo_mapping_type
        FROM promos
        LEFT JOIN promos_mapping ON promos_mapping.promo_mapping_code=promos.promo_id AND promos_mapping.promo_mapping_host_id = ' .
                $host_id .
                ' AND promos_mapping.promo_mapping_type = ' .
                $promo_mapping_type .
                ' AND promos_mapping.promo_mapping_status = 1 WHERE promos.promo_host_id = ' .
                $host_id .
                ' AND promos.promo_status = 1'
        );
        $results = $query->getResult();
        foreach ($results as &$result) {
            if ($result->promo_mapping_type != null) {
                $type_mapping_name_query = $this->db->query(
                    'SELECT type_mapping_name FROM types_mapping WHERE type_mapping_code = ' .
                        $result->promo_mapping_type .
                        ' AND type_mapping_lang = "it" AND type_mapping_host_id = ' .
                        $host_id .
                        ' AND type_mapping_main_status = 1'
                );
                $type_mapping_name_results = $type_mapping_name_query->getResult();
                $result->type_mapping_names = $type_mapping_name_results;
            }
            $result->type_mapping_names = [];
        }
        return $results;
    }
}
