<?php

namespace App\Models;

use CodeIgniter\Model;

class GuestModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'guests';
    protected $primaryKey = 'guest_id';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'guest_id',
        'guest_category_code',
        'guest_sub_category_code',
        'guest_referral_surname',
        'guest_referral_name',
        'guest_referral_phone',
        'guest_mobile_phone',
        'guest_referral_email',
        'guest_referral_lang',
        'guest_company_name',
        'guest_account_img',
        'guest_city_residence',
        'guest_address_residence',
        'guest_postcode_residence',
        'guest_province_residence',
        'guest_region_residence',
        'guest_iso_state_residence',
        'guest_company_name_tax',
        'guest_email_tax',
        'guest_phone_tax',
        'guest_address_tax',
        'guest_city_tax',
        'guest_postcode_tax',
        'guest_province_tax',
        'guest_iso_state_tax',
        'guest_taxnumber_tax',
        'guest_taxcode_tax',
        'guest_certemail_tax',
        'guest_sdicode_tax',
        'guest_activation_date',
        'guest_ip_connection',
        'guest_last_update',
        'guest_username_security',
        'guest_password_security',
        'guest_password_lastupdate_security',
        'guest_status',
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
}
