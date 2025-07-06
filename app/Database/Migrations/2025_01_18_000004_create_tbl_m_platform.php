<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-18
 * 
 * Migration for tbl_m_platform
 */

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblMPlatform extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true
            ],
            'id_outlet' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'default'    => 0
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true
            ],
            'kode' => [
                'type'       => 'VARCHAR',
                'constraint' => 160,
                'null'       => true,
                'default'    => null
            ],
            'platform' => [
                'type'       => 'VARCHAR',
                'constraint' => 160,
                'null'       => true,
                'default'    => null
            ],
            'keterangan' => [
                'type'       => 'TEXT',
                'null'       => true,
                'default'    => null
            ],
            'persen' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,1',
                'null'       => true,
                'default'    => null
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1'],
                'null'       => true,
                'default'    => '1'
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_m_platform');
        $this->db->query("ALTER TABLE `tbl_m_platform` COMMENT 'Platform Pembayaran'");
    }

    public function down()
    {
        $this->forge->dropTable('tbl_m_platform');
    }
} 