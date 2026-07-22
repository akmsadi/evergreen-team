<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEventImages extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'event_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'image_path' => ['type' => 'VARCHAR', 'constraint' => 500],
            'caption' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('event_id', false, false, 'event_id');
        $this->forge->addForeignKey('event_id', 'events', 'id', 'CASCADE', 'CASCADE', 'event_images_event_id_foreign');
        $this->forge->createTable('event_images', true);
    }

    public function down()
    {
        $this->forge->dropTable('event_images', true);
    }
}
