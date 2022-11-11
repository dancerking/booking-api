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

    public function get_level1_video($host_id) {
        $db = \Config\Database::connect();
        $query   = $db->query('SELECT video_contents.video_content_id, video_contents.video_content_channel, video_contents.video_content_code, video_contents.video_content_status, IF' . '(ISNULL(content_captions.content_caption)=1, ' . '""' . ', content_captions.content_caption) AS content_caption, IF' . '(ISNULL(content_captions.content_caption_lang)=1, ' . '""' . ', content_captions.content_caption_lang) AS content_caption_lang
        FROM video_contents LEFT JOIN content_captions ON video_contents.video_content_id = content_captions.content_caption_connection_id AND content_captions.content_caption_type="2" AND content_captions.content_caption_host_id = ' . $host_id . '
        WHERE video_contents.video_content_level = "1" AND  video_contents.video_content_host_id = ' . $host_id);
        $results = $query->getResult();
        foreach($results as &$result) {
            $content_caption = [
                'content_caption' => $result->content_caption,
                'content_caption_lang' => $result->content_caption_lang,
            ];
            $result->content_caption = $content_caption;
        }
        return $results;
    }

    public function get_level2_video($host_id) {
        $db = \Config\Database::connect();
        $query   = $db->query('SELECT video_contents.video_content_id, video_contents.video_content_connection, video_contents.video_content_channel, video_contents.video_content_code, video_contents.video_content_status, IF' . '(ISNULL(content_captions.content_caption)=1, ' . '""' . ', content_captions.content_caption) AS content_caption, IF' . '(ISNULL(content_captions.content_caption_lang)=1, ' . '""' . ', content_captions.content_caption_lang) AS content_caption_lang
        FROM video_contents
        LEFT JOIN content_captions ON video_contents.video_content_id = content_captions.content_caption_connection_id AND content_captions.content_caption_type = "1" AND content_captions.content_caption_host_id = ' . $host_id . '
        WHERE video_contents.video_content_level = "2" AND video_contents.video_content_host_id = ' . $host_id);
        $results = $query->getResult();
        foreach($results as &$result) {
            $query = $db->query('SELECT types_mapping.type_mapping_name
            FROM types_mapping
            WHERE types_mapping.type_mapping_code = ' . '"' . $result->video_content_connection . '"' . ' AND types_mapping.type_mapping_lang="it" AND types_mapping.type_mapping_host_id = ' . $host_id);
            $mapping_names = $query->getResult();
            $type_mapping_names = [];
            foreach($mapping_names as $mapping_name) {
                array_push($type_mapping_names, $mapping_name->type_mapping_name);
            }
            $result->type_mapping_name = $type_mapping_names == null ? [] : $type_mapping_names;
            $content_caption = [
                'content_caption' => $result->content_caption,
                'content_caption_lang' => $result->content_caption_lang,
            ];
            $result->content_caption = $content_caption;
        }
        return $results;
    }
}
