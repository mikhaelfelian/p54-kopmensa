<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-05-30
 * This file represents the Migration for tbl_ion_modules table.
 */
class Migration_20250530173000_create_tbl_ion_modules extends Migration
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
            'parent_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'comment'    => 'Untuk modul dengan sub-menu',
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
                'comment'    => 'Nama modul (ex: Barang, Kategori)',
            ],
            'route' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
                'comment'    => 'ex: Master/Item',
            ],
            'icon' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'is_menu' => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1'],
                'default'    => '1',
                'comment'    => 'tampil di sidebar',
            ],
            'is_active' => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1'],
                'default'    => '1',
            ],
            'sort_order' => [
                'type'       => 'INT',
                'default'    => 0,
            ],
            'default_permissions' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => 'JSON: {"create":true,"read_all":true}',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_ion_modules');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_ion_modules');
    }
} 