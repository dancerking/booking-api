<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class City extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'city_code' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'city_name' => [
                'type' => 'VARCHAR',
                'constraint' => '38',
                'null' => true,
                'default' => null,
            ],
            'city_province' => [
                'type' => 'VARCHAR',
                'constraint' => '9',
                'null' => true,
                'default' => null,
            ],
            'city_pr_from' => [
                'type' => 'VARCHAR',
                'constraint' => '19',
                'null' => true,
                'default' => null,
            ],
            'city_pr_to' => [
                'type' => 'VARCHAR',
                'constraint' => '19',
                'null' => true,
                'default' => null,
            ],
            'city_region' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
            ],
            'city_postal_code' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
            ],
        ]);
        $this->forge->addPrimaryKey('city_code');
        $this->forge->createTable('cities');
    }

    public function down()
    {
        $this->forge->dropTable('cities');
    }
}
