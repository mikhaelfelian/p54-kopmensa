<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-01
 * This file represents the Migration for tbl_ion_permissions table.
 */
class Migration_20250101000001_create_tbl_ion_permissions extends Migration
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
            'module_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'comment'    => 'Reference to tbl_ion_modules.id',
            ],
            'action_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'comment'    => 'Reference to tbl_ion_actions.id',
            ],
            'group_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'Reference to groups.id (null for user-specific)',
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'Reference to users.id (null for group-specific)',
            ],
            'is_granted' => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1'],
                'default'    => '1',
                'comment'    => '1 = granted, 0 = denied',
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

        $this->forge->dropTable('tbl_ion_permissions', true);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['module_id', 'action_id', 'group_id', 'user_id']);
        $this->forge->addKey('group_id');
        $this->forge->addKey('user_id');
        
        // Note: Foreign key constraints are commented out to avoid migration issues
        // They can be added later using ALTER TABLE statements if needed
        // $this->forge->addForeignKey('module_id', 'tbl_ion_modules', 'id', 'CASCADE', 'CASCADE');
        // $this->forge->addForeignKey('action_id', 'tbl_ion_actions', 'id', 'CASCADE', 'CASCADE');
        // $this->forge->addForeignKey('group_id', 'groups', 'id', 'SET NULL', 'CASCADE');
        // $this->forge->addForeignKey('user_id', 'users', 'id', 'SET NULL', 'CASCADE');
        
        $this->forge->createTable('tbl_ion_permissions');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_ion_permissions');
    }
}
