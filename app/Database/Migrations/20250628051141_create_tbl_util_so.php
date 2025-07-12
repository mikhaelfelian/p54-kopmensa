<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-28
 * Github : github.com/mikhaelfelian
 * description : Migration for creating tbl_util_so table
 * This file represents the Migration for tbl_util_so table.
 */
class CreateTblUtilSo extends Migration
{
    public function up()
    {
        // Drop table if exists
        $this->forge->dropTable('tbl_util_so', true);

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_gudang' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => 0,
            ],
            'id_outlet' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => 0,
            ],
            'id_user' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
            ],
            'tgl_masuk' => [
                'type' => 'DATE',
                'null' => true,
                'default' => null,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null,
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
                'default' => null,
            ],
            'reset' => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1'],
                'null'       => true,
                'default'    => '0',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1', '2', '3'],
                'null'       => true,
                'default'    => null,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_util_so', true, [
            'comment' => 'Tabel untuk stock opname'
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_util_so', true);
    }
} 