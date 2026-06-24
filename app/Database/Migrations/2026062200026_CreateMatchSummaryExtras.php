<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMatchSummaryExtras extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'match_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => false],
            'innings_number' => ['type' => 'TINYINT', 'constraint' => 1],
            'byes'           => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'leg_byes'       => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'wides'          => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'no_balls'       => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['match_id', 'innings_number']);
        $this->forge->createTable('match_summary_extras');

        $this->db->query('ALTER TABLE match_summary_extras ADD CONSTRAINT fk_extras_match_id FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE ON UPDATE CASCADE');
    }

    public function down()
    {
        $this->forge->dropTable('match_summary_extras', true);
    }
}
