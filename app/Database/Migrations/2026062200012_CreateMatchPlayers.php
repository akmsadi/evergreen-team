<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMatchPlayers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11],
            'match_id' => ['type' => 'INT', 'constraint' => 11],
            'player_id' => ['type' => 'INT', 'constraint' => 11],
            'side' => ['type' => 'VARCHAR', 'constraint' => 255],
            'role' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'shirt_number' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'batting_position' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'is_captain' => ['type' => 'TINYINT', 'constraint' => 4],
            'is_wicketkeeper' => ['type' => 'TINYINT', 'constraint' => 4],
            'playing_xi' => ['type' => 'TINYINT', 'constraint' => 4],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true]
        ]);

        // Review keys and foreign keys manually after generation.
        $this->forge->createTable('match_players', true);
    }

    public function down()
    {
        $this->forge->dropTable('match_players', true);
    }
}
