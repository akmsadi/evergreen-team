<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterDepositAmountDecimal extends Migration
{
    public function up()
    {
        $this->db->query('ALTER TABLE match_player_deposits MODIFY COLUMN amount DECIMAL(10, 2) NOT NULL');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE match_player_deposits MODIFY COLUMN amount DECIMAL(10, 0) NOT NULL');
    }
}
