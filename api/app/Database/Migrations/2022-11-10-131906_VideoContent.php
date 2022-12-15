<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class VideoContent extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'video_content_id' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'video_content_host_id' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'video_content_channel' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'video_content_code' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'video_content_level' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'video_order' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'video_content_connection' => [
                'type' => 'VARCHAR',
                'constraint' => '30',
            ],
            'video_content_status' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'video_content_activation' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql(
                    'CURRENT_TIMESTAMP'
                ),
            ],
            'video_content_last_update' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql(
                    'CURRENT_TIMESTAMP'
                ),
                'null' => true,
            ],
        ]);
        $this->forge->addPrimaryKey('video_content_id');
        $this->forge->createTable('video_contents');
    }

    public function down()
    {
        $this->forge->dropTable('video_contents');
    }
}
