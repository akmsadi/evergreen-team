<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropIsWicketkeeperFromMatchPlayers extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('match_players', 'is_wicketkeeper');
    }

    public function down()
    {
        $this->forge->addColumn('match_players', [
            'is_wicketkeeper' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0, 'after' => 'is_captain'],
        ]);
    }
}
