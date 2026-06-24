<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSortOrderToMatchSummary extends Migration
{
    public function up()
    {
        $this->forge->addColumn('match_summary_batsmen', [
            'sort_order' => ['type' => 'INT', 'constraint' => 11, 'default' => 0, 'after' => 'innings_number'],
        ]);

        $this->forge->addColumn('match_summary_bowlers', [
            'sort_order' => ['type' => 'INT', 'constraint' => 11, 'default' => 0, 'after' => 'innings_number'],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('match_summary_batsmen', 'sort_order');
        $this->forge->dropColumn('match_summary_bowlers', 'sort_order');
    }
}
