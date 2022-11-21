<?php

namespace App\Models;

use CodeIgniter\Model;

class RateLangModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'rates_lang';
    protected $primaryKey = 'rate_lang_id';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'rate_lang_id',
        'rate_lang_host_id',
        'rate_lang_code',
        'rate_lang_rules',
        'rate_name',
        'rate_short_description',
        'rates_lang',
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

    public function delete_by($rate_id, $host_id)
    {
        $builder = $this->builder();
        $builder
            ->where('rate_lang_host_id', $host_id)
            ->where('rate_lang_code', $rate_id);
        return $builder->delete();
    }

    public function is_existed_data($host_id, $rate_id)
    {
        $query = $this->db->query(
            'SELECT rate_lang_id FROM rates_lang WHERE rate_lang_host_id = ' .
                $host_id .
                ' AND rate_lang_code = ' .
                $rate_id
        );
        $results = $query->getResult();
        return $results;
    }
}
