<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class ServiceBooked extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'service_booked_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'auto_increment' => true,
            ],
            'service_booked_host' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'service_booked_booking_id' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'service_booked_service_id' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'service_booked_from' => [
                'type' => 'TIMESTAMP',
                'default' => null,
                'null' => true,
            ],
            'service_booked_to' => [
                'type' => 'TIMESTAMP',
                'default' => null,
                'null' => true,
            ],
            'service_booked_qty' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'service_booked_value' => [
                'type' => 'DECIMAL',
                'constraint' => '10, 2',
            ],
            'service_booked_registration_date' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql(
                    'CURRENT_TIMESTAMP'
                ),
            ],
        ]);
        $this->forge->addPrimaryKey('service_booked_id');
        $this->forge->createTable('services_booked');
    }

    public function down()
    {
        $this->forge->dropTable('services_booked');
    }
}
