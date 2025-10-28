<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblMItemSupplier extends Migration
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
            'id_item' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'id_supplier' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'harga_beli' => [
                'type'       => 'DECIMAL',
                'constraint' => '18,2',
                'null'       => true,
                'default'    => 0.00,
            ],
            'prioritas' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'default'    => 0,
                'comment'    => 'Priority order for supplier selection (0 = highest)'
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Soft delete timestamp'
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('id_item');
        $this->forge->addKey('id_supplier');
        $this->forge->addUniqueKey(['id_item', 'id_supplier'], 'unique_item_supplier');
        
        // Add foreign keys
        $this->forge->addForeignKey('id_item', 'tbl_m_item', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_supplier', 'tbl_m_supplier', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('tbl_m_item_supplier', true, [
            'ENGINE' => 'InnoDB',
            'COLLATE' => 'utf8mb4_general_ci',
            'COMMENT' => 'Mapping table for item-supplier relationships with purchase price'
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_m_item_supplier', true);
    }
}

