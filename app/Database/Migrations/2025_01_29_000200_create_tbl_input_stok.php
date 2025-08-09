<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblInputStok extends Migration
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
            'no_terima' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'tgl_terima' => [
                'type'    => 'DATETIME',
                'null'    => false,
            ],
            'id_supplier' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'id_gudang' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'id_penerima' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1'],
                'default'    => '1',
                'comment'    => '0=Inactive, 1=Active',
            ],
            'status_hps' => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1'],
                'default'    => '0',
                'comment'    => '0=Not Deleted, 1=Deleted',
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
        $this->forge->addKey('no_terima');
        $this->forge->addKey('tgl_terima');
        $this->forge->addKey('id_supplier');
        $this->forge->addKey('id_gudang');
        $this->forge->addKey('id_penerima');
        
        $this->forge->addForeignKey('id_supplier', 'tbl_m_supplier', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_gudang', 'tbl_m_gudang', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_penerima', 'tbl_m_karyawan', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('tbl_input_stok');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_input_stok');
    }
}
