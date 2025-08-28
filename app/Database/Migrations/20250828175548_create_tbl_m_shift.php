<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Create Shift Master Table
 *
 * Tracks cash register shifts per outlet and user,
 * including opening float, sales cash total, petty totals,
 * expected vs counted cash, and approval info.
 */
class CreateShiftTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'shift_code'        => ['type' => 'VARCHAR', 'constraint' => 30],
            'outlet_id'         => ['type' => 'INT', 'unsigned' => true],
            'user_open_id'      => ['type' => 'MEDIUMINT', 'unsigned' => true],
            'user_close_id'     => ['type' => 'MEDIUMINT', 'unsigned' => true, 'null' => true],
            'user_approve_id'   => ['type' => 'MEDIUMINT', 'unsigned' => true, 'null' => true],

            'start_at'          => ['type' => 'DATETIME'],
            'end_at'            => ['type' => 'DATETIME', 'null' => true],

            'open_float'        => ['type' => 'DECIMAL', 'constraint' => '18,2', 'default' => 0.00],
            'sales_cash_total'  => ['type' => 'DECIMAL', 'constraint' => '18,2', 'default' => 0.00],
            'petty_in_total'    => ['type' => 'DECIMAL', 'constraint' => '18,2', 'default' => 0.00],
            'petty_out_total'   => ['type' => 'DECIMAL', 'constraint' => '18,2', 'default' => 0.00],

            'expected_cash'     => ['type' => 'DECIMAL', 'constraint' => '18,2', 'default' => 0.00],
            'counted_cash'      => ['type' => 'DECIMAL', 'constraint' => '18,2', 'null' => true],
            'diff_cash'         => ['type' => 'DECIMAL', 'constraint' => '18,2', 'null' => true],

            'status'            => ['type' => 'ENUM', 'constraint' => ['open','closed','approved','void'], 'default' => 'open'],
            'notes'             => ['type' => 'TEXT', 'null' => true],

            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['shift_code'], false, true); // unique
        $this->forge->addKey(['outlet_id']);
        $this->forge->addKey(['user_open_id']);
        $this->forge->addKey(['status']);
        $this->forge->addKey(['start_at']);
        $this->forge->addKey(['end_at']);

        // Foreign keys
        $this->forge->addForeignKey('outlet_id', 'tbl_m_gudang', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('user_open_id', 'tbl_ion_users', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->addForeignKey('user_close_id', 'tbl_ion_users', 'id', 'SET NULL', 'RESTRICT');
        $this->forge->addForeignKey('user_approve_id', 'tbl_ion_users', 'id', 'SET NULL', 'RESTRICT');

        $this->forge->createTable('tbl_m_shift', true, ['ENGINE' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_m_shift', true);
    }
}
