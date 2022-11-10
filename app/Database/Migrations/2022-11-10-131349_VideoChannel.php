<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class VideoChannel extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'video_channel_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'auto_increment' => true,
            ],
            'video_channel_name' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
            ],
            'video_channel_settings' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'video_channel_status' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
        ]);
        $this->forge->addPrimaryKey('video_channel_id');
        $this->forge->createTable('video_channels');
    }

    public function down()
    {
        $this->forge->dropTable('video_channels');
    }
}
