<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterMatchSummaryBatsmenDismissalBy extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('match_summary_batsmen', [
            'dismissal_by' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
        ]);

        $this->db->query('ALTER TABLE match_summary_batsmen ADD CONSTRAINT fk_msb_dismissal_by FOREIGN KEY (dismissal_by) REFERENCES players(id) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE match_summary_batsmen DROP FOREIGN KEY fk_msb_dismissal_by');

        $this->forge->modifyColumn('match_summary_batsmen', [
            'dismissal_by' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
        ]);
    }
}
