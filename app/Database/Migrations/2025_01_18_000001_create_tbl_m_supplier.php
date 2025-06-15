<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-18
 * 
 * Migration for tbl_m_supplier
 */

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblMSupplier extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 4,
                'unsigned'       => true,
                'auto_increment' => true
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
                'null'    => true
            ],
            'kode' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true
            ],
            'npwp' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true
            ],
            'alamat' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'rt' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true
            ],
            'rw' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true
            ],
            'kecamatan' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true
            ],
            'kelurahan' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true
            ],
            'kota' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true
            ],
            'no_tlp' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true
            ],
            'no_hp' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true
            ],
            'tipe' => [
                'type'       => "ENUM('0','1','2')",
                'null'       => true,
                'default'    => '0',
                'comment'    => '1= Instansi; 2=Personal'
            ],
            'status' => [
                'type'       => "ENUM('0','1')",
                'null'       => true,
                'default'    => '0'
            ],
            'status_hps' => [
                'type'       => "ENUM('0','1')",
                'null'       => true,
                'default'    => '0'
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_m_supplier');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_m_supplier');
    }
} 