<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-07-11
 * Github : github.com/mikhaelfelian
 * description : Migration for creating item variant master data table
 * This file represents the Migration for tbl_m_item_varian table.
 */
class CreateTblMItemVarian extends Migration
{
    /**
     * Membuat tabel master data varian item
     * Fungsi tabel: Table untuk menyimpan data varian item seperti warna, ukuran, dll
     */
    public function up()
    {
        // Drop the table if it exists before creating
        $this->forge->dropTable('tbl_m_item_varian', true);

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_item' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'id_item_harga' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'default'    => null,
            ],
            'created_at' => [
                'type'    => 'TIMESTAMP',
                'null'    => false,
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type'    => 'TIMESTAMP',
                'null'    => true,
                'default' => null,
                'on_update' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
            'deleted_at' => [
                'type'    => 'TIMESTAMP',
                'null'    => true,
                'default' => null,
            ],
            'kode' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
                'collate'    => 'utf8mb4_general_ci',
            ],
            'barcode' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'collate'    => 'utf8mb4_general_ci',
            ],
            'varian' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
                'collate'    => 'utf8mb4_general_ci',
            ],
            'harga_beli' => [
                'type'       => 'DECIMAL',
                'constraint' => '18,2',
                'null'       => true,
                'default'    => null,
            ],
            'harga_dasar' => [
                'type'       => 'DECIMAL',
                'constraint' => '18,2',
                'null'       => true,
                'default'    => null,
            ],
            'harga_jual' => [
                'type'       => 'DECIMAL',
                'constraint' => '18,2',
                'null'       => true,
                'default'    => null,
            ],
            'foto' => [
                'type'       => 'VARCHAR',
                'constraint' => 160,
                'null'       => true,
                'default'    => null,
                'collate'    => 'utf8mb4_general_ci',
            ],
            'status' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 1,
            ],
        ]);

        // Primary key
        $this->forge->addKey('id', true, true, 'PRIMARY'); // PRIMARY KEY (`id`) USING BTREE

        // Unique indexes
        $this->forge->addUniqueKey('kode', 'uq_kode'); // UNIQUE INDEX `uq_kode` (`kode`)

        // Indexes
        $this->forge->addKey('id_item', false, false, 'idx_id_item'); // INDEX `idx_id_item` (`id_item`) USING BTREE

        // Foreign key
        $this->forge->addForeignKey(
            'id_item',
            'tbl_m_item',
            'id',
            'CASCADE',
            'CASCADE',
            'tbl_m_item_varian_id_item_foreign'
        );

        // Create table
        $this->forge->createTable('tbl_m_item_varian', false, [
            'ENGINE'   => 'InnoDB',
            'COLLATE'  => 'utf8mb4_general_ci',
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_m_item_varian');
    }
}