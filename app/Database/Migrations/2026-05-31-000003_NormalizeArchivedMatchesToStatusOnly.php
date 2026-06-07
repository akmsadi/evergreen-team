<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class NormalizeArchivedMatchesToStatusOnly extends Migration
{
    public function up()
    {
        $this->db->table('matches')
            ->where('match_status', 'archived')
            ->where('deleted_at IS NOT NULL', null, false)
            ->update([
                'deleted_at' => null,
            ]);
    }

    public function down() {}
}
