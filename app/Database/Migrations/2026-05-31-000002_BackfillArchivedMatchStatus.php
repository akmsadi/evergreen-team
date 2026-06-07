<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BackfillArchivedMatchStatus extends Migration
{
    public function up()
    {
        $this->db->table('matches')
            ->where('deleted_at IS NOT NULL', null, false)
            ->where('match_status !=', 'archived')
            ->update([
                'match_status' => 'archived',
            ]);
    }

    public function down()
    {
        $this->db->table('matches')
            ->where('match_status', 'archived')
            ->where('deleted_at IS NOT NULL', null, false)
            ->update([
                'match_status' => 'scheduled',
            ]);
    }
}
