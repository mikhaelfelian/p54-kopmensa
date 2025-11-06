<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-18
 * GitHub: github.com/mikhaelfelian
 * Description: Migration for adding nama_shift, catatan_shift, and approved_at fields to tbl_m_shift table
 */

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddShiftFields extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // Add nama_shift column (shift name)
        $query = $db->query("SHOW COLUMNS FROM tbl_m_shift LIKE 'nama_shift'");
        if ($query->getNumRows() === 0) {
            $this->forge->addColumn('tbl_m_shift', [
                'nama_shift' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                    'after'      => 'shift_code',
                    'comment'    => 'Shift name/description'
                ],
            ]);
        }

        // Add catatan_shift column (shift notes)
        $query = $db->query("SHOW COLUMNS FROM tbl_m_shift LIKE 'catatan_shift'");
        if ($query->getNumRows() === 0) {
            $this->forge->addColumn('tbl_m_shift', [
                'catatan_shift' => [
                    'type'    => 'TEXT',
                    'null'    => true,
                    'after'   => 'notes',
                    'comment' => 'Additional notes for the shift'
                ],
            ]);
        }

        // Add approved_at column (approval timestamp)
        $query = $db->query("SHOW COLUMNS FROM tbl_m_shift LIKE 'approved_at'");
        if ($query->getNumRows() === 0) {
            $this->forge->addColumn('tbl_m_shift', [
                'approved_at' => [
                    'type'    => 'DATETIME',
                    'null'    => true,
                    'after'   => 'updated_at',
                    'comment' => 'Timestamp when shift was approved'
                ],
            ]);
        }

        // Add indexes for faster queries
        // Check if index exists before adding
        $indexes = $db->query("SHOW INDEXES FROM tbl_m_shift WHERE Key_name = 'idx_user_status'")->getResult();
        if (empty($indexes)) {
            // Add composite index using raw SQL since forge doesn't support named composite indexes easily
            $db->query("ALTER TABLE tbl_m_shift ADD INDEX idx_user_status (user_open_id, status)");
        }

        $indexes = $db->query("SHOW INDEXES FROM tbl_m_shift WHERE Key_name = 'idx_approved_at'")->getResult();
        if (empty($indexes)) {
            $this->forge->addKey('approved_at', false, false, 'idx_approved_at');
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        
        // Drop indexes
        $indexes = $db->query("SHOW INDEXES FROM tbl_m_shift WHERE Key_name = 'idx_user_status'")->getResult();
        if (!empty($indexes)) {
            $db->query("ALTER TABLE tbl_m_shift DROP INDEX idx_user_status");
        }

        $indexes = $db->query("SHOW INDEXES FROM tbl_m_shift WHERE Key_name = 'idx_approved_at'")->getResult();
        if (!empty($indexes)) {
            $this->forge->dropKey('tbl_m_shift', 'idx_approved_at');
        }

        // Drop columns
        $query = $db->query("SHOW COLUMNS FROM tbl_m_shift LIKE 'approved_at'");
        if ($query->getNumRows() > 0) {
            $this->forge->dropColumn('tbl_m_shift', 'approved_at');
        }

        $query = $db->query("SHOW COLUMNS FROM tbl_m_shift LIKE 'catatan_shift'");
        if ($query->getNumRows() > 0) {
            $this->forge->dropColumn('tbl_m_shift', 'catatan_shift');
        }

        $query = $db->query("SHOW COLUMNS FROM tbl_m_shift LIKE 'nama_shift'");
        if ($query->getNumRows() > 0) {
            $this->forge->dropColumn('tbl_m_shift', 'nama_shift');
        }
    }
}

