<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-20
 * 
 * Migration for tbl_trans_beli_po_det
 */

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblTransBeliPoDet extends Migration
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
            'id_user' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'default'    => 0
            ],
            'id_pembelian' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false
            ],
            'id_item' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false
            ],
            'id_satuan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => false
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => false
            ],
            'tgl_masuk' => [
                'type'    => 'DATE',
                'null'    => false
            ],
            'kode' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true
            ],
            'item' => [
                'type'       => 'VARCHAR',
                'constraint' => 160,
                'null'       => true
            ],
            'jml' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true
            ],
            'jml_satuan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true
            ],
            'satuan' => [
                'type'       => 'VARCHAR',
                'constraint' => 160,
                'null'       => true
            ],
            'keterangan' => [
                'type'       => 'VARCHAR',
                'constraint' => 160,
                'null'       => true
            ],
            'keterangan_itm' => [
                'type'       => 'TEXT',
                'null'       => true
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['0','1'],
                'null'       => true
            ]
        ]);

        $this->forge->addKey('id', true);
        
        // Add Foreign Keys
        $this->forge->addForeignKey('id_pembelian', 'tbl_trans_beli_po', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_item', 'tbl_m_item', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('tbl_trans_beli_po_det');
        
        $this->db->query("ALTER TABLE `tbl_trans_beli_po_det` COMMENT 'Table untuk menyimpan PO Detail, berelasi ke tbl_trans_beli_po dan tbl_m_item'");
    }

    public function down()
    {
        $this->forge->dropTable('tbl_trans_beli_po_det');
    }
} 