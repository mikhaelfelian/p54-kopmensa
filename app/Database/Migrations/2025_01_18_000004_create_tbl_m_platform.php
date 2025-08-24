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
        $fields = [
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
                'null'    => true,
                'default' => null
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null
            ],
            'kode' => [
                'type'       => 'VARCHAR',
                'constraint' => 160,
                'null'       => true,
                'default'    => null,
                'collate'    => 'utf8mb4_general_ci'
            ],
            'platform' => [
                'type'       => 'VARCHAR',
                'constraint' => 160,
                'null'       => true,
                'default'    => null,
                'collate'    => 'utf8mb4_general_ci'
            ],
            'keterangan' => [
                'type'       => 'TEXT',
                'null'       => true,
                'default'    => null,
                'collate'    => 'utf8mb4_general_ci'
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
                'default'    => '1',
                'collate'    => 'utf8mb4_general_ci'
            ],
            'status_sys' => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1'],
                'null'       => true,
                'default'    => '0',
                'collate'    => 'utf8mb4_general_ci'
            ]
        ];

        $this->forge->addField($fields);
        $this->forge->addKey('id', true, true, 'BTREE');
        $this->forge->createTable('tbl_m_platform', false, [
            'ENGINE' => 'InnoDB',
            'COLLATE' => 'utf8mb4_general_ci',
            'COMMENT' => 'Table untuk platform pembayaran'
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_m_platform');
    }
}