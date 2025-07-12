<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration for adding 'limit' column to 'tbl_pengaturan' table.
 * Created by: Mikhael Felian Waskito
 * Date: 2025-01-01
 */
class Migration_20250101000001_add_limit_to_tbl_pengaturan extends Migration
{
    public function up()
    {
        // Check if the column does not exist before adding
        if (!$this->db->fieldExists('limit', 'tbl_pengaturan')) {
            $this->forge->addColumn('tbl_pengaturan', [
                'limit' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '10,2',
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'ppn',
                    'comment'    => 'Limit pengaturan'
                ]
            ]);
        }
    }

    public function down()
    {
        // Remove the column if it exists
        if ($this->db->fieldExists('limit', 'tbl_pengaturan')) {
            $this->forge->dropColumn('tbl_pengaturan', 'limit');
        }
    }
}
