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
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
            'kode' => [
                'type'       => 'VARCHAR',
                'constraint' => 160,
                'null'       => true,
            ],
            'gudang' => [
                'type'       => 'VARCHAR',
                'constraint' => 160,
                'null'       => true,
            ],
            'keterangan' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'status' => [
                'type'       => "ENUM('0','1')",
                'null'       => true,
                'comment'    => '1 = aktif\r\n0 = Non Aktif',
            ],
            'status_gd' => [
                'type'       => "ENUM('0','1')",
                'null'       => true,
                'default'    => '0',
                'comment'    => '1 = Gudang Utama\r\n0 = Bukan Gudang Utama',
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_m_gudang', true);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_m_gudang');
    }
} 