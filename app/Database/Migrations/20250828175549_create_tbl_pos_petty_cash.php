<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Create POS Petty Cash Entries Table
 *
 * Records petty cash IN/OUT tied to an active shift and outlet.
 */
class CreatePosPettyCashTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'shift_id'        => ['type' => 'INT', 'unsigned' => true],
            'outlet_id'       => ['type' => 'INT', 'unsigned' => true],
            'kasir_user_id'   => ['type' => 'MEDIUMINT', 'unsigned' => true],
            'category_id'     => ['type' => 'INT', 'unsigned' => true, 'null' => true],

            'direction'       => ['type' => 'ENUM', 'constraint' => ['IN','OUT'], 'default' => 'OUT'],
            'amount'          => ['type' => 'DECIMAL', 'constraint' => '18,2', 'default' => 0.00],
            'reason'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'ref_no'          => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'attachment_path' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],

            'status'          => ['type' => 'ENUM', 'constraint' => ['draft','posted','void'], 'default' => 'posted'],
            'approved_by'     => ['type' => 'MEDIUMINT', 'unsigned' => true, 'null' => true],
            'approved_at'     => ['type' => 'DATETIME', 'null' => true],

            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['shift_id']);
        $this->forge->addKey(['outlet_id']);
        $this->forge->addKey(['kasir_user_id']);
        $this->forge->addKey(['category_id']);
        $this->forge->addKey(['status']);
        $this->forge->addKey(['direction']);
        $this->forge->addKey(['created_at']);

        // Foreign keys
        $this->forge->addForeignKey('shift_id', 'tbl_m_shift', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('outlet_id', 'tbl_m_gudang', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('kasir_user_id', 'tbl_ion_users', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->addForeignKey('category_id', 'tbl_m_petty_category', 'id', 'SET NULL', 'RESTRICT');
        $this->forge->addForeignKey('approved_by', 'tbl_ion_users', 'id', 'SET NULL', 'RESTRICT');

        $this->forge->createTable('tbl_pos_petty_cash', true, ['ENGINE' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_pos_petty_cash', true);
    }
}
