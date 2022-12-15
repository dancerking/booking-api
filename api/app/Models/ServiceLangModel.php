<?php

namespace App\Models;

use CodeIgniter\Model;

class ServiceLangModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'services_lang';
    protected $primaryKey = 'service_lang_id';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'service_lang_id',
        'service_lang_host_id',
        'service_lang_service_id',
        'service_lang_lang',
        'service_lang_name',
        'service_lang_description',
        'service_lang_note_label',
        'service_lang_group_label',
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
