<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePengaturanThemeTable extends Migration
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
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'null'          => true,
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'path' => [
                'type'       => 'VARCHAR',
                'constraint' => 160,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => 0,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('id_pengaturan');

        // Add foreign key
        $this->forge->addForeignKey('id_pengaturan', 'tbl_pengaturan', 'id', 'CASCADE', 'CASCADE', 'FK_pengaturan_theme');

        $this->forge->createTable('tbl_pengaturan_theme', true);

        // Add initial data
        $seeder = \Config\Database::seeder();
        $seeder->call('PengaturanThemeSeeder');
    }

    public function down()
    {
        $this->forge->dropForeignKey('tbl_pengaturan_theme', 'FK_pengaturan_theme');
        $this->forge->dropTable('tbl_pengaturan_theme', true);
    }
} 