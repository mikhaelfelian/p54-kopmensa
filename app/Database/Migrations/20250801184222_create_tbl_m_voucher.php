<?php

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-08-01
 * Github : github.com/mikhaelfelian
 * Description : Migration for creating tbl_m_voucher table to store voucher master data
 * This file represents the Migration.
 */

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblMVoucher extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
                'comment'        => 'Primary key for voucher table',
            ],
            'id_user' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'User ID who created the voucher',
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => false,
                'comment' => 'Record creation timestamp',
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => false,
                'comment' => 'Record update timestamp',
            ],
            'kode' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
                'comment'    => 'Unique voucher code',
            ],
            'jml' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'comment'    => 'Total voucher amount/quantity',
            ],
            'jml_keluar' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'default'    => 0,
                'comment'    => 'Amount/quantity used',
            ],
            'jml_max' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'comment'    => 'Maximum usage limit',
            ],
            'tgl_masuk' => [
                'type'    => 'DATE',
                'null'    => false,
                'comment' => 'Voucher start date',
            ],
            'tgl_keluar' => [
                'type'    => 'DATE',
                'null'    => false,
                'comment' => 'Voucher expiry date',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1'],
                'default'    => '1',
                'comment'    => '1=Active, 0=Inactive',
            ],
            'keterangan' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => 'Voucher description or notes',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('kode');
        $this->forge->addKey('id_user');
        $this->forge->addKey('status');
        $this->forge->addKey('tgl_masuk');
        $this->forge->addKey('tgl_keluar');
        
        $this->forge->createTable('tbl_m_voucher', true, [
            'comment' => 'Table untuk menyimpan data master voucher/kupon diskon yang dapat digunakan dalam transaksi penjualan'
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_m_voucher');
    }
}