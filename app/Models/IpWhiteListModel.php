<?php

namespace App\Models;

use CodeIgniter\Model;

class IpWhiteListModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'ip_white_list';
    protected $primaryKey = 'ip_id';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['white_ip', 'host_id'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'white_ip' => 'required',
        'host_id' => 'required',
    ];
    protected $validationMessages = [];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = ['is_table_exist'];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    protected function is_table_exist()
    {
        $forge = \Config\Database::forge();
        $forge->addField([
            'ip_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'auto_increment' => true,
            ],
            'white_ip' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
            ],
            'host_id' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
        ]);
        $forge->addPrimaryKey('ip_id');
        $forge->createTable('ip_white_list', true);
    }
}
