<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-17
 * Github : github.com/mikhaelfelian
 * description : Migration for creating outlet master table
 * This file represents the Migration for tbl_m_outlet table.
 */
class CreateTblMOutlet extends Migration
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
            'id_user' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
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
            ],
            'kode' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'null'       => true,
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 128,
                'null'       => true,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1'],
                'default'    => '1',
            ],
            'status_hps' => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1'],
                'default'    => '0',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_m_outlet');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_m_outlet');
    }
} 