<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class GuestTypeMapping extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'guest_type_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'auto_increment' => true,
            ],
            'guest_type_host_id' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'guest_type_code' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
            ],
            'guest_type_age_from' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'guest_type_age_to' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'guest_type_status' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'guest_type_activation' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'guest_type_lastupdate' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
                'null' => true,
            ],
        ]);
        $this->forge->addPrimaryKey('guest_type_id');
        $this->forge->createTable('guest_types_mapping');
    }

    public function down()
    {
        $this->forge->dropTable('guest_types_mapping');
    }
}
