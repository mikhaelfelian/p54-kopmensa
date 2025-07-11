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
                'null'       => false,
            ],
            'created_at' => [
                'type'    => 'TIMESTAMP',
                'null'    => false,
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
            ],
            'kode' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
                'collate'    => 'utf8mb4_general_ci',
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
                'collate'    => 'utf8mb4_general_ci',
            ],
            'harga_beli' => [
                'type'       => 'FLOAT',
                'null'       => true,
                'default'    => 0,
            ],
            'harga_jual' => [
                'type'       => 'FLOAT',
                'null'       => true,
                'default'    => 0,
            ],
            'barcode' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'collate'    => 'utf8mb4_general_ci',
            ],
            'foto' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
                'collate'    => 'utf8mb4_general_ci',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1'],
                'null'       => true,
                'default'    => '1',
                'collate'    => 'utf8mb4_general_ci',
            ],
        ]);

        // Use unique key names to avoid "Duplicate key name" error
        $this->forge->addKey('id', true, true, 'PRIMARY'); // Primary key
        $this->forge->addKey('id_item', false, false, 'idx_id_item');
        $this->forge->addKey('id_item_harga', false, false, 'idx_id_item_harga');
        $this->forge->addForeignKey('id_item', 'tbl_m_item', 'id', 'CASCADE', 'CASCADE', 'tbl_m_item_varian_id_item_foreign');
        $this->forge->addForeignKey('id_item_harga', 'tbl_m_item_harga', 'id', 'CASCADE', 'CASCADE', 'tbl_m_item_varian_id_item_harga_foreign');
        $this->forge->createTable('tbl_m_item_varian', false, [
            'ENGINE' => 'InnoDB',
            'COLLATE' => 'utf8mb4_general_ci',
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_m_item_varian');
    }
} 