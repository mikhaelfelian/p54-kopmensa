<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-02-04
 * 
 * Migration for tbl_trans_beli_det
 * Table untuk menyimpan transaksi pembelian detail, berelasi pada tbl_trans_beli
 */

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblTransBeliDet extends Migration
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
            'id_user' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => 0
            ],
            'id_pembelian' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false
            ],
            'id_item' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true
            ],
            'id_satuan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'tgl_masuk' => [
                'type' => 'DATE',
                'null' => true
            ],
            'tgl_terima' => [
                'type' => 'DATE',
                'null' => true
            ],
            'tgl_ed' => [
                'type' => 'DATE',
                'null' => true
            ],
            'kode' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'collate'    => 'latin1_swedish_ci'
            ],
            'kode_batch' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'collate'    => 'latin1_swedish_ci'
            ],
            'item' => [
                'type'       => 'VARCHAR',
                'constraint' => 160,
                'null'       => true,
                'collate'    => 'latin1_swedish_ci'
            ],
            'jml' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true
            ],
            'jml_satuan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true
            ],
            'jml_diterima' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => 0
            ],
            'jml_retur' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => 0
            ],
            'satuan' => [
                'type'       => 'VARCHAR',
                'constraint' => 160,
                'null'       => true,
                'collate'    => 'latin1_swedish_ci'
            ],
            'harga' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true
            ],
            'disk1' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true
            ],
            'disk2' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true
            ],
            'disk3' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true
            ],
            'diskon' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true
            ],
            'potongan' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true
            ],
            'subtotal' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true
            ],
            'satuan_retur' => [
                'type'       => 'VARCHAR',
                'constraint' => 160,
                'null'       => true,
                'collate'    => 'latin1_swedish_ci'
            ],
            'keterangan' => [
                'type'       => 'VARCHAR',
                'constraint' => 160,
                'null'       => true,
                'collate'    => 'latin1_swedish_ci'
            ],
            'status_item' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true
            ],
            'id_gudang' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'comment'    => 'ID gudang untuk penerimaan barang'
            ],
            'status_terima' => [
                'type'       => 'ENUM',
                'constraint' => ['1','2','3'],
                'null'       => true,
                'comment'    => '1=Diterima, 2=Ditolak, 3=Sebagian'
            ],
            'keterangan_terima' => [
                'type'       => 'TEXT',
                'null'       => true,
                'comment'    => 'Keterangan saat penerimaan'
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_pembelian', 'tbl_trans_beli', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('tbl_trans_beli_det', true, [
            'comment' => 'Table untuk menyimpan transaksi pembelian detail, berelasi pada tbl_trans_beli'
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_trans_beli_det');
    }
} 