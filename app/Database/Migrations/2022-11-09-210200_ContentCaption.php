<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class ContentCaption extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'content_caption_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'auto_increment' => true,
            ],
            'content_caption_host_id' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'content_caption_type' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'content_caption_connection_id' => [
                'type' => 'INT',
                'constraint' => '10',
            ],
            'content_caption' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
            ],
            'content_caption_lang' => [
                'type' => 'VARCHAR',
                'constraint' => '2',
            ],
            'content_caption_status' => [
                'type' => 'INT',
                'constraint' => '11',
            ],
            'content_activation' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql(
                    'CURRENT_TIMESTAMP'
                ),
            ],
        ]);
        $this->forge->addPrimaryKey('content_caption_id');
        $this->forge->createTable('content_captions');
    }

    public function down()
    {
        $this->forge->dropTable('content_captions');
    }
}
