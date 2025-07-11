<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-07-11
 * Github : github.com/mikhaelfelian
 * description : Migration for creating purchase transaction table
 * This file represents the Migration for tbl_trans_beli table.
 */
class CreateTblTransBeli extends Migration
{
    /**
     * Membuat tabel transaksi pembelian
     * Fungsi tabel: Table untuk menyimpan data transaksi pembelian barang dari supplier
     */
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_penerima' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => 0,
            ],
            'id_supplier' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
            ],
            'id_user' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
            ],
            'id_po' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
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
            'tgl_bayar' => [
                'type'    => 'DATE',
                'null'    => true,
                'default' => null,
            ],
            'tgl_masuk' => [
                'type'    => 'DATE',
                'null'    => true,
                'default' => null,
            ],
            'tgl_keluar' => [
                'type'    => 'DATE',
                'null'    => true,
                'default' => null,
            ],
            'no_nota' => [
                'type'       => 'VARCHAR',
                'constraint' => 160,
                'null'       => true,
                'default'    => null,
            ],
            'no_po' => [
                'type'       => 'VARCHAR',
                'constraint' => 160,
                'null'       => true,
                'default'    => null,
            ],
            'supplier' => [
                'type'       => 'VARCHAR',
                'constraint' => 160,
                'null'       => true,
                'default'    => null,
            ],
            'jml_total' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => '0.00',
            ],
            'disk1' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => '0.00',
            ],
            'disk2' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => '0.00',
            ],
            'disk3' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => '0.00',
            ],
            'jml_potongan' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => '0.00',
            ],
            'jml_retur' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => '0.00',
            ],
            'jml_diskon' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => '0.00',
            ],
            'jml_biaya' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => '0.00',
            ],
            'jml_ongkir' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => '0.00',
            ],
            'jml_subtotal' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => '0.00',
            ],
            'jml_dpp' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => '0.00',
            ],
            'ppn' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => 0,
            ],
            'jml_ppn' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => '0.00',
            ],
            'jml_gtotal' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => '0.00',
            ],
            'jml_bayar' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => '0.00',
            ],
            'jml_kembali' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => '0.00',
            ],
            'jml_kurang' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => '0.00',
            ],
            'status_bayar' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
            ],
            'status_nota' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => 0,
            ],
            'status_ppn' => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1', '2'],
                'null'       => true,
                'default'    => null,
            ],
            'status_retur' => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1'],
                'null'       => true,
                'default'    => null,
            ],
            'status_penerimaan' => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1', '2', '3'],
                'null'       => true,
                'default'    => '0',
            ],
            'metode_bayar' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => null,
            ],
            'status_hps' => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1'],
                'null'       => true,
                'default'    => '0',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_trans_beli');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_trans_beli');
    }
} 