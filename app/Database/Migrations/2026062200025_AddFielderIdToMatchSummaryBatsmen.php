<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFielderIdToMatchSummaryBatsmen extends Migration
{
    public function up()
    {
        $this->forge->addColumn('match_summary_batsmen', [
            'fielder_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
                'after'      => 'dismissal_by',
            ],
        ]);

        $this->db->query('ALTER TABLE match_summary_batsmen ADD CONSTRAINT fk_msb_fielder_id FOREIGN KEY (fielder_id) REFERENCES players(id) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE match_summary_batsmen DROP FOREIGN KEY fk_msb_fielder_id');
        $this->forge->dropColumn('match_summary_batsmen', 'fielder_id');
    }
}
