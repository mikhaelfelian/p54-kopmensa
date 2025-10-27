<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-27
 * 
 * Migration for tbl_outlet_platform (Junction table for Outlet and Platform)
 */

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblOutletPlatform extends Migration
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
            'id_outlet' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'id_platform' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1'],
                'default'    => '1',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('id_outlet');
        $this->forge->addKey('id_platform');
        $this->forge->addUniqueKey(['id_outlet', 'id_platform']);
        
        $this->forge->createTable('tbl_outlet_platform', true);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_outlet_platform', true);
    }
}

