<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-18
 * Github: github.com/mikhaelfelian
 * Description: Migration for creating tbl_trans_jual_plat table
 * This file represents the Migration.
 * Table untuk menyimpan data platform pembayaran transaksi penjualan
 */

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblTransJualPlat extends Migration
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
            'id_penjualan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'default'    => 0
            ],
            'id_platform' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false
            ],
            'modified_at' => [
                'type' => 'DATETIME',
                'null' => false
            ],
            'no_nota' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false
            ],
            'platform' => [
                'type'       => 'VARCHAR',
                'constraint' => 160,
                'null'       => false
            ],
            'keterangan' => [
                'type'       => 'VARCHAR',
                'constraint' => 160,
                'null'       => true,
                'default'    => null
            ],
            'nominal' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => false
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_penjualan', 'tbl_trans_jual', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_platform', 'tbl_m_platform', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('tbl_trans_jual_plat', true, [
            'comment' => 'Table untuk menyimpan data platform pembayaran transaksi penjualan'
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_trans_jual_plat');
    }
} 