<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class TypeMain extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'main_type_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'auto_increment' => true,
            ],
            'main_type_code' => [
                'type' => 'VARCHAR',
                'constraint' => '30',
            ],
            'main_type_name' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
            ],
            'main_type_lang' => [
                'type' => 'VARCHAR',
                'constraint' => '2',
            ],
            'main_type_status' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'main_type_activation' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql(
                    'CURRENT_TIMESTAMP'
                ),
            ],
            'main_type_last_update' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql(
                    'CURRENT_TIMESTAMP'
                ),
                'null' => true,
            ],
        ]);
        $this->forge->addPrimaryKey('main_type_id');
        $this->forge->createTable('types_main');
    }

    public function down()
    {
        $this->forge->dropTable('types_main');
    }
}
