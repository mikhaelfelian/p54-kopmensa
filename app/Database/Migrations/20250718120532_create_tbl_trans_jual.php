<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-18
 * Github: github.com/mikhaelfelian
 * Description: Migration for creating tbl_trans_jual table
 * This file represents the Migration.
 * Table untuk menyimpan data transaksi penjualan/sales transaction
 */

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblTransJual extends Migration
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
                'null'       => true
            ],
            'id_sales' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true
            ],
            'id_pelanggan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null
            ],
            'id_gudang' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null
            ],
            'id_shift' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null
            ],
            'no_nota' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false
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
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null
            ],
            'tgl_bayar' => [
                'type'    => 'DATE',
                'null'    => true,
                'default' => '0000-00-00'
            ],
            'tgl_masuk' => [
                'type'    => 'DATE',
                'null'    => true,
                'default' => '0000-00-00'
            ],
            'tgl_keluar' => [
                'type'    => 'DATE',
                'null'    => true,
                'default' => '0000-00-00'
            ],
            'jml_total' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => '0.00'
            ],
            'jml_biaya' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => '0.00'
            ],
            'jml_ongkir' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => '0.00'
            ],
            'jml_retur' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => '0.00'
            ],
            'diskon' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => '0.00'
            ],
            'jml_diskon' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => '0.00'
            ],
            'jml_subtotal' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => '0.00'
            ],
            'ppn' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => '0'
            ],
            'jml_ppn' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => '0.00'
            ],
            'jml_gtotal' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => '0.00'
            ],
            'jml_bayar' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => '0.00'
            ],
            'jml_kembali' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => '0.00'
            ],
            'jml_kurang' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => '0.00'
            ],
            'disk1' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => '0.00'
            ],
            'jml_disk1' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => '0.00'
            ],
            'disk2' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => '0.00'
            ],
            'jml_disk2' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => '0.00'
            ],
            'disk3' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => '0.00'
            ],
            'jml_disk3' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => '0.00'
            ],
            'metode_bayar' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1', '2', '3', '4'],
                'default'    => '0',
                'null'       => true,
                'comment'    => '1=pos'
            ],
            'status_nota' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
                'comment'    => '1=anamnesa, 2=pemeriksaan, 3=tindakan, 4=obat, 5=dokter, 6=pembayaran, 7=finish'
            ],
            'status_ppn' => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1'],
                'default'    => '0',
                'null'       => true
            ],
            'status_bayar' => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1', '2'],
                'default'    => '0',
                'null'       => true
            ],
            'status_retur' => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1', '2'],
                'default'    => '0',
                'null'       => true
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('no_nota');
        
        $this->forge->createTable('tbl_trans_jual', true);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_trans_jual');
    }
} 