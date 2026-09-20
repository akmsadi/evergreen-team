<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIsExternalToTeams extends Migration
{
    public function up()
    {
        $this->forge->addColumn('teams', [
            'is_external' => ['type' => 'BOOLEAN', 'default' => false, 'after' => 'vice_captain_id'],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('teams', 'is_external');
    }
}
