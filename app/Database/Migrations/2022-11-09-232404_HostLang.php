<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class HostLang extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'host_lang_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'auto_increment' => true,
            ],
            'host_id' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'host_lang_code' => [
                'type' => 'VARCHAR',
                'constraint' => '2',
            ],
            'host_lang_name' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'host_lang_subtitle' => [
                'type' => 'VARCHAR',
                'constraint' => '300',
            ],
            'host_short_description' => [
                'type' => 'VARCHAR',
                'constraint' => '300',
            ],
            'host_lang_long_description' => [
                'type' => 'TEXT',
            ],
            'host_lang_booking_rules' => [
                'type' => 'TEXT',
            ],
            'host_lang_property_rules' => [
                'type' => 'TEXT',
            ],
            'host_lang_arrival_information' => [
                'type' => 'TEXT',
            ],
        ]);
        $this->forge->addPrimaryKey('host_lang_id');
        $this->forge->createTable('host_lang');
    }

    public function down()
    {
        $this->forge->dropTable('host_lang');
    }
}
