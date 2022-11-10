<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class RateCalendar extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'daily_rate_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'auto_increment' => true,
            ],
            'rate_calendar_host_id' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'daily_rate_code' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'daily_rate_type' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'daily_rate_day' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'daily_rate_baserate' => [
                'type' => 'DECIMAL',
                'constraint' => '10, 2',
            ],
            'daily_rate_guesttype_1' => [
                'type' => 'DECIMAL',
                'constraint' => '10, 2',
            ],
            'daily_rate_guesttype_2' => [
                'type' => 'DECIMAL',
                'constraint' => '10, 2',
            ],
            'daily_rate_guesttype_3' => [
                'type' => 'DECIMAL',
                'constraint' => '10, 2',
            ],
            'daily_rate_guesttype_4' => [
                'type' => 'DECIMAL',
                'constraint' => '10, 2',
            ],
            'daily_rate_minstay' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'daily_rate_maxstay' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'daily_rate_last_update' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);
        $this->forge->addPrimaryKey('daily_rate_id');
        $this->forge->createTable('rates_calendar');
    }

    public function down()
    {
        $this->forge->dropTable('rates_calendar');
    }
}
