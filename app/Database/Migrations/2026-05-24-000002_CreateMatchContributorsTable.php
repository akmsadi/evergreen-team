<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMatchContributorsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'match_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'player_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
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
        $this->forge->addKey(['match_id', 'player_id'], false, true);
        $this->forge->addForeignKey('match_id', 'matches', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('player_id', 'players', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('match_contributors');

        $rows = $this->db->table('match_expense_contributors')
            ->select('match_expenses.match_id, match_expense_contributors.player_id')
            ->join('match_expenses', 'match_expenses.id = match_expense_contributors.expense_id')
            ->distinct()
            ->get()
            ->getResultArray();

        if ($rows === []) {
            return;
        }

        $timestamp = date('Y-m-d H:i:s');
        $payload = array_map(static fn(array $row): array => [
            'match_id' => (int) $row['match_id'],
            'player_id' => (int) $row['player_id'],
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ], $rows);

        $this->db->table('match_contributors')->insertBatch($payload);
    }

    public function down()
    {
        $this->forge->dropTable('match_contributors', true);
    }
}
