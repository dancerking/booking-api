<?php

namespace App\Models;

use CodeIgniter\Model;

class ContentCaptionModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'content_captions';
    protected $primaryKey = 'content_caption_id';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'content_caption_id',
        'content_caption_host_id',
        'content_caption_type',
        'content_caption_connection_id',
        'content_caption',
        'content_caption_lang',
        'content_caption_status',
        'content_activation',
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

    public function delete_by(
        $host_id,
        $content_caption_type,
        $content_caption_connection_id
    ) {
        $builder = $this->builder();
        $builder
            ->where('content_caption_host_id', $host_id)
            ->where(
                'content_caption_type',
                $content_caption_type
            )
            ->where(
                'content_caption_connection_id',
                $content_caption_connection_id
            );
        $builder->delete();
    }
}
