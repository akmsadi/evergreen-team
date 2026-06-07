<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAdminHistoryTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'admin_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'admin_username' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'action' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'table_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
            ],
            'row_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
            ],
            'old_values' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'new_values' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'request_method' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            'request_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('admin_id');
        $this->forge->addKey(['table_name', 'row_id']);
        $this->forge->addKey('created_at');
        $this->forge->addForeignKey('admin_id', 'admins', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('admin_history');
    }

    public function down()
    {
        $this->forge->dropTable('admin_history', true);
    }
}
