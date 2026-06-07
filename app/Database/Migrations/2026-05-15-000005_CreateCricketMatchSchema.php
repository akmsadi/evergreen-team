<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCricketMatchSchema extends Migration
{
    public function up()
    {
        $this->createMatchesTable();
        $this->createMatchPlayersTable();
        $this->createMatchInningsTable();
        $this->createMatchBallsTable();
    }

    public function down()
    {
        $this->forge->dropTable('match_balls', true);
        $this->forge->dropTable('match_innings', true);
        $this->forge->dropTable('match_players', true);
        $this->forge->dropTable('matches', true);
    }

    private function createMatchesTable(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'match_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'default'    => 'limited_overs',
            ],
            'format_overs' => [
                'type'       => 'INT',
                'constraint' => 3,
                'unsigned'   => true,
                'default'    => 20,
            ],
            'venue' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'scheduled_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'team_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'default'    => 'Evergreen Team',
            ],
            'opponent_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'toss_winner' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'toss_decision' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'match_status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'scheduled',
            ],
            'result_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
            'result_summary' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('matches');
    }

    private function createMatchPlayersTable(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'match_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'player_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'side' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'evergreen',
            ],
            'role' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
            'shirt_number' => [
                'type'       => 'INT',
                'constraint' => 3,
                'unsigned'   => true,
                'null'       => true,
            ],
            'batting_position' => [
                'type'       => 'INT',
                'constraint' => 2,
                'unsigned'   => true,
                'null'       => true,
            ],
            'is_captain' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'is_wicketkeeper' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'playing_xi' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['match_id', 'player_id'], false, true);
        $this->forge->addForeignKey('match_id', 'matches', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('player_id', 'players', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('match_players');
    }

    private function createMatchInningsTable(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'match_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'innings_number' => [
                'type'       => 'INT',
                'constraint' => 2,
                'unsigned'   => true,
            ],
            'batting_side' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'bowling_side' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'runs' => [
                'type'       => 'INT',
                'constraint' => 4,
                'unsigned'   => true,
                'default'    => 0,
            ],
            'wickets' => [
                'type'       => 'INT',
                'constraint' => 2,
                'unsigned'   => true,
                'default'    => 0,
            ],
            'overs' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,1',
                'default'    => 0.0,
            ],
            'balls' => [
                'type'       => 'INT',
                'constraint' => 4,
                'unsigned'   => true,
                'default'    => 0,
            ],
            'extras' => [
                'type'       => 'INT',
                'constraint' => 4,
                'unsigned'   => true,
                'default'    => 0,
            ],
            'byes' => [
                'type'       => 'INT',
                'constraint' => 4,
                'unsigned'   => true,
                'default'    => 0,
            ],
            'leg_byes' => [
                'type'       => 'INT',
                'constraint' => 4,
                'unsigned'   => true,
                'default'    => 0,
            ],
            'wides' => [
                'type'       => 'INT',
                'constraint' => 4,
                'unsigned'   => true,
                'default'    => 0,
            ],
            'no_balls' => [
                'type'       => 'INT',
                'constraint' => 4,
                'unsigned'   => true,
                'default'    => 0,
            ],
            'target_runs' => [
                'type'       => 'INT',
                'constraint' => 4,
                'unsigned'   => true,
                'null'       => true,
            ],
            'required_run_rate' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
            ],
            'completed' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'opening_striker_player_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'opening_non_striker_player_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'opening_bowler_player_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['match_id', 'innings_number'], false, true);
        $this->forge->addForeignKey('match_id', 'matches', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('opening_striker_player_id', 'players', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('opening_non_striker_player_id', 'players', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('opening_bowler_player_id', 'players', 'id', 'SET NULL', 'CASCADE');

        $this->forge->createTable('match_innings');
    }

    private function createMatchBallsTable(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'match_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'innings_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'striker_player_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'non_striker_player_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'bowler_player_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'fielder_player_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'over_number' => [
                'type'       => 'INT',
                'constraint' => 3,
                'unsigned'   => true,
            ],
            'ball_in_over' => [
                'type'       => 'INT',
                'constraint' => 2,
                'unsigned'   => true,
            ],
            'ball_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 15,
                'null'       => true,
            ],
            'runs_bat' => [
                'type'       => 'INT',
                'constraint' => 2,
                'unsigned'   => true,
                'default'    => 0,
            ],
            'extras' => [
                'type'       => 'INT',
                'constraint' => 2,
                'unsigned'   => true,
                'default'    => 0,
            ],
            'extra_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'total_runs' => [
                'type'       => 'INT',
                'constraint' => 2,
                'unsigned'   => true,
                'default'    => 0,
            ],
            'is_legal_delivery' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'is_boundary' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'wicket_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
            'is_wicket' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'dismissed_player_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'partnership_runs' => [
                'type'       => 'INT',
                'constraint' => 4,
                'unsigned'   => true,
                'default'    => 0,
            ],
            'score_after_ball' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'commentary' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['innings_id', 'over_number', 'ball_in_over'], false, true);
        $this->forge->addForeignKey('match_id', 'matches', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('innings_id', 'match_innings', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('striker_player_id', 'players', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('non_striker_player_id', 'players', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('bowler_player_id', 'players', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('fielder_player_id', 'players', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('dismissed_player_id', 'players', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('match_balls');
    }
}
