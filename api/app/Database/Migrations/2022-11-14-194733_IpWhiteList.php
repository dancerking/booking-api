<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class IpWhiteList extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'ip_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'auto_increment' => true,
            ],
            'white_ip' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
            ],
            'host_id' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
        ]);
        $this->forge->addPrimaryKey('ip_id');
        $this->forge->createTable('ip_white_list', true);
    }

    public function down()
    {
        $this->forge->dropTable('ip_white_list');
    }
}
