<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class Payment extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'payment_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'auto_increment' => true,
            ],
            'payment_date' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql(
                    'CURRENT_TIMESTAMP'
                ),
            ],
            'payment_value' => [
                'type' => 'DECIMAL',
                'constraint' => '10, 2',
            ],
            'payment_currency' => [
                'type' => 'VARCHAR',
                'constraint' => '3',
            ],
            'payment_booking_id' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'payment_guest_id' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'payment_method' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'payment_code' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
        ]);
        $this->forge->addPrimaryKey('payment_id');
        $this->forge->createTable('payments');
    }

    public function down()
    {
        $this->forge->dropTable('payments');
    }
}
