<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ServiceMapping extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'service_mapping_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'auto_increment' => true,
            ],
            'service_mapping_host_id' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'service_mapping_code' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'service_mapping_type' => [
                'type' => 'VARCHAR',
                'constraint' => '30',
            ],
            'service_mapping_status' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
        ]);
        $this->forge->addPrimaryKey('service_mapping_id');
        $this->forge->createTable('services_mapping');
    }

    public function down()
    {
        $this->forge->dropTable('services_mapping');
    }
}
