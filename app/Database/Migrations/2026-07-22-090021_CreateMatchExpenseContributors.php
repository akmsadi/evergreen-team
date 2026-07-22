<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMatchExpenseContributors extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'expense_id' => ['type' => 'INT', 'constraint' => 11],
            'player_id' => ['type' => 'INT', 'constraint' => 11],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('expense_id', false, false, 'match_expense_contributors_expense_id_foreign');
        $this->forge->addKey('player_id', false, false, 'match_expense_contributors_player_id_foreign');
        $this->forge->addForeignKey('expense_id', 'match_expenses', 'id', 'CASCADE', 'CASCADE', 'match_expense_contributors_expense_id_foreign');
        $this->forge->addForeignKey('player_id', 'players', 'id', 'CASCADE', 'CASCADE', 'match_expense_contributors_player_id_foreign');
        $this->forge->createTable('match_expense_contributors', true);
    }

    public function down()
    {
        $this->forge->dropTable('match_expense_contributors', true);
    }
}
