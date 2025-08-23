<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblMPelangganGrupMember extends Migration
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
                'comment'    => 'Relasi ke tbl_m_pelanggan.id',
            ],
            'id_grup' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => false,
                'comment'    => 'Relasi ke tbl_m_pelanggan_grup.id',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['id_pelanggan', 'id_group'], 'uq_member');
        $this->forge->addForeignKey('id_pelanggan', 'tbl_m_pelanggan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_group', 'tbl_m_pelanggan_grup', 'id', 'CASCADE', 'CASCADE');

        // Tambahkan komentar MySQL untuk tabel
        $this->forge->createTable('tbl_m_pelanggan_grup_member', true, [
            'comment' => 'Table untuk menyimpan keanggotaan pelanggan di berbagai grup'
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_m_pelanggan_grup_member', true);
    }
}
