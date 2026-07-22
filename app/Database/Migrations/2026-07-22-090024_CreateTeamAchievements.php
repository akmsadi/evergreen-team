<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTeamAchievements extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'team_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'title' => ['type' => 'VARCHAR', 'constraint' => 255],
            'description' => ['type' => 'TEXT'],
            'created_at' => ['type' => 'TIMESTAMP'],
            'updated_at' => ['type' => 'TIMESTAMP'],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('team_id', false, false, 'TEAM_ACHIEVEMENT');
        $this->forge->addForeignKey('team_id', 'teams', 'id', 'CASCADE', 'SET NULL', 'TEAM_ACHIEVEMENT');
        $this->forge->createTable('team_achievements', true);
    }

    public function down()
    {
        $this->forge->dropTable('team_achievements', true);
    }
}
