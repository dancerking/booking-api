<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TypeLang extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'type_lang_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'auto_increment' => true,
            ],
            'type_lang_host' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'type_lang_code' => [
                'type' => 'VARCHAR',
                'constraint' => '30',
            ],
            'type_lang_name' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'type_lang_description' => [
                'type' => 'VARCHAR',
                'constraint' => '500',
            ],
            'type_lang' => [
                'type' => 'VARCHAR',
                'constraint' => '2',
            ],
        ]);
        $this->forge->addPrimaryKey('type_lang_id');
        $this->forge->createTable('types_lang');
    }

    public function down()
    {
        $this->forge->dropTable('types_lang');
    }
}
