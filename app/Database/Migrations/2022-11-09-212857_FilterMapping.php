<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class FilterMapping extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'filter_mapping_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'auto_increment' => true,
            ],
            'filter_mapping_host_id' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'filter_mapping_code' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
            ],
            'filter_mapping_level' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'filter_mapping_type' => [
                'type' => 'VARCHAR',
                'constraint' => '30',
            ],
            'filter_mapping_status' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'filter_mapping_activation' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql(
                    'CURRENT_TIMESTAMP'
                ),
            ],
            'filter_mapping_lastupdate' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql(
                    'CURRENT_TIMESTAMP'
                ),
                'null' => true,
            ],
        ]);
        $this->forge->addPrimaryKey('filter_mapping_id');
        $this->forge->createTable('filters_mapping');
    }

    public function down()
    {
        $this->forge->dropTable('filters_mapping');
    }
}
