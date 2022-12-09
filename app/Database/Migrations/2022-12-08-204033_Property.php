<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Property extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'property_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'auto_increment' => true,
            ],
            'property_host_id' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'property_name' => [
                'type' => 'VARCHAR',
                'constraint' => '30',
            ],
            'property_type' => [
                'type' => 'VARCHAR',
                'constraint' => '30',
            ],
            'property_status' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
        ]);
        $this->forge->addPrimaryKey('property_id');
        $this->forge->createTable('properties');
    }

    public function down()
    {
        $this->forge->dropTable('properties');
    }
}
