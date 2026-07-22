<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTeamPlayers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'team_id' => ['type' => 'INT', 'constraint' => 11],
            'player_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('team_id', false, false, 'team_players_team_id_foreign');
        $this->forge->addKey('player_id', false, false, 'team_players_player_id_foreign');
        $this->forge->addForeignKey('team_id', 'teams', 'id', 'CASCADE', 'CASCADE', 'team_players_team_id_foreign');
        $this->forge->addForeignKey('player_id', 'players', 'id', 'CASCADE', 'SET NULL', 'team_players_player_id_foreign');
        $this->forge->createTable('team_players', true);
    }

    public function down()
    {
        $this->forge->dropTable('team_players', true);
    }
}
