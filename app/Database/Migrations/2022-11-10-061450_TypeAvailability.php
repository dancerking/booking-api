<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class TypeAvailability extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'type_availability_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'auto_increment' => true,
            ],
            'type_availability_host_id' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'type_availability_day' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
                'null' => true,
            ],
            'type_availability_code' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'type_availability_qty' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'type_availability_msa' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'type_availability_coa' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'type_availability_cod' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
        ]);
        $this->forge->addPrimaryKey('type_availability_id');
        $this->forge->createTable('type_availability');
    }

    public function down()
    {
        $this->forge->dropTable('type_availability');
    }
}
