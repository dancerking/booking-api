<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class Language extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'language_code' => [
                'type' => 'VARCHAR',
                'constraint' => '2',
            ],
            'language_status' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'language_activation' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'language_last_update' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);
        $this->forge->addPrimaryKey('language_code');
        $this->forge->createTable('languages');
    }

    public function down()
    {
        $this->forge->dropTable('languages');
    }
}
