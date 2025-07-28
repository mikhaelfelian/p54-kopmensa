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
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_penjualan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'comment'    => 'Referensi ke tbl_trans_jual.id',
            ],
            'id_user' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
            ],
            'id_pelanggan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
            ],
            'id_sales' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
            ],
            'id_gudang' => [
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
            'no_nota' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
                'collation'  => 'utf8mb4_general_ci',
            ],
            'no_retur' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
                'collation'  => 'utf8mb4_general_ci',
            ],
            'tgl_masuk' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'keterangan' => [
                'type'      => 'TEXT',
                'null'      => true,
                'default'   => null,
                'collation' => 'utf8mb4_general_ci',
            ],
            'status' => [
                'type'      => "ENUM('0','1','2')",
                'null'      => true,
                'default'   => '0',
                'comment'   => '0=Draft, 1=Diproses, 2=Selesai',
                'collation' => 'utf8mb4_general_ci',
            ],
            'status_retur' => [
                'type'      => "ENUM('1','2')",
                'null'      => true,
                'default'   => '1',
                'comment'   => "1 = refund\r\n2 = retur barang",
                'collation' => 'utf8mb4_general_ci',
            ],
            'status_terima' => [
                'type'      => "ENUM('0','1','2')",
                'null'      => true,
                'default'   => '0',
                'collation' => 'utf8mb4_general_ci',
            ],
        ]);

        $this->forge->addKey('id', true); // PRIMARY KEY
        $this->forge->addUniqueKey('no_retur', 'uniq_no_retur'); // UNIQUE INDEX
        $this->forge->addKey('id_penjualan', false, false, 'fk_retur_penjualan'); // INDEX

        $this->forge->createTable('tbl_trans_retur_jual', false, [
            'ENGINE'  => 'InnoDB',
            'COMMENT' => 'Table untuk menyimpan data retur penjualan',
            'COLLATE' => 'utf8mb4_general_ci',
        ]);

        // Add foreign key constraint
        $this->db->query('ALTER TABLE `tbl_trans_retur_jual` ADD CONSTRAINT `fk_retur_penjualan` FOREIGN KEY (`id_penjualan`) REFERENCES `tbl_trans_jual` (`id`) ON UPDATE CASCADE ON DELETE CASCADE');
    }

    public function down()
    {
        // Drop foreign key constraint first
        $this->db->query('ALTER TABLE `tbl_trans_retur_jual` DROP FOREIGN KEY `fk_retur_penjualan`');
        $this->forge->dropTable('tbl_trans_retur_jual');
    }
}