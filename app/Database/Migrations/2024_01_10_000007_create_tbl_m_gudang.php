<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblMGudang extends Migration
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
            'id_user' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => 0,
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
            'kode' => [
                'type'       => 'VARCHAR',
                'constraint' => 160,
                'null'       => true,
                'default'    => null,
                'collate'    => 'utf8mb4_general_ci',
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 160,
                'null'       => true,
                'default'    => null,
                'collate'    => 'utf8mb4_general_ci',
            ],
            'deskripsi' => [
                'type'       => 'TEXT',
                'null'       => true,
                'default'    => null,
                'collate'    => 'utf8mb4_general_ci',
            ],
            'status' => [
                'type'       => "ENUM('0','1')",
                'null'       => true,
                'default'    => null,
                'comment'    => "1 = aktif\r\n0 = Non Aktif",
                'collate'    => 'utf8mb4_general_ci',
            ],
            'status_gd' => [
                'type'       => "ENUM('0','1')",
                'null'       => true,
                'default'    => '0',
                'comment'    => "1 = Gudang Utama\r\n0 = Bukan Gudang Utama",
                'collate'    => 'utf8mb4_general_ci',
            ],
            'status_otl' => [
                'type'       => "ENUM('0','1')",
                'null'       => true,
                'default'    => '0',
                'comment'    => "0 = Bukan Outlet\r\n1 = Outlet",
                'collate'    => 'utf8mb4_general_ci',
            ],
            'status_hps' => [
                'type'       => "ENUM('0','1')",
                'null'       => true,
                'default'    => '0',
                'comment'    => "1 = Dihapus\r\n0 = Tidak Dihapus",
                'collate'    => 'utf8mb4_general_ci',
            ],
        ]);

        $this->forge->addKey('id', true);

        $this->forge->createTable('tbl_m_gudang', true, [
            'ENGINE' => 'InnoDB',
            'COLLATE' => 'utf8mb4_general_ci',
        ]);

        // Set table comment
        $db = \Config\Database::connect();
        $db->query("ALTER TABLE `tbl_m_gudang` COMMENT='Table untuk menyimpan nama gudang ./ outlet.\r\nOutlet digabung dengan gudang'");
    }

    public function down()
    {
        $this->forge->dropTable('tbl_m_gudang');
    }
} 