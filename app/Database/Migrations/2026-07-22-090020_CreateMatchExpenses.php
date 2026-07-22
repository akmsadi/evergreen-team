<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMatchExpenses extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'match_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'title' => ['type' => 'VARCHAR', 'constraint' => 255],
            'amount' => ['type' => 'DECIMAL', 'constraint' => '10,0', 'default' => 0],
            'notes' => ['type' => 'MEDIUMTEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('match_id', false, false, 'match_expenses_match_id_foreign');
        $this->forge->addForeignKey('match_id', 'matches', 'id', 'CASCADE', 'CASCADE', 'match_expenses_match_id_foreign');
        $this->forge->createTable('match_expenses', true);
    }

    public function down()
    {
        $this->forge->dropTable('match_expenses', true);
    }
}
