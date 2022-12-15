<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Promos extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'promo_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'auto_increment' => true,
            ],
            'promo_host_id' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'promo_code' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
            ],
            'promo_booking_from' => [
                'type' => 'TIMESTAMP',
                'default' => null,
                'null' => true,
            ],
            'promo_booking_to' => [
                'type' => 'TIMESTAMP',
                'default' => null,
                'null' => true,
            ],
            'promo_rate' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'promo_arrival' => [
                'type' => 'TIMESTAMP',
                'default' => null,
                'null' => true,
            ],
            'promo_departure' => [
                'type' => 'TIMESTAMP',
                'default' => null,
                'null' => true,
            ],
            'promo_percentage' => [
                'type' => 'DECIMAL',
                'constraint' => '10, 2',
            ],
            'promo_status' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
        ]);
        $this->forge->addPrimaryKey('promo_id');
        $this->forge->createTable('promos');
    }

    public function down()
    {
        $this->forge->dropTable('promos');
    }
}
