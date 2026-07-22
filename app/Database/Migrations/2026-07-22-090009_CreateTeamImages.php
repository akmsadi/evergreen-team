<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTeamImages extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'team_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'image_path' => ['type' => 'VARCHAR', 'constraint' => 500],
            'caption' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('team_id', false, false, 'event_id');
        $this->forge->addForeignKey('team_id', 'teams', 'id', 'CASCADE', 'SET NULL', 'TEAM_IMAGE');
        $this->forge->createTable('team_images', true);
    }

    public function down()
    {
        $this->forge->dropTable('team_images', true);
    }
}
