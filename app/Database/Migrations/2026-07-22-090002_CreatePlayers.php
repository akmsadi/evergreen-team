<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePlayers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'app_id' => ['type' => 'CHAR', 'constraint' => 32, 'null' => true],
            'name' => ['type' => 'MEDIUMTEXT'],
            'organization' => ['type' => 'MEDIUMTEXT', 'null' => true],
            'email' => ['type' => 'MEDIUMTEXT', 'null' => true],
            'phone' => ['type' => 'MEDIUMTEXT'],
            'address' => ['type' => 'MEDIUMTEXT'],
            'total_contribution' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'total_expense' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'status' => ['type' => 'VARCHAR', 'constraint' => 255, 'default' => 'pending'],
            'guest_of_player_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('app_id', 'APP_ID');
        $this->forge->createTable('players', true);
    }

    public function down()
    {
        $this->forge->dropTable('players', true);
    }
}
