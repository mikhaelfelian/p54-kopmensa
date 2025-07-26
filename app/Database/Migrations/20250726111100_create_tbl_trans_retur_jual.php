<?php

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-07-26
 * Github : github.com/mikhaelfelian
 * description : Migration for sales return transactions table
 * This file represents the Migration.
 */

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblTransReturJual extends Migration
{
    public function up()
    {
        // Create tbl_trans_retur_jual table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_penjualan' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
                'comment' => 'Referensi ke tbl_trans_jual.id'
            ],
            'id_user' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => null,
            ],
            'id_pelanggan' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => null,
            ],
            'id_sales' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => null,
            ],
            'id_gudang' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => null,
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
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null,
            ],
            'no_nota' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'no_retur' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'tgl_masuk' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
                'default' => null,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['0', '1', '2'],
                'null' => true,
                'default' => '0',
                'comment' => '0=Draft, 1=Diproses, 2=Selesai'
            ],
            'status_terima' => [
                'type' => 'ENUM',
                'constraint' => ['0', '1', '2'],
                'null' => true,
                'default' => '0',
                'comment' => '0=Belum Diterima, 1=Sebagian, 2=Sudah Diterima'
            ],
        ]);

        // Add primary key
        $this->forge->addKey('id', true);
        
        // Add unique index for no_retur
        $this->forge->addUniqueKey('no_retur', 'uniq_no_retur');
        
        // Add index for foreign key
        $this->forge->addKey('id_penjualan', false, false, 'fk_retur_penjualan');

        // Create table
        $this->forge->createTable('tbl_trans_retur_jual', false, [
            'ENGINE' => 'InnoDB',
            'COMMENT' => 'Table untuk menyimpan data retur penjualan'
        ]);

        // Add foreign key constraint
        $this->db->query('ALTER TABLE `tbl_trans_retur_jual` ADD CONSTRAINT `fk_retur_penjualan` FOREIGN KEY (`id_penjualan`) REFERENCES `tbl_trans_jual` (`id`) ON DELETE CASCADE ON UPDATE CASCADE');
    }

    public function down()
    {
        // Drop foreign key constraint first
        $this->db->query('ALTER TABLE `tbl_trans_retur_jual` DROP FOREIGN KEY `fk_retur_penjualan`');
        
        // Drop table
        $this->forge->dropTable('tbl_trans_retur_jual');
    }
} 