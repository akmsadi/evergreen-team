<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTeams extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11],
            'name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'logo' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true]
        ]);

        // Review keys and foreign keys manually after generation.
        $this->forge->createTable('teams', true);
    }

    public function down()
    {
        $this->forge->dropTable('teams', true);
    }
}
