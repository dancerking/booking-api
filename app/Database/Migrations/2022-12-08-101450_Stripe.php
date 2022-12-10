<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Stripe extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'stripe_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'auto_increment' => true,
            ],
            'stripe_host_id' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'stripe_public' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
            ],
            'stripe_secret' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
            ],
            'stripe_status' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
        ]);
        $this->forge->addPrimaryKey('stripe_id');
        $this->forge->createTable('stripe_data');
    }

    public function down()
    {
        $this->forge->dropTable('stripe_data');
    }
}
