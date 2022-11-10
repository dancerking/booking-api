<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RateMapping extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'rate_mapping_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'auto_increment' => true,
            ],
            'rate_mapping_host_id' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'rate_mapping_rates_id' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'rate_mapping_type_code' => [
                'type' => 'VARCHAR',
                'constraint' => '30',
            ],
            'rate_mapping_downpayment_percentage' => [
                'type' => 'DECIMAL',
                'constraint' => '10, 2',
            ],
            'rate_mapping_alt_fixed_price' => [
                'type' => 'DECIMAL',
                'constraint' => '10, 2',
                'default' => '0.00',
            ],
        ]);
        $this->forge->addPrimaryKey('rate_mapping_id');
        $this->forge->createTable('rates_mapping');
    }

    public function down()
    {
        $this->forge->dropTable('rates_mapping');
    }
}
