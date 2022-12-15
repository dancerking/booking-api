<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ServiceLang extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'service_lang_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'auto_increment' => true,
            ],
            'service_lang_host_id' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'service_lang_service_id' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'service_lang_lang' => [
                'type' => 'VARCHAR',
                'constraint' => '2',
            ],
            'service_lang_name' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
            ],
            'service_lang_description' => [
                'type' => 'VARCHAR',
                'constraint' => '500',
            ],
            'service_lang_note_label' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
            ],
            'service_lang_group_label' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
            ],
        ]);
        $this->forge->addPrimaryKey('service_lang_id');
        $this->forge->createTable('services_lang');
    }

    public function down()
    {
        $this->forge->dropTable('services_lang');
    }
}
