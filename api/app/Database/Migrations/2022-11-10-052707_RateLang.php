<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RateLang extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'rate_lang_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'auto_increment' => true,
            ],
            'rate_lang_host_id' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'rate_lang_code' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'rate_lang_rules' => [
                'type' => 'TEXT',
            ],
            'rate_name' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
            ],
            'rate_short_description' => [
                'type' => 'VARCHAR',
                'constraint' => '500',
            ],
            'rates_lang' => [
                'type' => 'VARCHAR',
                'constraint' => '2',
            ],
        ]);
        $this->forge->addPrimaryKey('rate_lang_id');
        $this->forge->createTable('rates_lang');
    }

    public function down()
    {
        $this->forge->dropTable('rates_lang');
    }
}
