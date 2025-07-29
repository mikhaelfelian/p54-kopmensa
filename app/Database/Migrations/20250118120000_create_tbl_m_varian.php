<?php

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-18
 * Github : github.com/mikhaelfelian
 * Description : Migration for creating tbl_m_varian table to store product variants
 * This file represents the Migration.
 */

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblMVarian extends Migration
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
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'kode' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
                'comment'    => 'Kode unik varian',
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
                'comment'    => 'Nama varian, contoh: Warna Merah, Ukuran XL',
            ],
            'keterangan' => [
                'type'    => 'TEXT',
                'null'    => true,
                'default' => null,
                'comment' => 'Penjelasan detail varian jika perlu',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['1', '0'],
                'default'    => '1',
                'comment'    => '1=Aktif, 0=Nonaktif',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('kode');
        $this->forge->createTable('tbl_m_varian', true, [
            'comment' => 'Table untuk menyimpan data varian produk seperti warna, ukuran, atau atribut lainnya'
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_m_varian');
    }
} 