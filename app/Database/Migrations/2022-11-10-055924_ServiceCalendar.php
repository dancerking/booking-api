<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class ServiceCalendar extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'service_price_id' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'service_price_host_id' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'service_price_code' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'service_price_type' => [
                'type' => 'VARCHAR',
                'constraint' => '30',
            ],
            'service_price_day' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql(
                    'CURRENT_TIMESTAMP'
                ),
            ],
            'service_price' => [
                'type' => 'DECIMAL',
                'constraint' => '10, 2',
            ],
        ]);
        $this->forge->addPrimaryKey('service_price_id');
        $this->forge->createTable('services_calendar');
    }

    public function down()
    {
        $this->forge->dropTable('services_calendar');
    }
}
