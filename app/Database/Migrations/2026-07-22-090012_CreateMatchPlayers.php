<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMatchPlayers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'match_id' => ['type' => 'INT', 'constraint' => 11],
            'player_id' => ['type' => 'INT', 'constraint' => 11],
            'side' => ['type' => 'VARCHAR', 'constraint' => 255, 'default' => 'evergreen'],
            'role' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'shirt_number' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'batting_position' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'is_captain' => ['type' => 'TINYINT', 'constraint' => 4, 'default' => 0],
            'playing_xi' => ['type' => 'TINYINT', 'constraint' => 4, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('match_id', false, false, 'match_players_match_id_foreign');
        $this->forge->addKey('player_id', false, false, 'match_players_player_id_foreign');
        $this->forge->addForeignKey('match_id', 'matches', 'id', 'CASCADE', 'CASCADE', 'match_players_match_id_foreign');
        $this->forge->addForeignKey('player_id', 'players', 'id', 'CASCADE', 'CASCADE', 'match_players_player_id_foreign');
        $this->forge->createTable('match_players', true);
    }

    public function down()
    {
        $this->forge->dropTable('match_players', true);
    }
}
