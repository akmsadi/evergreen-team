<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MakeMatchExpenseMatchIdNullable extends Migration
{
    public function up()
    {
        $this->db->query('ALTER TABLE match_expenses MODIFY COLUMN match_id INT(11) NULL');
    }

    public function down()
    {
        $this->db->query('UPDATE match_expenses SET match_id = 0 WHERE match_id IS NULL');
        $this->db->query('ALTER TABLE match_expenses MODIFY COLUMN match_id INT(11) NOT NULL');
    }
}
