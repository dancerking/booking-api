<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class HostAgreement extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'host_agreement_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'auto_increment' => true,
            ],
            'host_agreement_host_id' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'host_agreement_privacy' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql(
                    'CURRENT_TIMESTAMP'
                ),
            ],
            'host_agreement_newsletter' => [
                'type' => 'TIMESTAMP',
                'default' => '0000-00-00 00:00:00',
            ],
            'host_agreement_rules' => [
                'type' => 'TIMESTAMP',
                'default' => '0000-00-00 00:00:00',
            ],
        ]);
        $this->forge->addPrimaryKey('host_agreement_id');
        $this->forge->createTable('hosts_agreement');
    }

    public function down()
    {
        $this->forge->dropTable('hosts_agreement');
    }
}
