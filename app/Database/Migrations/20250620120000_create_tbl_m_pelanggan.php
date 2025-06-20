<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-20
 * Github : github.com/mikhaelfelian
 * description : Migration for creating customer master data table
 * This file represents the Migration for tbl_m_pelanggan table.
 */
class CreateTblMPelanggan extends Migration
{
    /**
     * Membuat tabel master data pelanggan
     * Fungsi tabel: Menyimpan data master pelanggan/konsumen
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
            'id_user' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'kode' => [
                'type'       => 'VARCHAR',
                'constraint' => 160,
                'null'       => true,
                'default'    => null,
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 360,
                'null'       => true,
                'default'    => null,
            ],
            'no_telp' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => null,
            ],
            'alamat' => [
                'type'    => 'TEXT',
                'null'    => true,
                'default' => null,
            ],
            'kota' => [
                'type'    => 'TEXT',
                'null'    => true,
                'default' => null,
            ],
            'provinsi' => [
                'type'    => 'TEXT',
                'null'    => true,
                'default' => null,
            ],
            'tipe' => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1', '2', '3'],
                'null'       => true,
                'default'    => '0',
                'comment'    => '0=none; 1=anggota koperasi; 2=umum; 3=swasta',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1'],
                'null'       => true,
                'default'    => '0',
            ],
            'status_hps' => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1'],
                'null'       => true,
                'default'    => '0',
                'comment'    => '0=none; 1=terhapus;',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_m_pelanggan');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_m_pelanggan');
    }
} 