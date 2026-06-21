<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMatchContributors extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11],
            'match_id' => ['type' => 'INT', 'constraint' => 11],
            'player_id' => ['type' => 'INT', 'constraint' => 11],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true]
        ]);

        // Review keys and foreign keys manually after generation.
        $this->forge->createTable('match_contributors', true);
    }

    public function down()
    {
        $this->forge->dropTable('match_contributors', true);
    }
}
