<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePlayers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11],
            'name' => ['type' => 'VARCHAR'],
            'organization' => ['type' => 'VARCHAR', 'null' => true],
            'email' => ['type' => 'VARCHAR', 'null' => true],
            'phone' => ['type' => 'VARCHAR'],
            'address' => ['type' => 'VARCHAR'],
            'total_contribution' => ['type' => 'INT', 'constraint' => 11],
            'total_expense' => ['type' => 'INT', 'constraint' => 11],
            'status' => ['type' => 'VARCHAR', 'constraint' => 255],
            'guest_of_player_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true]
        ]);

        // Review keys and foreign keys manually after generation.
        $this->forge->createTable('players', true);
    }

    public function down()
    {
        $this->forge->dropTable('players', true);
    }
}
