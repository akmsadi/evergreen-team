<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEventImages extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11],
            'event_id' => ['type' => 'INT', 'constraint' => 11],
            'image_path' => ['type' => 'VARCHAR', 'constraint' => 500],
            'caption' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true]
        ]);

        // Review keys and foreign keys manually after generation.
        $this->forge->createTable('event_images', true);
    }

    public function down()
    {
        $this->forge->dropTable('event_images', true);
    }
}
