<?php

namespace App\Models;

use CodeIgniter\Model;

class RateModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'rates';
    protected $primaryKey = 'rate_id';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'rate_id',
        'rate_host_id',
        'rate_setting',
        'rate_master',
        'rate_discount_markup',
        'rate_guests_included',
        'rate_downpayment',
        'rate_from_checkin',
        'rate_status',
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

    public function get_rate_data($host_id)
    {
        $rate_query = $this->db->query(
            'SELECT rate_id, rate_setting, rate_master, rate_discount_markup, rate_guests_included, rate_downpayment, rate_from_checkin, rate_status
        FROM rates
        WHERE rate_host_id = ' . $host_id
        );
        $results = $rate_query->getResult();
        foreach ($results as &$result) {
            $rate_mapping_query = $this->db->query(
                'SELECT rate_mapping_id, rate_mapping_rates_id, rate_mapping_type_code, rate_mapping_dowpayment_percentage, rate_mapping_alt_fixed_price FROM rates_mapping WHERE rate_mapping_host_id = ' .
                    $host_id .
                    ' AND rate_mapping_rates_id = ' .
                    $result->rate_id
            );
            $rate_mapping_result = $rate_mapping_query->getResult();
            $result->rate_mappings = $rate_mapping_result;

            $rate_lang_query = $this->db->query(
                'SELECT rate_lang_id, rate_lang_code, rate_lang_rules, rate_name, rate_short_description, rates_lang FROM rates_lang WHERE rate_lang_host_id = ' .
                    $host_id .
                    ' AND rate_lang_code = ' .
                    $result->rate_id
            );
            $rate_lang_result = $rate_lang_query->getResult();
            $result->rate_langs = $rate_lang_result;
        }
        return $results;
    }
}
