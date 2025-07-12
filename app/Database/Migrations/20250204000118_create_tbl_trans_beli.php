<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-02-04
 * 
 * Migration for tbl_trans_beli
 * Table untuk menyimpan transaksi pembelian, detail item disimpan pada tbl_trans_beli_det
 */

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblTransBeli extends Migration
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
            'id_penerima' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => 0
            ],
            'id_supplier' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true
            ],
            'id_user' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true
            ],
            'id_po' => [
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
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'tgl_bayar' => [
                'type' => 'DATE',
                'null' => true
            ],

            'tgl_masuk' => [
                'type' => 'DATE',
                'null' => true
            ],
            'tgl_keluar' => [
                'type' => 'DATE',
                'null' => true
            ],
            'no_nota' => [
                'type'       => 'VARCHAR',
                'constraint' => 160,
                'null'       => true
            ],
            'no_po' => [
                'type'       => 'VARCHAR',
                'constraint' => 160,
                'null'       => true,
                'collate'    => 'latin1_swedish_ci'
            ],
            'supplier' => [
                'type'       => 'VARCHAR',
                'constraint' => 160,
                'null'       => true,
                'collate'    => 'latin1_swedish_ci'
            ],
            'jml_total' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => 0.00
            ],
            'disk1' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => 0.00
            ],
            'disk2' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => 0.00
            ],
            'disk3' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => 0.00
            ],
            'jml_potongan' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => 0.00
            ],
            'jml_retur' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => 0.00
            ],
            'jml_diskon' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => 0.00
            ],
            'jml_biaya' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => 0.00
            ],
            'jml_ongkir' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => 0.00
            ],
            'jml_subtotal' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => 0.00
            ],
            'jml_dpp' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => 0.00
            ],
            'ppn' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => 0
            ],
            'jml_ppn' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => 0.00
            ],
            'jml_gtotal' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => 0.00
            ],
            'jml_bayar' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => 0.00
            ],
            'jml_kembali' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => 0.00
            ],
            'jml_kurang' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => 0.00
            ],
            'status_bayar' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true
            ],
            'status_nota' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => 0
            ],
            'status_ppn' => [
                'type'       => 'ENUM',
                'constraint' => ['0','1','2'],
                'null'       => true,
                'collate'    => 'latin1_swedish_ci'
            ],
            'status_retur' => [
                'type'       => 'ENUM',
                'constraint' => ['0','1'],
                'null'       => true,
                'collate'    => 'latin1_swedish_ci'
            ],
            'status_penerimaan' => [
                'type'       => 'ENUM',
                'constraint' => ['0','1','2','3'],
                'null'       => true,
                'default'    => '0',
                'collate'    => 'latin1_swedish_ci'
            ],
            'metode_bayar' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'collate'    => 'utf8_general_ci'
            ],
            'status_hps' => [
                'type'       => 'ENUM',
                'constraint' => ['0','1'],
                'null'       => true,
                'default'    => '0',
                'collate'    => 'latin1_swedish_ci'
            ],
            'status_terima' => [
                'type'       => 'ENUM',
                'constraint' => ['0','1'],
                'null'       => true,
                'default'    => '0',
                'comment'    => '0=Belum diterima, 1=Sudah diterima'
            ],
            'tgl_terima' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Tanggal penerimaan barang'
            ],
            'catatan_terima' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Catatan saat penerimaan barang'
            ],
            'id_user_terima' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'comment'    => 'ID user yang menerima barang'
            ]
        ]);

        $this->forge->addKey('id', true);
    $this->forge->createTable('tbl_trans_beli', true, [
        'comment' => 'Table untuk menyimpan transaksi pembelian, detail item disimpan pada tbl_trans_beli_det'
    ]);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_trans_beli');
    }
} 