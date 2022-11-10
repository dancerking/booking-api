<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Service extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'service_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'auto_increment' => true,
            ],
            'service_host_id' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'service_mode' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'service_mandatory' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'service_mandatory_group_name' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
            ],
            'service_mandatory_note' => [
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
            ],
            'service_vat_percentage' => [
                'type' => 'DECIMAL',
                'constraint' => '10, 2',
            ],
            'service_status' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
        ]);
        $this->forge->addPrimaryKey('service_id');
        $this->forge->createTable('services');
    }

    public function down()
    {
        $this->forge->dropTable('services');
    }
}
