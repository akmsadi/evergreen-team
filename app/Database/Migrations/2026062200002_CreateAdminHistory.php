<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAdminHistory extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11],
            'admin_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'admin_username' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'action' => ['type' => 'VARCHAR', 'constraint' => 255],
            'table_name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'row_id' => ['type' => 'VARCHAR', 'constraint' => 255],
            'old_values' => ['type' => 'LONGTEXT', 'null' => true],
            'new_values' => ['type' => 'LONGTEXT', 'null' => true],
            'request_method' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'request_path' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true]
        ]);

        // Review keys and foreign keys manually after generation.
        $this->forge->createTable('admin_history', true);
    }

    public function down()
    {
        $this->forge->dropTable('admin_history', true);
    }
}
