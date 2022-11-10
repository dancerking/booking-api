<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class HostBooking extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'host_booking_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'auto_increment' => true,
            ],
            'host_booking_host_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
            ],
            'host_booking_code' => [
                'type' => 'VARCHAR',
                'constraint' => '30',
            ],
            'host_booking_ota' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => '1',
            ],
            'host_booking_ota_code' => [
                'type' => 'VARCHAR',
                'constraint' => '30',
                'default' => '0',
            ],
            'host_booking_registration_date' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'host_booking_arrival' => [
                'type' => 'TIMESTAMP',
                'default' => null,
                'null' => true,
            ],
            'host_booking_departure' => [
                'type' => 'TIMESTAMP',
                'default' => null,
                'null' => true,
            ],
            'host_booking_property_type' => [
                'type' => 'VARCHAR',
                'constraint' => '30',
                'default' => '0',
            ],
            'host_booking_guests_number' => [
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
            ],
            'host_booking_gtype1' => [
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
            ],
            'host_booking_gtype2' => [
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
            ],
            'host_booking_gtype3' => [
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
            ],
            'host_booking_gtype4' => [
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
            ],
            'host_booking_total_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '10, 2',
                'default' => '0.00'
            ],
            'host_booking_paid' => [
                'type' => 'DECIMAL',
                'constraint' => '10, 2',
                'default' => '0.00'
            ],
            'host_booking_currency' => [
                'type' => 'VARCHAR',
                'constraint' => '3',
                'default' => 'EUR',
                'null' => true,
            ],
            'host_booking_referral_email' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => null,
                'null' => true,
            ],
            'host_booking_referral_name' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => null,
                'null' => true,
            ],
            'host_booking_referral_surname' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => null,
                'null' => true,
            ],
            'host_booking_referral_mobile_phone' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => null,
                'null' => true,
            ],
            'host_booking_referral_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'default' => null,
                'null' => true,
            ],
            'host_booking_referral_lang' => [
                'type' => 'VARCHAR',
                'constraint' => '2',
                'default' => null,
                'null' => true,
            ],
            'host_booking_ota_fee' => [
                'type' => 'DECIMAL',
                'constraint' => '10, 2',
                'default' => '0.00'
            ],
            'host_booking_note' => [
                'type' => 'TEXT',
                'default' => null,
                'null' => true,
            ],
            'host_booking_rules' => [
                'type' => 'TEXT',
                'default' => null,
                'null' => true,
            ],
            'host_booking_rate' => [
                'type' => 'TEXT',
                'default' => null,
                'null' => true,
            ],
            'host_booking_rate_id' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'host_booking_promo' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'host_booking_status' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'host_booking_activation_date' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'host_booking_ip_connection' => [
                'type' => 'VARCHAR',
                'constraint' => '15',
                'default' => null,
                'null' => true,
            ],
            'host_booking_last_update' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);
        $this->forge->addPrimaryKey('host_booking_id');
        $this->forge->createTable('host_bookings');
    }

    public function down()
    {
        $this->forge->dropTable('host_bookings');
    }
}
