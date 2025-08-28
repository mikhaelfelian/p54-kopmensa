<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Create Petty Cash Category Master
 *
 * Optional category master for petty cash entries.
 */
class CreatePettyCategoryTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'is_active'   => ['type' => 'ENUM', 'constraint' => ['0','1'], 'default' => '1'],

            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['name'], false, true); // unique
        $this->forge->createTable('tbl_m_petty_category', true, ['ENGINE' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_m_petty_category', true);
    }
}
