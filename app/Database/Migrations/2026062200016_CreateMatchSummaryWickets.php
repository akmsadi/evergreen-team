<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMatchSummaryWickets extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11],
            'match_id' => ['type' => 'INT', 'constraint' => 11],
            'innings_number' => ['type' => 'INT', 'constraint' => 10],
            'player_name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'score_at_dismissal' => ['type' => 'INT', 'constraint' => 10],
            'over_at_dismissal' => ['type' => 'DECIMAL', 'constraint' => 5],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true]
        ]);

        // Review keys and foreign keys manually after generation.
        $this->forge->createTable('match_summary_wickets', true);
    }

    public function down()
    {
        $this->forge->dropTable('match_summary_wickets', true);
    }
}
