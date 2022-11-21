<?php

namespace App\Models;

use CodeIgniter\Model;

class HostModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'hosts';
    protected $primaryKey = 'host_id';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'host_id',
        'host_category_code',
        'host_sub_category_code',
        'host_referral_surname',
        'host_referral_name',
        'host_referral_phone',
        'host_mobile_phone',
        'host_referral_email',
        'host_referral_lang',
        'host_company_name',
        'host_company_logo',
        'host_city_residence',
        'host_address_residence',
        'host_gps',
        'host_postcode_residence',
        'host_province_residence',
        'host_region_residence',
        'host_iso_state_residence',
        'host_company_name_tax',
        'host_email_tax',
        'host_phone_tax',
        'host_address_tax',
        'host_city_tax',
        'host_postcode_tax',
        'host_province_tax',
        'host_iso_state_tax',
        'host_taxnumber_tax',
        'host_taxcode_tax',
        'host_certemail_tax',
        'host_sdicode_tax',
        'host_activation_date',
        'host_ip_connection',
        'host_last_update',
        'host_username_security',
        'host_password_security',
        'host_password_lastupdate_security',
        'host_status',
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

    public function get_host_data($host_id)
    {
        // $query   = $this->db->query('SELECT `hosts`.host_id, `hosts`.host_company_name, `hosts`.host_referral_phone, `hosts`.host_referral_email, `hosts`.host_status
        // FROM `hosts` WHERE `hosts`.host_status = "1"');
        $query = $this->db->query(
            'SELECT `hosts`.host_id, `hosts`.host_company_name, `hosts`.host_referral_phone, `hosts`.host_referral_email, `hosts`.host_status
        FROM `hosts` WHERE `hosts`.host_id = ' . $host_id
        );
        $results = $query->getResult();
        return $results;
    }
}
