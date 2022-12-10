<?php

namespace App\Models;

use CodeIgniter\Model;

class VideoContentModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'video_contents';
    protected $primaryKey = 'video_content_id';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
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

    public function get_level1_video($host_id, $limit)
    {
        $query = $this->db->query(
            'SELECT video_content_id, video_content_channel, video_content_code, video_content_status
        FROM video_contents
        WHERE video_content_level = "1" AND  video_content_host_id = ' .
                $host_id .
                ' ORDER BY video_order ASC' .
                ' LIMIT ' .
                $limit
        );
        $results = $query->getResult();
        foreach ($results as &$result) {
            $content_caption_query = $this->db->query(
                'SELECT IF' .
                    '(ISNULL(content_caption)=1, ' .
                    '""' .
                    ', content_caption) AS content_caption, IF' .
                    '(ISNULL(content_caption_lang)=1, ' .
                    '""' .
                    ', content_caption_lang) AS content_caption_lang FROM content_captions WHERE content_caption_connection_id = ' .
                    $result->video_content_id .
                    ' AND content_caption_host_id = ' .
                    $host_id .
                    ' AND content_caption_type = 2'
            );
            $content_caption_results = $content_caption_query->getResult();
            $result->content_captions = $content_caption_results;
        }
        return $results;
    }

    public function get_level2_video($host_id, $limit)
    {
        $query = $this->db->query(
            'SELECT video_content_id, video_content_connection, video_content_channel, video_content_code, video_content_status
        FROM video_contents
        WHERE video_content_level = "2" AND video_content_host_id = ' .
                $host_id .
                ' ORDER BY video_order ASC' .
                ' LIMIT ' .
                $limit
        );
        $results = $query->getResult();
        foreach ($results as &$result) {
            $query = $this->db->query(
                'SELECT types_mapping.type_mapping_name
            FROM types_mapping
            WHERE types_mapping.type_mapping_code = ' .
                    '"' .
                    $result->video_content_connection .
                    '"' .
                    ' AND types_mapping.type_mapping_lang="it" AND types_mapping.type_mapping_host_id = ' .
                    $host_id
            );
            $mapping_names = $query->getResult();
            $type_mapping_names = [];
            foreach ($mapping_names as $mapping_name) {
                array_push(
                    $type_mapping_names,
                    $mapping_name->type_mapping_name
                );
            }
            $result->type_mapping_names =
                $type_mapping_names == null
                    ? []
                    : $type_mapping_names;
            $content_caption_query = $this->db->query(
                'SELECT IF' .
                    '(ISNULL(content_caption)=1, ' .
                    '""' .
                    ', content_caption) AS content_caption, IF' .
                    '(ISNULL(content_caption_lang)=1, ' .
                    '""' .
                    ', content_caption_lang) AS content_caption_lang FROM content_captions WHERE content_caption_connection_id = ' .
                    $result->video_content_id .
                    ' AND content_caption_host_id = ' .
                    $host_id .
                    ' AND content_caption_type = 2'
            );
            $content_caption_results = $content_caption_query->getResult();
            $result->content_captions = $content_caption_results;
        }
        return $results;
    }
}
