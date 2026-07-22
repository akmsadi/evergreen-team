<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMatchSummaryBowlers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'match_id' => ['type' => 'INT', 'constraint' => 11],
            'innings_number' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true],
            'sort_order' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'player_id' => ['type' => 'INT', 'constraint' => 11],
            'overs' => ['type' => 'DECIMAL', 'constraint' => '5,1', 'null' => true, 'default' => 0.0],
            'maidens' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => true, 'default' => 0],
            'runs' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => true, 'default' => 0],
            'wickets' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => true, 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('match_id', false, false, 'match_id');
        $this->forge->addKey('player_id', false, false, 'player_id');
        $this->forge->addForeignKey('match_id', 'matches', 'id', 'CASCADE', 'CASCADE', 'match_summary_bowlers_ibfk_1');
        $this->forge->addForeignKey('player_id', 'players', 'id', 'CASCADE', 'CASCADE', 'match_summary_bowlers_ibfk_2');
        $this->forge->createTable('match_summary_bowlers', true);
    }

    public function down()
    {
        $this->forge->dropTable('match_summary_bowlers', true);
    }
}
