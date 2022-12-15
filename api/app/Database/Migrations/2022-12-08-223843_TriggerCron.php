<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class TriggerCron extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'trigger_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'auto_increment' => true,
            ],
            'trigger_task' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'trigger_date' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql(
                    'CURRENT_TIMESTAMP'
                ),
            ],
            'trigger_update' => [
                'type' => 'TIMESTAMP',
                'default' => '0000-00-00 00:00:00',
            ],
            'trigger_property_id' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'trigger_type_code' => [
                'type' => 'VARCHAR',
                'constraint' => '30',
            ],
            'trigger_done' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'trigger_errors' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'trigger_mail_customer' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'trigger_mail_admin' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'trigger_mail_content' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
        ]);
        $this->forge->addPrimaryKey('trigger_id');
        $this->forge->createTable('trigger_cron');
    }

    public function down()
    {
        $this->forge->dropTable('trigger_cron');
    }
}
