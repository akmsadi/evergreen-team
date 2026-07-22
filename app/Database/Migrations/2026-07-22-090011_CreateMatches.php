<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMatches extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'match_type' => ['type' => 'VARCHAR', 'constraint' => 255, 'default' => 'limited_overs'],
            'format_overs' => ['type' => 'INT', 'constraint' => 11, 'default' => 20],
            'venue' => ['type' => 'MEDIUMTEXT', 'null' => true],
            'scheduled_at' => ['type' => 'DATETIME', 'null' => true],
            'team_name' => ['type' => 'VARCHAR', 'constraint' => 255, 'default' => 'Evergreen Team'],
            'opponent_name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'toss_winner' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'toss_decision' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'match_status' => ['type' => 'VARCHAR', 'constraint' => 255, 'default' => 'scheduled'],
            'result_type' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'result_summary' => ['type' => 'MEDIUMTEXT', 'null' => true],
            'notes' => ['type' => 'MEDIUMTEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
            'venue_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('matches', true);
    }

    public function down()
    {
        $this->forge->dropTable('matches', true);
    }
}
