<?php

namespace App\Models;

use CodeIgniter\Model;

class VideoChannelModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'video_channels';
    protected $primaryKey       = 'video_channel_id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'video_channel_id',
        'video_channel_name',
        'video_channel_settings',
        'video_channel_status',
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

    public function get_video_channel() {
        $db = \Config\Database::connect();
        $query   = $db->query('SELECT video_channel_id, video_channel_name
        FROM video_channels
        WHERE video_channel_status = 1');
        $results = $query->getResult();
        return $results;
    }
}
