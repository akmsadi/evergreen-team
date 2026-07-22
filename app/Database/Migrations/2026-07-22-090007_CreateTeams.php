<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTeams extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'slug' => ['type' => 'VARCHAR', 'constraint' => 255],
            'slogan' => ['type' => 'VARCHAR', 'constraint' => 255],
            'about' => ['type' => 'TEXT'],
            'logo' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'banner' => ['type' => 'VARCHAR', 'constraint' => 255],
            'established' => ['type' => 'INT', 'constraint' => 11],
            'matches' => ['type' => 'INT', 'constraint' => 11],
            'wins' => ['type' => 'INT', 'constraint' => 11],
            'titles' => ['type' => 'INT', 'constraint' => 11],
            'captain_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'vice_captain_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('captain_id', false, false, 'CAPTAIN_PLAYER');
        $this->forge->addKey('vice_captain_id', false, false, 'VICE_CAPTAIN_PLAYER');
        $this->forge->addForeignKey('captain_id', 'players', 'id', 'CASCADE', 'SET NULL', 'CAPTAIN_PLAYER');
        $this->forge->addForeignKey('vice_captain_id', 'players', 'id', 'CASCADE', 'SET NULL', 'VICE_CAPTAIN_PLAYER');
        $this->forge->createTable('teams', true);
    }

    public function down()
    {
        $this->forge->dropTable('teams', true);
    }
}
