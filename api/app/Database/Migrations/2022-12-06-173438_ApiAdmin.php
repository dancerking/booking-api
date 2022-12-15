<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class ApiAdmin extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'api_admin_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'auto_increment' => true,
            ],
            'api_admin_username' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
            ],
            'api_admin_password' => [
                'type' => 'VARCHAR',
                'constraint' => '200',
            ],
            'api_admin_activation' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql(
                    'CURRENT_TIMESTAMP'
                ),
            ],
            'api_admin_status' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
        ]);
        $this->forge->addPrimaryKey('api_admin_id');
        $this->forge->createTable('api_admins');
    }

    public function down()
    {
        $this->forge->dropTable('api_admins');
    }
}
