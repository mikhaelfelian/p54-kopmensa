<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-20
 * 
 * Migration for tbl_trans_beli_po
 */

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblTransBeliPo extends Migration
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
            'id_penerima' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => 0
            ],
            'id_supplier' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'default'    => null
            ],
            'id_user' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'default'    => null
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true
            ],
            'deleted_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'comment' => 'Kalau terisi sudah terhapus'                
            ],
            'tgl_masuk' => [
                'type'    => 'DATE',
                'null'    => true
            ],
            'tgl_keluar' => [
                'type'    => 'DATE',
                'null'    => true
            ],
            'no_nota' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'comment'    => 'No Faktur pembelian'
            ],
            'supplier' => [
                'type'       => 'VARCHAR',
                'constraint' => 160,
                'null'       => true,
                'comment'    => 'Nama Supplier'
            ],
            'keterangan' => [
                'type'       => 'TEXT',
                'null'       => true
            ],
            'pengiriman' => [
                'type'       => 'TEXT',
                'null'       => true
            ],
            'status' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => 0
            ],
            'status_hps' => [
                'type'       => 'ENUM',
                'constraint' => ['0','1'],
                'null'       => true,
                'default'    => '0',
                'comment'    => '0 = Belum dihapus\r\n1 = Dihapus'
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_supplier', 'tbl_m_supplier', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('tbl_trans_beli_po');
        
        $this->db->query("ALTER TABLE `tbl_trans_beli_po` COMMENT 'Table untuk menyimpan PO, berelasi ke tbl_trans_beli_po_det'");
    }

    public function down()
    {
        $this->forge->dropTable('tbl_trans_beli_po');
    }
} 