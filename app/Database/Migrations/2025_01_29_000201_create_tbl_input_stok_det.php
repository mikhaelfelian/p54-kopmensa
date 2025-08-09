<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblInputStokDet extends Migration
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
            'id_input_stok' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'id_item' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'id_satuan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'jml' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => '0.00',
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('id_input_stok');
        $this->forge->addKey('id_item');
        $this->forge->addKey('id_satuan');
        
        $this->forge->addForeignKey('id_input_stok', 'tbl_input_stok', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_item', 'tbl_m_item', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_satuan', 'tbl_m_satuan', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('tbl_input_stok_det');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_input_stok_det');
    }
}
