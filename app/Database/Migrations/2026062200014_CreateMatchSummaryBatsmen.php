<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMatchSummaryBatsmen extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11],
            'match_id' => ['type' => 'INT', 'constraint' => 11],
            'innings_number' => ['type' => 'INT', 'constraint' => 10],
            'batsman_name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'runs' => ['type' => 'INT', 'constraint' => 10, 'null' => true],
            'balls' => ['type' => 'INT', 'constraint' => 10, 'null' => true],
            'fours' => ['type' => 'INT', 'constraint' => 10, 'null' => true],
            'sixes' => ['type' => 'INT', 'constraint' => 10, 'null' => true],
            'dismissal_mode' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'dismissal_by' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true]
        ]);

        // Review keys and foreign keys manually after generation.
        $this->forge->createTable('match_summary_batsmen', true);
    }

    public function down()
    {
        $this->forge->dropTable('match_summary_batsmen', true);
    }
}
