<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMatchContributors extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'match_id' => ['type' => 'INT', 'constraint' => 11],
            'player_id' => ['type' => 'INT', 'constraint' => 11],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('match_id', false, false, 'match_contributors_match_id_foreign');
        $this->forge->addKey('player_id', false, false, 'match_contributors_player_id_foreign');
        $this->forge->addForeignKey('match_id', 'matches', 'id', 'CASCADE', 'CASCADE', 'match_contributors_match_id_foreign');
        $this->forge->addForeignKey('player_id', 'players', 'id', 'CASCADE', 'CASCADE', 'match_contributors_player_id_foreign');
        $this->forge->createTable('match_contributors', true);
    }

    public function down()
    {
        $this->forge->dropTable('match_contributors', true);
    }
}
