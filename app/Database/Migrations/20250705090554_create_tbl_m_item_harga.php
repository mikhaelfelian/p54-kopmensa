<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-07-05
 * Github : github.com/mikhaelfelian
 * description : Migration for creating item price table
 * This file represents the Migration for tbl_m_item_harga table.
 */
class CreateTblMItemHarga extends Migration
{
    public function up()
    {
        // Drop table if exists
        $this->forge->dropTable('tbl_m_item_harga', true);

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
                'comment'    => 'Relasi ke tbl_m_item.id',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'null'       => false,
                'comment'    => 'Nama level harga, contoh: ecer, grosir, distributor',
            ],
            'jml_min' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'default'    => 1,
                'comment'    => 'Jumlah minimal beli agar harga ini berlaku',
            ],
            'harga' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'null'       => false,
                'comment'    => 'Harga jual untuk level ini',
            ],
            'keterangan' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
                'comment'    => 'Keterangan tambahan (opsional)',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('id_item');
        $this->forge->addForeignKey('id_item', 'tbl_m_item', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tbl_m_item_harga', true, [
            'comment' => 'Table untuk menyimpan harga. Cth : harga utk anggota,dokter,pelanggan, dll'
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_m_item_harga');
    }
} 