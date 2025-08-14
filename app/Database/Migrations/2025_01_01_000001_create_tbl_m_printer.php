<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Migration_2025_01_01_000001_create_tbl_m_printer extends Migration
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
            'nama_printer' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => false,
            ],
            'tipe_printer' => [
                'type'       => 'ENUM',
                'constraint' => ['network', 'usb', 'file', 'windows'],
                'default'    => 'network',
                'null'       => false,
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => '45',
                'null'       => true,
            ],
            'port' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'path' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'driver' => [
                'type'       => 'ENUM',
                'constraint' => ['pos58', 'epson', 'star', 'citizen', 'generic'],
                'default'    => 'pos58',
                'null'       => false,
            ],
            'width_paper' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'default'    => 58,
            ],
            'status' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 1,
            ],
            'is_default' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 0,
            ],
            'keterangan' => [
                'type'       => 'TEXT',
                'null'       => true,
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
        $this->forge->addKey('tipe_printer');
        $this->forge->addKey('status');
        $this->forge->addKey('is_default');
        
        $this->forge->createTable('tbl_m_printer');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_m_printer');
    }
} 