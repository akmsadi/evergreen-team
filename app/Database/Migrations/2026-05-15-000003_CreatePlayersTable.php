<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePlayersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'organization' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'email' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'phone' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'address' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'total_contribution' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
            ],
            'total_expense' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'pending',
            ],
            'guest_of_player_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('guest_of_player_id', 'players', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('players');
    }

    public function down()
    {
        $this->forge->dropTable('players');
    }
}
