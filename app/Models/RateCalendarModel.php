<?php

namespace App\Models;

use CodeIgniter\Model;

class RateCalendarModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'rates_calendar';
    protected $primaryKey = 'daily_rate_id';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'daily_rate_id',
        'rate_calendar_host_id',
        'daily_rate_code',
        'daily_rate_type',
        'daily_rate_day',
        'daily_rate_baserate',
        'daily_rate_guesttype_1',
        'daily_rate_guesttype_2',
        'daily_rate_guesttype_3',
        'daily_rate_guesttype_4',
        'daily_rate_minstay',
        'daily_rate_maxstay',
        'daily_rate_last_update',
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

    public function multi_query_execute($multi_query)
    {
        $query = true;
        if ($multi_query != null) {
            foreach ($multi_query as $single_query) {
                $query =
                    $query &&
                    $this->db->query($single_query);
            }
        }
        return $query;
    }
}
