<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-17
 * Github : github.com/mikhaelfelian
 * description : Migration for creating item master table
 * This file represents the Migration for tbl_m_item table.
 */
class CreateTblMItem extends Migration
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
            'id_user' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'id_kategori' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'id_satuan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'id_merk' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type'    => 'TIMESTAMP',
                'null'    => true,
                'default' => null,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'kode' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'null'       => true,
                'comment'    => 'Kode Item / SKU',
            ],
            'barcode' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'null'       => true,
                'comment'    => 'Barcode Produk',
            ],
            'item' => [
                'type'       => 'VARCHAR',
                'constraint' => 128,
                'null'       => true,
                'comment'    => 'Nama Produk / Item',
            ],
            'deskripsi' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => 'Deskripsi Produk',
            ],
            'jml_min' => [
                'type'       => 'FLOAT',
                'null'       => true,
                'default'    => 0,
                'comment'    => 'Minimum stok sebelum restock',
            ],
            'harga_beli' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'null'       => true,
                'default'    => 0.00,
            ],
            'harga_jual' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'null'       => true,
                'default'    => 0.00,
            ],
            'foto' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Gambar produk (opsional)',
            ],
            'tipe' => [
                'type'       => 'ENUM',
                'constraint' => ['1', '2', '3'],
                'null'       => true,
                'default'    => '1',
                'comment'    => 'Tipe produk 1=item; 2=jasa; 3=paket;',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1'],
                'null'       => true,
                'default'    => '1',
                'comment'    => 'Status item aktif / tidak',
            ],
            'status_hps' => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1'],
                'null'       => true,
                'default'    => '0',
                'comment'    => 'Status soft delete',
            ],
            'status_stok' => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1'],
                'null'       => true,
                'default'    => '1',
                'comment'    => 'Status stockable 1=stockable',
            ],
            'sp' => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1'],
                'null'       => true,
                'default'    => '0',
                'comment'    => 'Status processed',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_m_item', true, ['comment' => 'Tabel untuk menyimpan data master item/produk']);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_m_item');
    }
}