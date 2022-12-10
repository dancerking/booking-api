<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Log extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'log_id' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'log_host_id' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'log_time' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'log_request' => [
                'type' => 'TEXT',
            ],
            'log_response' => [
                'type' => 'TEXT',
            ],
            'log_http_response' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
            ],
            'log_error' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'log_email_user' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'log_email_admin' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
        ]);
        $this->forge->addPrimaryKey('log_id');
        $this->forge->createTable('logs');
    }

    public function down()
    {
        $this->forge->dropTable('logs');
    }
}
