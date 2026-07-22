<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCiSessions extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'VARCHAR', 'constraint' => 128],
            'ip_address' => ['type' => 'VARCHAR', 'constraint' => 45],
            'timestamp' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'default' => 0],
            'data' => ['type' => 'BLOB'],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('timestamp', false, false, 'ci_sessions_timestamp');
        $this->forge->createTable('ci_sessions', true);
    }

    public function down()
    {
        $this->forge->dropTable('ci_sessions', true);
    }
}
