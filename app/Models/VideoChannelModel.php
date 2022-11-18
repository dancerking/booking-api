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

    public function get_video_channel($host_id, $record_status) {
        $query   = $this->db->query('SELECT video_contents.video_content_id, video_contents.video_content_code, video_channels.video_channel_id, video_channels.video_channel_name  FROM video_contents LEFT JOIN video_channels ON video_channels.video_channel_id = video_contents.video_content_channel WHERE video_content_status = ' . $record_status . ' AND  video_content_host_id = ' . $host_id);
        $results = $query->getResult();
        foreach($results as &$result) {
            $content_caption_query = $this->db->query('SELECT content_caption_id, IF' . '(ISNULL(content_caption)=1, ' . '""' . ', content_caption) AS content_caption, IF' . '(ISNULL(content_caption_lang)=1, ' . '""' . ', content_caption_lang) AS content_caption_lang FROM content_captions WHERE content_caption_connection_id = ' . $result->video_content_id . ' AND content_caption_host_id = ' . $host_id . ' AND content_caption_type = 2');
            $content_caption_results = $content_caption_query->getResult();
            $result->content_caption = $content_caption_results;
        }
        return $results;
    }
}
