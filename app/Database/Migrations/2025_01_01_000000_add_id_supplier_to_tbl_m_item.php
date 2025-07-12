<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-01
 * Github : github.com/mikhaelfelian
 * description : Migration for adding id_supplier field to tbl_m_item table
 * This file represents the Migration for adding supplier field to item table.
 */
class Migration_2025_01_01_000000_add_id_supplier_to_tbl_m_item extends Migration
{
    public function up()
    {
        // Check if the column already exists
        if (!$this->db->fieldExists('id_supplier', 'tbl_m_item')) {
            $this->forge->addColumn('tbl_m_item', [
                'id_supplier' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'id_merk',
                    'comment'    => 'Relasi ke tbl_m_supplier.id'
                ]
            ]);

            // Add foreign key constraint if tbl_m_supplier exists
            if ($this->db->tableExists('tbl_m_supplier')) {
                $this->forge->addForeignKey('id_supplier', 'tbl_m_supplier', 'id', 'SET NULL', 'SET NULL', 'tbl_m_item_id_supplier_foreign');
            }
        }
    }

    public function down()
    {
        // Remove foreign key constraint first
        if ($this->db->tableExists('tbl_m_supplier') && $this->db->fieldExists('id_supplier', 'tbl_m_item')) {
            $this->forge->dropForeignKey('tbl_m_item', 'tbl_m_item_id_supplier_foreign');
        }
        
        // Remove the column if it exists
        if ($this->db->fieldExists('id_supplier', 'tbl_m_item')) {
            $this->forge->dropColumn('tbl_m_item', 'id_supplier');
        }
    }
} 