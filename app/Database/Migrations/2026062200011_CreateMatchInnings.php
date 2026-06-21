<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMatchInnings extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11],
            'match_id' => ['type' => 'INT', 'constraint' => 11],
            'innings_number' => ['type' => 'INT', 'constraint' => 11],
            'batting_side' => ['type' => 'VARCHAR', 'constraint' => 255],
            'bowling_side' => ['type' => 'VARCHAR', 'constraint' => 255],
            'runs' => ['type' => 'INT', 'constraint' => 11],
            'wickets' => ['type' => 'INT', 'constraint' => 11],
            'overs' => ['type' => 'DECIMAL', 'constraint' => 10],
            'balls' => ['type' => 'INT', 'constraint' => 11],
            'extras' => ['type' => 'INT', 'constraint' => 11],
            'byes' => ['type' => 'INT', 'constraint' => 11],
            'leg_byes' => ['type' => 'INT', 'constraint' => 11],
            'wides' => ['type' => 'INT', 'constraint' => 11],
            'no_balls' => ['type' => 'INT', 'constraint' => 11],
            'target_runs' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'required_run_rate' => ['type' => 'DECIMAL', 'constraint' => 10, 'null' => true],
            'completed' => ['type' => 'TINYINT', 'constraint' => 4],
            'opening_striker_player_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'opening_non_striker_player_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'opening_bowler_player_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true]
        ]);

        // Review keys and foreign keys manually after generation.
        $this->forge->createTable('match_innings', true);
    }

    public function down()
    {
        $this->forge->dropTable('match_innings', true);
    }
}
