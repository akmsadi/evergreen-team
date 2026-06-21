<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMatchBalls extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11],
            'match_id' => ['type' => 'INT', 'constraint' => 11],
            'innings_id' => ['type' => 'INT', 'constraint' => 11],
            'striker_player_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'non_striker_player_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'bowler_player_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'fielder_player_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'over_number' => ['type' => 'INT', 'constraint' => 11],
            'ball_in_over' => ['type' => 'INT', 'constraint' => 11],
            'ball_code' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'runs_bat' => ['type' => 'INT', 'constraint' => 11],
            'extras' => ['type' => 'INT', 'constraint' => 11],
            'extra_type' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'total_runs' => ['type' => 'INT', 'constraint' => 11],
            'is_legal_delivery' => ['type' => 'TINYINT', 'constraint' => 4],
            'is_boundary' => ['type' => 'TINYINT', 'constraint' => 4],
            'wicket_type' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'is_wicket' => ['type' => 'TINYINT', 'constraint' => 4],
            'dismissed_player_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'partnership_runs' => ['type' => 'INT', 'constraint' => 11],
            'score_after_ball' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'commentary' => ['type' => 'VARCHAR', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true]
        ]);

        // Review keys and foreign keys manually after generation.
        $this->forge->createTable('match_balls', true);
    }

    public function down()
    {
        $this->forge->dropTable('match_balls', true);
    }
}
