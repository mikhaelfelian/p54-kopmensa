<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-28
 * Github : github.com/mikhaelfelian
 * description : Migration for creating tbl_trans_mutasi table
 * This file represents the Migration for tbl_trans_mutasi table.
 */
class CreateTblTransMutasi extends Migration
{
    public function up()
    {
        // Drop table if exists
        $this->forge->dropTable('tbl_trans_mutasi', true);

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_user' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'id_user_terima' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
            ],
            'no_nota' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => null,
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
            'kode_nota_dpn' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => null,
            ],
            'kode_nota_blk' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => null,
            ],
            'tgl_masuk' => [
                'type'    => 'DATE',
                'null'    => true,
                'default' => '0000-00-00',
            ],
            'tgl_keluar' => [
                'type'    => 'DATE',
                'null'    => true,
                'default' => '0000-00-00',
            ],
            'id_gd_asal' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
            ],
            'id_gd_tujuan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
            ],
            'id_outlet' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
                'default' => null,
            ],
            'status_nota' => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1', '2', '3', '4'],
                'null'       => true,
                'default'    => '0',
            ],
            'status_terima' => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1', '2'],
                'null'       => true,
                'default'    => '0',
                'comment'    => '1 = Terima, 2 = Tolak',
            ],
            'tipe' => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1', '2', '3'],
                'null'       => true,
                'default'    => '0',
                'comment'    => '1 = Pindah Gudang, 2 = Stok Masuk, 3 = Stok Keluar',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_trans_mutasi', true, [
            'comment' => 'Tabel untuk transfer mutasi masuk / keluar'
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_trans_mutasi', true);
    }
} 