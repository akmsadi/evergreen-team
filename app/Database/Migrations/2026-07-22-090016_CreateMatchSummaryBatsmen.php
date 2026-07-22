<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMatchSummaryBatsmen extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'match_id' => ['type' => 'INT', 'constraint' => 11],
            'innings_number' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true],
            'sort_order' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'player_id' => ['type' => 'INT', 'constraint' => 11],
            'runs' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => true, 'default' => 0],
            'balls' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => true, 'default' => 0],
            'fours' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => true, 'default' => 0],
            'sixes' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => true, 'default' => 0],
            'dismissal_mode' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'dismissal_by' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'fielder_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('match_id', false, false, 'match_id');
        $this->forge->addKey('player_id', false, false, 'player_id');
        $this->forge->addKey('dismissal_by', false, false, 'fk_msb_dismissal_by');
        $this->forge->addKey('fielder_id', false, false, 'fk_msb_fielder_id');
        $this->forge->addForeignKey('match_id', 'matches', 'id', 'CASCADE', 'CASCADE', 'match_summary_batsmen_ibfk_1');
        $this->forge->addForeignKey('player_id', 'players', 'id', 'CASCADE', 'CASCADE', 'match_summary_batsmen_ibfk_2');
        $this->forge->addForeignKey('dismissal_by', 'players', 'id', 'CASCADE', 'SET NULL', 'fk_msb_dismissal_by');
        $this->forge->addForeignKey('fielder_id', 'players', 'id', 'CASCADE', 'SET NULL', 'fk_msb_fielder_id');
        $this->forge->createTable('match_summary_batsmen', true);
    }

    public function down()
    {
        $this->forge->dropTable('match_summary_batsmen', true);
    }
}
