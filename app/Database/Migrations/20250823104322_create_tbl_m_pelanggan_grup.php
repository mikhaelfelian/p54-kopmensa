<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblMPelangganGrup extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_pelanggan' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => false,
                'comment'    => 'Foreign key ke tbl_m_pelanggan.id',
            ],
            'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL',
            'updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL',
            'grup' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'comment'    => 'Nama grup pelanggan, misal: Umum, Anggota, Reseller',
            ],
            'deskripsi' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Keterangan tambahan grup pelanggan',
            ],
            'status' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'comment'    => '1=aktif, 0=nonaktif',
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('id_pelanggan');
        $this->forge->addForeignKey('id_pelanggan', 'tbl_m_pelanggan', 'id', 'CASCADE', 'CASCADE');
        // Table untuk menyimpan grup pelanggan (misal: Umum, Anggota, Reseller)
        $this->forge->createTable('tbl_m_pelanggan_grup', true, ['comment' => 'Table untuk menyimpan grup pelanggan']);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_m_pelanggan_grup', true);
    }
}
