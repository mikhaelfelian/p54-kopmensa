<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-01
 * This file represents the Migration for tbl_ion_actions table.
 */
class Migration_20250101000000_create_tbl_ion_actions extends Migration
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
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
                'comment'    => 'Action name: create, read, update, delete, etc.',
            ],
            'description' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Description of the action',
            ],
            'is_active' => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1'],
                'default'    => '1',
                'comment'    => 'Whether action is active',
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

        $this->forge->dropTable('tbl_ion_actions', true);
        $this->forge->addKey('id', true);
        $this->forge->addKey('name');
        $this->forge->createTable('tbl_ion_actions');

        // Insert default actions
        $this->db->table('tbl_ion_actions')->insertBatch([
            ['name' => 'create', 'description' => 'Create new records', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'read', 'description' => 'Read/view records', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'read_all', 'description' => 'Read all records (not just own)', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'update', 'description' => 'Update records', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'update_all', 'description' => 'Update all records (not just own)', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'delete', 'description' => 'Delete records', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'delete_all', 'description' => 'Delete all records (not just own)', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'export', 'description' => 'Export data', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'import', 'description' => 'Import data', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'approve', 'description' => 'Approve records', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'reject', 'description' => 'Reject records', 'created_at' => date('Y-m-d H:i:s')],
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_ion_actions');
    }
}
