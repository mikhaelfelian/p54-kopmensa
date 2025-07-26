<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2024-06-09
 * Github : github.com/mikhaelfelian
 * description : Migration for creating tbl_trans_retur_beli_det
 * This file represents the Migration.
 * Table untuk <note_for_using_table>
 */

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblTransReturBeliDet extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_retur' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'comment'    => 'Referensi ke tbl_trans_retur_beli.id',
            ],
            'id_beli_det' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'default'    => null,
                'comment'    => 'Referensi ke tbl_trans_beli_det.id (jika ada)',
            ],
            'id_user' => [
                'type'    => 'INT',
                'constraint' => 11,
                'null'    => true,
                'default' => '0',
            ],
            'id_item' => [
                'type'    => 'INT',
                'constraint' => 11,
                'null'    => true,
                'default' => null,
            ],
            'id_satuan' => [
                'type'    => 'INT',
                'constraint' => 11,
                'null'    => true,
                'default' => null,
            ],
            'id_gudang' => [
                'type'    => 'INT',
                'constraint' => 11,
                'null'    => true,
                'default' => null,
                'comment' => 'Gudang asal barang diretur',
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
            ],
            'deleted_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
            ],
            'tgl_masuk' => [
                'type'    => 'DATE',
                'null'    => true,
                'default' => null,
                'comment' => 'Tanggal barang masuk ke gudang',
            ],
            'tgl_keluar' => [
                'type'    => 'DATE',
                'null'    => true,
                'default' => null,
                'comment' => 'Tanggal barang keluar karena retur',
            ],
            'kode' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => null,
                'collate'    => 'utf8mb4_general_ci',
            ],
            'kode_batch' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => null,
                'collate'    => 'utf8mb4_general_ci',
            ],
            'item' => [
                'type'       => 'VARCHAR',
                'constraint' => 160,
                'null'       => true,
                'default'    => null,
                'collate'    => 'utf8mb4_general_ci',
            ],
            'keterangan' => [
                'type'       => 'TEXT',
                'null'       => true,
                'default'    => null,
                'collate'    => 'utf8mb4_general_ci',
            ],
            'satuan' => [
                'type'       => 'VARCHAR',
                'constraint' => 160,
                'null'       => true,
                'default'    => null,
                'collate'    => 'utf8mb4_general_ci',
            ],
            'jml' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => null,
                'comment'    => 'Jumlah item yang diretur',
            ],
            'harga' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => null,
            ],
            'disk1' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => null,
            ],
            'disk2' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => null,
            ],
            'disk3' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => null,
            ],
            'diskon' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => null,
            ],
            'potongan' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => null,
            ],
            'subtotal' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => null,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('id_retur');
        $this->forge->addKey('id_beli_det');
        $this->forge->addForeignKey('id_retur', 'tbl_trans_retur_beli', 'id', 'CASCADE', 'CASCADE', 'fk_retur_beli_det_retur');
        $this->forge->addForeignKey('id_beli_det', 'tbl_trans_beli_det', 'id', 'SET NULL', 'CASCADE', 'fk_retur_beli_det_beli_det');
        $this->forge->createTable('tbl_trans_retur_beli_det', true, [
            'comment' => 'Table untuk menyimpan detail retur pembelian barang'
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_trans_retur_beli_det', true);
    }
} 