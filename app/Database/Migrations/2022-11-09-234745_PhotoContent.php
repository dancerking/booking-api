<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class PhotoContent extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'photo_content_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'auto_increment' => true,
            ],
            'photo_content_host_id' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'photo_content_level' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'photo_content_connection' => [
                'type' => 'VARCHAR',
                'constraint' => '30',
            ],
            'photo_content_url' => [
                'type' => 'VARCHAR',
                'constraint' => '200',
            ],
            'photo_content_order' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'photo_content_status' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'photo_content_activation' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql(
                    'CURRENT_TIMESTAMP'
                ),
            ],
            'photo_content_last_update' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql(
                    'CURRENT_TIMESTAMP'
                ),
                'null' => true,
            ],
        ]);
        $this->forge->addPrimaryKey('photo_content_id');
        $this->forge->createTable('photo_contents');
    }

    public function down()
    {
        $this->forge->dropTable('photo_contents');
    }
}
