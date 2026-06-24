<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMatchSummaryBowlers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11],
            'match_id' => ['type' => 'INT', 'constraint' => 11],
            'innings_number' => ['type' => 'INT', 'constraint' => 10],
            'player_id' => ['type' => 'INT', 'constraint' => 11],
            'overs' => ['type' => 'DECIMAL', 'constraint' => 5, 'null' => true],
            'maidens' => ['type' => 'INT', 'constraint' => 10, 'null' => true],
            'runs' => ['type' => 'INT', 'constraint' => 10, 'null' => true],
            'wickets' => ['type' => 'INT', 'constraint' => 10, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true]
        ]);

        $this->forge->addForeignKey('player_id', 'players', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('match_summary_bowlers', true);
    }

    public function down()
    {
        $this->forge->dropTable('match_summary_bowlers', true);
    }
}
