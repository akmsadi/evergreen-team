<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMatchPlayerDeposits extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'match_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'player_id' => ['type' => 'INT', 'constraint' => 11],
            'amount' => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'notes' => ['type' => 'MEDIUMTEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('player_id', false, false, 'match_player_deposits_player_id_foreign');
        $this->forge->addForeignKey('player_id', 'players', 'id', 'CASCADE', 'CASCADE', 'match_player_deposits_player_id_foreign');
        $this->forge->createTable('match_player_deposits', true);
    }

    public function down()
    {
        $this->forge->dropTable('match_player_deposits', true);
    }
}
