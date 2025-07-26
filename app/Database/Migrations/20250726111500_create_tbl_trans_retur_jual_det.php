<?php

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-07-26
 * Github : github.com/mikhaelfelian
 * description : Migration for sales return detail transactions table
 * This file represents the Migration.
 */

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblTransReturJualDet extends Migration
{
    public function up()
    {
        // Create tbl_trans_retur_jual_det table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_retur_jual' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'comment'    => 'Referensi ke tbl_trans_retur_jual.id',
            ],
            'id_item' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
            ],
            'id_satuan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
            ],
            // New column: id_gudang after id_satuan
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
            'kode' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
            ],
            'item' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
            ],
            'satuan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
            ],
            'harga' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => '0.00',
            ],
            'jml' => [
                'type'       => 'INT',
                'constraint' => 6,
                'null'       => true,
                'default'    => '0',
            ],
            'subtotal' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => '0.00',
            ],
            'keterangan' => [
                'type'    => 'TEXT',
                'null'    => true,
                'default' => null,
            ],
            'status_item' => [
                'type'       => 'ENUM',
                'constraint' => ['1', '2'],
                'null'       => true,
                'default'    => '1',
                'comment'    => '1=Valid, 2=Ditolak',
            ],
            // New column: status_terima after status_item
            'status_terima' => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1', '2'],
                'null'       => true,
                'default'    => '0',
                'comment'    => '0=Belum, 1=Diterima, 2=Ditolak',
                'collate'    => 'utf8mb4_general_ci',
            ],
        ]);

        // Add primary key
        $this->forge->addKey('id', true);

        // Add index for foreign key
        $this->forge->addKey('id_retur_jual', false, false, 'fk_retur_jual_det_retur');

        // Create table
        $this->forge->createTable('tbl_trans_retur_jual_det', false, [
            'ENGINE'  => 'InnoDB',
            'COMMENT' => 'Table untuk menyimpan detail item retur penjualan',
        ]);

        // Add foreign key constraint
        $this->db->query(
            'ALTER TABLE `tbl_trans_retur_jual_det` ADD CONSTRAINT `fk_retur_jual_det_retur` FOREIGN KEY (`id_retur_jual`) REFERENCES `tbl_trans_retur_jual` (`id`) ON DELETE CASCADE ON UPDATE CASCADE'
        );
    }

    public function down()
    {
        // Drop foreign key constraint first
        $this->db->query('ALTER TABLE `tbl_trans_retur_jual_det` DROP FOREIGN KEY `fk_retur_jual_det_retur`');

        // Drop table
        $this->forge->dropTable('tbl_trans_retur_jual_det');
    }
} 