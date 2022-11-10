<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TypeMapping extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'type_mapping_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'auto_increment' => true,
            ],
            'type_mapping_host_id' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'type_mapping_main_code' => [
                'type' => 'VARCHAR',
                'constraint' => '30',
            ],
            'type_mapping_code' => [
                'type' => 'VARCHAR',
                'constraint' => '30',
            ],
            'type_mapping_name' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
            ],
            'type_mapping_description' => [
                'type' => 'VARCHAR',
                'constraint' => '500',
            ],
            'type_mapping_lang' => [
                'type' => 'VARCHAR',
                'constraint' => '2',
            ],
            'type_mapping_main_status' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
        ]);
        $this->forge->addPrimaryKey('type_mapping_id');
        $this->forge->createTable('types_mapping');
    }

    public function down()
    {
        $this->forge->dropTable('types_mapping');
    }
}
