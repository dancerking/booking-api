<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class GuestType extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'guest_type_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'auto_increment' => true,
            ],
            'guest_type_code' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
            ],
            'guest_type_name' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
            ],
            'guest_type_lang' => [
                'type' => 'VARCHAR',
                'constraint' => '2',
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
        $this->forge->createTable('guest_types');
    }

    public function down()
    {
        $this->forge->dropTable('guest_types');
    }
}
