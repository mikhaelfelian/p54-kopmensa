<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2024-07-15
 * Github : github.com/mikhaelfelian
 * description : Migration for creating item stock table.
 * This file represents the CreateTblMItemStok migration.
 */

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblMItemStok extends Migration
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
            'id_gudang' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'default'    => 1,
            ],
            'id_user' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'default'    => 1,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'jml' => [
                'type'    => 'FLOAT',
                'null'    => true,
                'default' => 0,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1', '2'],
                'null'       => true,
                'default'    => '0',
                'collation'  => 'utf8mb4_general_ci',
            ],
        ]);

        // Primary Key
        $this->forge->addKey('id', true);

        // Foreign Keys
        $this->forge->addForeignKey('id_item', 'tbl_m_item', 'id', 'CASCADE', 'CASCADE');
        
        // Indexes
        $this->forge->addKey(['id_item', 'id_gudang', 'id_outlet']);
        $this->forge->addKey('status');

        $this->forge->createTable('tbl_m_item_stok');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_m_item_stok');
    }
} 