<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblSessions extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 128,
                'null' => false
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => false
            ],
            'timestamp' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'null' => false,
                'default' => 0
            ],
            'data' => [
                'type' => 'BLOB',
                'null' => false
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('timestamp');
        $this->forge->createTable('tbl_sessions', true);

        // Add foreign key constraint
        $this->forge->addForeignKey('id_medrecs', 'tbl_trans_medrecs', 'id', 'CASCADE', 'CASCADE', 'fk_trans_medrecs_icd_medrecs');

        // Add table comment
        $this->db->query("ALTER TABLE `tbl_sessions` COMMENT 'Table untuk menyimpan session data'");
    }

    public function down()
    {
        $this->forge->dropTable('tbl_sessions', true);
    }
} 