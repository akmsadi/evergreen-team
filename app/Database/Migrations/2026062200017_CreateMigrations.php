<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMigrations extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11],
            'version' => ['type' => 'VARCHAR', 'constraint' => 255],
            'class' => ['type' => 'VARCHAR', 'constraint' => 255],
            'group' => ['type' => 'VARCHAR', 'constraint' => 255],
            'namespace' => ['type' => 'VARCHAR', 'constraint' => 255],
            'time' => ['type' => 'INT', 'constraint' => 11],
            'batch' => ['type' => 'INT', 'constraint' => 11]
        ]);

        // Review keys and foreign keys manually after generation.
        $this->forge->createTable('migrations', true);
    }

    public function down()
    {
        $this->forge->dropTable('migrations', true);
    }
}
