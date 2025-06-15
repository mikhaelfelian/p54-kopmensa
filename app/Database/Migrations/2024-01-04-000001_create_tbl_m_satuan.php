<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMSatuanTable extends Migration
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
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
            'satuanKecil' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'satuanBesar' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'jml' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['1', '0'],
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_m_satuan', true);

        // Set the collation for specific columns
        $sql = "ALTER TABLE `tbl_m_satuan` 
                MODIFY COLUMN `satuanKecil` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
                MODIFY COLUMN `satuanBesar` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_general_ci NULL,
                MODIFY COLUMN `status` ENUM('1','0') CHARACTER SET latin1 COLLATE latin1_general_ci NULL";
        $this->db->query($sql);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_m_satuan', true);
    }
} 