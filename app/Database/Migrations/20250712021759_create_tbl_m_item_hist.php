<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblMItemHist extends Migration
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
            'id_item' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'default'    => null
            ],
            'id_satuan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => 0
            ],
            'id_gudang' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'default'    => null
            ],
            // Add id_outlet after id_gudang
            'id_outlet' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'default'    => null
            ],
            'id_user' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => 0
            ],
            'id_pelanggan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => 0
            ],
            'id_supplier' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => 0
            ],
            'id_penjualan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => 0
            ],
            'id_pembelian' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => 0
            ],
            'id_pembelian_det' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => 0
            ],
            'id_so' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => 0
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null
            ],
            'tgl_masuk' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null
            ],
            'no_nota' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'collate'    => 'utf8mb4_general_ci'
            ],
            'kode' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'collate'    => 'utf8mb4_general_ci'
            ],
            'item' => [
                'type'       => 'TEXT',
                'null'       => true,
                'default'    => null,
                'collate'    => 'utf8mb4_general_ci'
            ],
            'keterangan' => [
                'type'       => 'LONGTEXT',
                'null'       => true,
                'default'    => null,
                'collate'    => 'utf8mb4_general_ci'
            ],
            'nominal' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => 0.00
            ],
            'jml' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => 0
            ],
            'jml_satuan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => 1
            ],
            'satuan' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => null,
                'collate'    => 'utf8mb4_general_ci'
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['1','2','3','4','5','6','7','8'],
                'null'       => true,
                'default'    => null,
                'comment'    => '1 = Stok Masuk Pembelian, 2 = Stok Masuk, 3 = Stok Masuk Retur Jual, 4 = Stok Keluar Penjualan, 5 = Stok Keluar Retur Beli, 6 = SO, 7 = Stok Keluar, 8 = Mutasi Antar Gd',
                'collate'    => 'utf8mb4_general_ci'
            ],
            'sp' => [
                'type'       => 'ENUM',
                'constraint' => ['0','1'],
                'null'       => true,
                'default'    => '0',
                'collate'    => 'utf8mb4_general_ci'
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('id_item');
        $this->forge->addKey('id_gudang');
        $this->forge->addKey('id_outlet');
        
        // Add foreign key constraints
        $this->forge->addForeignKey('id_gudang', 'tbl_m_gudang', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_item', 'tbl_m_item', 'id', 'CASCADE', 'CASCADE');
        // Optionally, add a foreign key for id_outlet if tbl_m_outlet exists:
        // $this->forge->addForeignKey('id_outlet', 'tbl_m_outlet', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('tbl_m_item_hist', true, [
            'comment' => 'Table untuk menyimpan item stok histories',
            'collate' => 'utf8mb4_general_ci',
            'engine'  => 'InnoDB'
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_m_item_hist');
    }
}
