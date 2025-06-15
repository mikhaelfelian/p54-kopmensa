<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblMKategori extends Migration
{
    public function up()
    {
        // Skip if table already exists
        if ($this->db->tableExists('tbl_m_kategori')) {
            return;
        }

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
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
                'constraint' => 100,
                'null'       => true,
            ],
            'kategori' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type'    => "ENUM('0','1')",
                'default' => '0',
            ]
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_m_kategori', true);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_m_kategori', true);
    }
} 