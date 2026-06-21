<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMatchPlayerDeposits extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11],
            'match_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'player_id' => ['type' => 'INT', 'constraint' => 11],
            'amount' => ['type' => 'DECIMAL', 'constraint' => 10],
            'notes' => ['type' => 'VARCHAR', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true]
        ]);

        // Review keys and foreign keys manually after generation.
        $this->forge->createTable('match_player_deposits', true);
    }

    public function down()
    {
        $this->forge->dropTable('match_player_deposits', true);
    }
}
