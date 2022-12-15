<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PromosMapping extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'promo_mapping_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'auto_increment' => true,
            ],
            'promo_mapping_host_id' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'promo_mapping_code' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
            ],
            'promo_mapping_type' => [
                'type' => 'VARCHAR',
                'constraint' => '30',
            ],
            'promo_mapping_status' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
        ]);
        $this->forge->addPrimaryKey('promo_mapping_id');
        $this->forge->createTable('promos_mapping');
    }

    public function down()
    {
        $this->forge->dropTable('promos_mapping');
    }
}
