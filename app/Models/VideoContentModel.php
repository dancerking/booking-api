<?php

namespace App\Models;

use CodeIgniter\Model;

class VideoContentModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'video_contents';
    protected $primaryKey       = 'video_content_id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'video_content_id',
        'video_content_host_id',
        'video_content_channel',
        'video_content_code',
        'video_content_level',
        'video_order',
        'video_content_connection',
        'video_content_status',
        'video_content_activation',
        'video_content_last_update',
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}
