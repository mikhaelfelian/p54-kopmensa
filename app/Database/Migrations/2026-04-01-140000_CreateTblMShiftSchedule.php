<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Master jadwal shift per outlet (referensi jam operasional).
 */
class CreateTblMShiftSchedule extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'outlet_id'    => ['type' => 'INT', 'unsigned' => true],
            'day_of_week'  => ['type' => 'TINYINT', 'unsigned' => true, 'comment' => '1=Monday ... 7=Sunday'],
            'jam_buka'     => ['type' => 'TIME', 'null' => true],
            'jam_tutup'    => ['type' => 'TIME', 'null' => true],
            'keterangan'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'status'       => ['type' => 'CHAR', 'constraint' => 1, 'default' => '1'],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['outlet_id', 'day_of_week']);
        $this->forge->addForeignKey('outlet_id', 'tbl_m_gudang', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tbl_m_shift_schedule', true, ['ENGINE' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_m_shift_schedule', true);
    }
}
