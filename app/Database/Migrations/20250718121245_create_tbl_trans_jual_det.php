<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-18
 * Github: github.com/mikhaelfelian
 * Description: Migration for creating tbl_trans_jual_det table
 * This file represents the Migration.
 * Table untuk menyimpan detail transaksi penjualan/sales transaction detail
 */

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblTransJualDet extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true
            ],
            'id_penjualan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'default'    => null
            ],
            'id_item' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null
            ],
            'id_satuan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null
            ],
            'id_kategori' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null
            ],
            'id_merk' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null
            ],
            'no_nota' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => null
            ],
            'kode' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => null
            ],
            'produk' => [
                'type'       => 'VARCHAR',
                'constraint' => 256,
                'null'       => true,
                'default'    => null
            ],
            'satuan' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => null
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
                'default' => null
            ],
            'harga' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => null
            ],
            'harga_beli' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => null
            ],
            'jml' => [
                'type'       => 'INT',
                'constraint' => 6,
                'null'       => true,
                'default'    => null
            ],
            'jml_satuan' => [
                'type'       => 'INT',
                'constraint' => 6,
                'null'       => true,
                'default'    => null
            ],
            'disk1' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => null
            ],
            'disk2' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => null
            ],
            'disk3' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => null
            ],
            'diskon' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => null
            ],
            'potongan' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => null
            ],
            'subtotal' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => null
            ],
            'status' => [
                'type'       => 'INT',
                'constraint' => 2,
                'null'       => true,
                'default'    => null
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_penjualan', 'tbl_trans_jual', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('tbl_trans_jual_det', true, [
            'comment' => 'Table untuk menyimpan detail transaksi penjualan/sales transaction detail'
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_trans_jual_det');
    }
} 