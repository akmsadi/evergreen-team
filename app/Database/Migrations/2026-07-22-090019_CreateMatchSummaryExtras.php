<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMatchSummaryExtras extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'match_id' => ['type' => 'INT', 'constraint' => 11],
            'innings_number' => ['type' => 'TINYINT', 'constraint' => 1],
            'byes' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'leg_byes' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'wides' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'no_balls' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey(['match_id', 'innings_number'], 'uq_extras_match_innings');
        $this->forge->addForeignKey('match_id', 'matches', 'id', 'CASCADE', 'CASCADE', 'fk_extras_match_id');
        $this->forge->createTable('match_summary_extras', true);
    }

    public function down()
    {
        $this->forge->dropTable('match_summary_extras', true);
    }
}
