<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMatches extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11],
            'match_type' => ['type' => 'VARCHAR', 'constraint' => 255],
            'format_overs' => ['type' => 'INT', 'constraint' => 11],
            'venue' => ['type' => 'VARCHAR', 'null' => true],
            'scheduled_at' => ['type' => 'DATETIME', 'null' => true],
            'team_name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'opponent_name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'toss_winner' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'toss_decision' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'match_status' => ['type' => 'VARCHAR', 'constraint' => 255],
            'result_type' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'result_summary' => ['type' => 'VARCHAR', 'null' => true],
            'notes' => ['type' => 'VARCHAR', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
            'venue_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true]
        ]);

        // Review keys and foreign keys manually after generation.
        $this->forge->createTable('matches', true);
    }

    public function down()
    {
        $this->forge->dropTable('matches', true);
    }
}
