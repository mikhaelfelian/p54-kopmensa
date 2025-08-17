<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-01
 * This file represents the Seeder for tbl_ion_permissions table.
 */
class IonPermissionSeeder extends Seeder
{
    public function run()
    {
        // Get admin group ID (assuming it's 1)
        $adminGroupId = 1;
        $memberGroupId = 2;

        // Get all modules
        $modules = $this->db->table('tbl_ion_modules')->get()->getResultArray();
        
        // Get all actions
        $actions = $this->db->table('tbl_ion_actions')->get()->getResultArray();

        $permissions = [];

        foreach ($modules as $module) {
            $moduleId = $module['id'];
            $defaultPermissions = json_decode($module['default_permissions'], true) ?: [];

            foreach ($actions as $action) {
                $actionId = $action['id'];
                $actionName = $action['name'];

                // Admin gets all permissions
                $permissions[] = [
                    'module_id' => $moduleId,
                    'action_id' => $actionId,
                    'group_id' => $adminGroupId,
                    'user_id' => null,
                    'is_granted' => '1',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                // Members get limited permissions based on default_permissions
                if (isset($defaultPermissions[$actionName]) && $defaultPermissions[$actionName]) {
                    $permissions[] = [
                        'module_id' => $moduleId,
                        'action_id' => $actionId,
                        'group_id' => $memberGroupId,
                        'user_id' => null,
                        'is_granted' => '1',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                } else {
                    // Explicitly deny permission for members
                    $permissions[] = [
                        'module_id' => $moduleId,
                        'action_id' => $actionId,
                        'group_id' => $memberGroupId,
                        'user_id' => null,
                        'is_granted' => '0',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                }
            }
        }

        $this->db->table('tbl_ion_permissions')->insertBatch($permissions);
    }
}
