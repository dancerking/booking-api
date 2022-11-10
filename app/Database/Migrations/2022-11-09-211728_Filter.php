<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class Filter extends Migration
{
    public function up()
    {
       $this->forge->addField([
            'filter_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'auto_increment' => true,
            ],
            'filter_code' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
            ],
            'filter_level' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'filter_name' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
            ],
            'filter_lang' => [
                'type' => 'VARCHAR',
                'constraint' => '2',
            ],
            'filter_status' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'filter_activation' => [
                'type'     => 'TIMESTAMP',
                'default'  => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'filter_lastupdate' => [
                'type'     => 'TIMESTAMP',
                'default'  => new RawSql('CURRENT_TIMESTAMP'),
                'null' => true,
            ],
        ]);
        $this->forge->addPrimaryKey('filter_id');
        $this->forge->createTable('filters');
    }

    public function down()
    {
        $this->forge->dropTable('filters');
    }
}
