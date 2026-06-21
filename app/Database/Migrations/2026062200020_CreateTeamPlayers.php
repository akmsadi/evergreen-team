<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTeamPlayers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11],
            'team_id' => ['type' => 'INT', 'constraint' => 11],
            'player_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true]
        ]);

        // Review keys and foreign keys manually after generation.
        $this->forge->createTable('team_players', true);
    }

    public function down()
    {
        $this->forge->dropTable('team_players', true);
    }
}
