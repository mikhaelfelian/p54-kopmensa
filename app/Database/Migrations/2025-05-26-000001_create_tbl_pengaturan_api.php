<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-05-26
 * This file represents the migration for creating tbl_pengaturan_api table.
 */
class Migration_2025_05_26_001_create_tbl_pengaturan_api extends Migration
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
            'id_pengaturan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'pub_key' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'priv_key' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1'],
                'default'    => '1',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('id_pengaturan');
        $this->forge->addForeignKey('id_pengaturan', 'tbl_pengaturan', 'id', 'CASCADE', 'CASCADE', 'FK_pengaturan_api');
        $this->forge->createTable('tbl_pengaturan_api', true);

        // Add table comment
        $this->db->query("ALTER TABLE `tbl_pengaturan_api` COMMENT 'Table untuk menyimpan pengaturan API'");
    }

    public function down()
    {
        $this->forge->dropTable('tbl_pengaturan_api', true);
    }
}