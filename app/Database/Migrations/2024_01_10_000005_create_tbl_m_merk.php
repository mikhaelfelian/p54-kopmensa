<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblMMerk extends Migration
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
            'merk' => [
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
                'default'    => '0',
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_m_merk', true);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_m_merk');
    }
} 