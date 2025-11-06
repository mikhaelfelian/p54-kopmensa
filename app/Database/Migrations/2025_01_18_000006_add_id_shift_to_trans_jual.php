<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-18
 * GitHub: github.com/mikhaelfelian
 * Description: Migration for adding id_shift column to tbl_trans_jual table if it doesn't exist
 */

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIdShiftToTransJual extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // Check if id_shift column exists
        $query = $db->query("SHOW COLUMNS FROM tbl_trans_jual LIKE 'id_shift'");
        if ($query->getNumRows() === 0) {
            // Add id_shift column
            $this->forge->addColumn('tbl_trans_jual', [
                'id_shift' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'id_gudang',
                    'comment'    => 'Reference to tbl_m_shift.id'
                ],
            ]);

            // Add index for faster queries
            $this->forge->addKey('id_shift', false, false, 'idx_id_shift');
            
            // Add foreign key constraint
            $this->forge->addForeignKey('id_shift', 'tbl_m_shift', 'id', 'SET NULL', 'RESTRICT', 'fk_trans_jual_shift');
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        
        // Drop foreign key first
        $foreignKeys = $db->query("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tbl_trans_jual' AND CONSTRAINT_NAME = 'fk_trans_jual_shift'")->getResult();
        if (!empty($foreignKeys)) {
            $db->query("ALTER TABLE tbl_trans_jual DROP FOREIGN KEY fk_trans_jual_shift");
        }

        // Drop index
        $indexes = $db->query("SHOW INDEXES FROM tbl_trans_jual WHERE Key_name = 'idx_id_shift'")->getResult();
        if (!empty($indexes)) {
            $this->forge->dropKey('tbl_trans_jual', 'idx_id_shift');
        }

        // Drop column
        $query = $db->query("SHOW COLUMNS FROM tbl_trans_jual LIKE 'id_shift'");
        if ($query->getNumRows() > 0) {
            $this->forge->dropColumn('tbl_trans_jual', 'id_shift');
        }
    }
}

