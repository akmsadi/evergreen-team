<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMatchInnings extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'match_id' => ['type' => 'INT', 'constraint' => 11],
            'innings_number' => ['type' => 'INT', 'constraint' => 11],
            'batting_side' => ['type' => 'VARCHAR', 'constraint' => 255],
            'bowling_side' => ['type' => 'VARCHAR', 'constraint' => 255],
            'runs' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'wickets' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'overs' => ['type' => 'DECIMAL', 'constraint' => '10,0', 'default' => 0],
            'balls' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'extras' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'byes' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'leg_byes' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'wides' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'no_balls' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'target_runs' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'required_run_rate' => ['type' => 'DECIMAL', 'constraint' => '10,0', 'null' => true],
            'completed' => ['type' => 'TINYINT', 'constraint' => 4, 'default' => 0],
            'opening_striker_player_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'opening_non_striker_player_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'opening_bowler_player_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('match_id', false, false, 'match_innings_match_id_foreign');
        $this->forge->addForeignKey('match_id', 'matches', 'id', 'CASCADE', 'CASCADE', 'match_innings_match_id_foreign');
        $this->forge->createTable('match_innings', true);
    }

    public function down()
    {
        $this->forge->dropTable('match_innings', true);
    }
}
