<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-28
 * Github : github.com/mikhaelfelian
 * description : Migration for creating tbl_trans_mutasi_det table
 * This file represents the Migration for tbl_trans_mutasi_det table.
 */
class CreateTblTransMutasiDet extends Migration
{
    public function up()
    {
        // Drop table if exists
        $this->forge->dropTable('tbl_trans_mutasi_det', true);

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_mutasi' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'default'    => 0,
            ],
            'id_satuan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => 0,
            ],
            'id_item' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => 0,
            ],
            'id_user' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => 0,
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
            'no_nota' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => null,
            ],
            'kode' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => null,
            ],
            'item' => [
                'type'       => 'VARCHAR',
                'constraint' => 256,
                'null'       => true,
                'default'    => null,
            ],
            'satuan' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => null,
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
                'default' => null,
            ],
            'jml' => [
                'type'       => 'INT',
                'constraint' => 6,
                'null'       => true,
                'default'    => 0,
            ],
            'jml_diterima' => [
                'type'       => 'INT',
                'constraint' => 6,
                'null'       => true,
                'default'    => 0,
            ],
            'jml_satuan' => [
                'type'       => 'INT',
                'constraint' => 6,
                'null'       => true,
                'default'    => null,
            ],
            'status_brg' => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1'],
                'null'       => true,
                'default'    => '0',
            ],
            'status_terima' => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1'],
                'null'       => true,
                'default'    => '0',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('id_mutasi');
        $this->forge->addForeignKey('id_mutasi', 'tbl_trans_mutasi', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tbl_trans_mutasi_det', true, [
            'comment' => 'Tabel untuk transfer mutasi detail masuk / keluar'
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_trans_mutasi_det', true);
    }
} 