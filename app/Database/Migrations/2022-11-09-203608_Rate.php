<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Rate extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'rate_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'auto_increment' => true,
            ],
            'rate_host_id' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'rate_setting' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'rate_master' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'rate_discount_markup' => [
                'type' => 'DECIMAL',
                'constraint' => '10, 2',
            ],
            'rate_guests_included' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'rate_downpayment' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'rate_from_check-in' => [
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
            ],
            'rate_status' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
        ]);
        $this->forge->addPrimaryKey('rate_id');
        $this->forge->createTable('rates');
    }

    public function down()
    {
        $this->forge->dropTable('rates');
    }
}
