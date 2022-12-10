<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class PropertyCalendar extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'property_a_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'auto_increment' => true,
            ],
            'property_availability_host_id' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'property_a_day' => [
                'type' => 'TIMESTAMP',
                'default' => null,
                'null' => true,
            ],
            'property_code' => [
                'type' => 'INT',
                'constriant' => '11',
            ],
            'property_type_code' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'property_availability' => [
                'type' => 'TINYINT',
                'constraint' => '4',
            ],
        ]);
        $this->forge->addPrimaryKey('property_a_id');
        $this->forge->createTable('property_availability');
    }

    public function down()
    {
        $this->forge->dropTable('property_availability');
    }
}
