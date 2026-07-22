<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMatchBalls extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'match_id' => ['type' => 'INT', 'constraint' => 11],
            'innings_id' => ['type' => 'INT', 'constraint' => 11],
            'striker_player_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'non_striker_player_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'bowler_player_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'fielder_player_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'over_number' => ['type' => 'INT', 'constraint' => 11],
            'ball_in_over' => ['type' => 'INT', 'constraint' => 11],
            'ball_code' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'runs_bat' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'extras' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'extra_type' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'total_runs' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'is_legal_delivery' => ['type' => 'TINYINT', 'constraint' => 4, 'default' => 1],
            'is_boundary' => ['type' => 'TINYINT', 'constraint' => 4, 'default' => 0],
            'wicket_type' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'is_wicket' => ['type' => 'TINYINT', 'constraint' => 4, 'default' => 0],
            'dismissed_player_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'partnership_runs' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'score_after_ball' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'commentary' => ['type' => 'MEDIUMTEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('match_id', false, false, 'match_balls_match_id_foreign');
        $this->forge->addKey('innings_id', false, false, 'match_balls_innings_id_foreign');
        $this->forge->addKey('striker_player_id', false, false, 'match_balls_striker_player_id_foreign');
        $this->forge->addKey('non_striker_player_id', false, false, 'match_balls_non_striker_player_id_foreign');
        $this->forge->addKey('bowler_player_id', false, false, 'match_balls_bowler_player_id_foreign');
        $this->forge->addKey('fielder_player_id', false, false, 'match_balls_fielder_player_id_foreign');
        $this->forge->addKey('dismissed_player_id', false, false, 'match_balls_dismissed_player_id_foreign');
        $this->forge->addForeignKey('match_id', 'matches', 'id', 'CASCADE', 'CASCADE', 'match_balls_match_id_foreign');
        $this->forge->addForeignKey('innings_id', 'match_innings', 'id', 'CASCADE', 'CASCADE', 'match_balls_innings_id_foreign');
        $this->forge->addForeignKey('striker_player_id', 'players', 'id', 'SET NULL', 'CASCADE', 'match_balls_striker_player_id_foreign');
        $this->forge->addForeignKey('non_striker_player_id', 'players', 'id', 'SET NULL', 'CASCADE', 'match_balls_non_striker_player_id_foreign');
        $this->forge->addForeignKey('bowler_player_id', 'players', 'id', 'SET NULL', 'CASCADE', 'match_balls_bowler_player_id_foreign');
        $this->forge->addForeignKey('fielder_player_id', 'players', 'id', 'SET NULL', 'CASCADE', 'match_balls_fielder_player_id_foreign');
        $this->forge->addForeignKey('dismissed_player_id', 'players', 'id', 'SET NULL', 'CASCADE', 'match_balls_dismissed_player_id_foreign');
        $this->forge->createTable('match_balls', true);
    }

    public function down()
    {
        $this->forge->dropTable('match_balls', true);
    }
}
