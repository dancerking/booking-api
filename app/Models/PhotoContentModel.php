<?php

namespace App\Models;

use CodeIgniter\Model;

class PhotoContentModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'photo_contents';
    protected $primaryKey       = 'photo_content_id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'photo_content_id',
        'photo_content_host_id',
        'photo_content_level',
        'photo_content_connection',
        'photo_content_url',
        'photo_content_order',
        'photo_content_status',
        'photo_content_activation',
        'photo_content_last_update',
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

    public function get_level1_photo() {
        $db = \Config\Database::connect();
        $query   = $db->query('select photo_contents.photo_content_id, photo_contents.photo_content_url, photo_contents.photo_content_status, content_captions.content_caption, content_captions.content_caption_lang
        FROM photo_contents
        LEFT JOIN content_captions on photo_contents.photo_content_id = content_captions.content_caption_connection_id
        where photo_contents.photo_content_level = "1"');
        $results = $query->getResult();
        return $results;
    }

    public function get_level2_photo() {
        $db = \Config\Database::connect();
        $query   = $db->query('select photo_contents.photo_content_id, photo_contents.photo_content_url, photo_contents.photo_content_status, content_captions.content_caption, content_captions.content_caption_lang, types_mapping.type_mapping_name
        FROM photo_contents
        LEFT JOIN content_captions on photo_contents.photo_content_id = content_captions.content_caption_connection_id
        LEFT JOIN types_mapping on photo_contents.photo_content_connection = types_mapping.type_mapping_code and types_mapping.type_mapping_lang = "it"
        where photo_contents.photo_content_level = "2"');
        $results = $query->getResult();
        return $results;
    }
}
