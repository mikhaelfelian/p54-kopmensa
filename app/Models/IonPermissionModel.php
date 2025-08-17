<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-01
 * This file represents the IonPermission Model.
 */
class IonPermissionModel extends Model
{
    protected $table            = 'tbl_ion_permissions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'module_id', 'action_id', 'group_id', 'user_id', 
        'is_granted', 'created_at', 'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'module_id' => 'required|integer',
        'action_id' => 'required|integer',
        'is_granted' => 'required|in_list[0,1]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Get permissions for a specific user
     */
    public function getUserPermissions($userId, $moduleId = null)
    {
        $builder = $this->builder();
        $builder->select('tbl_ion_permissions.*, tbl_ion_actions.name as action_name, tbl_ion_modules.name as module_name')
                ->join('tbl_ion_actions', 'tbl_ion_actions.id = tbl_ion_permissions.action_id')
                ->join('tbl_ion_modules', 'tbl_ion_modules.id = tbl_ion_permissions.module_id')
                ->where('tbl_ion_permissions.user_id', $userId)
                ->where('tbl_ion_permissions.is_granted', '1');

        if ($moduleId) {
            $builder->where('tbl_ion_permissions.module_id', $moduleId);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Get permissions for a specific group
     */
    public function getGroupPermissions($groupId, $moduleId = null)
    {
        $builder = $this->builder();
        $builder->select('tbl_ion_permissions.*, tbl_ion_actions.name as action_name, tbl_ion_modules.name as module_name')
                ->join('tbl_ion_actions', 'tbl_ion_actions.id = tbl_ion_permissions.action_id')
                ->join('tbl_ion_modules', 'tbl_ion_modules.id = tbl_ion_permissions.module_id')
                ->where('tbl_ion_permissions.group_id', $groupId)
                ->where('tbl_ion_permissions.is_granted', '1');

        if ($moduleId) {
            $builder->where('tbl_ion_permissions.module_id', $moduleId);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Get all permissions for a user (including group permissions)
     */
    public function getAllUserPermissions($userId)
    {
        // Get user-specific permissions
        $userPermissions = $this->getUserPermissions($userId);
        
        // Get user's groups
        $ionAuth = \IonAuth\Libraries\IonAuth::getInstance();
        $user = $ionAuth->user($userId)->row();
        $groups = $ionAuth->getUsersGroups($userId)->result();
        
        $groupPermissions = [];
        foreach ($groups as $group) {
            $groupPerms = $this->getGroupPermissions($group->id);
            $groupPermissions = array_merge($groupPermissions, $groupPerms);
        }
        
        // Merge and remove duplicates
        $allPermissions = array_merge($userPermissions, $groupPermissions);
        $uniquePermissions = [];
        
        foreach ($allPermissions as $permission) {
            $key = $permission['module_id'] . '_' . $permission['action_id'];
            if (!isset($uniquePermissions[$key])) {
                $uniquePermissions[$key] = $permission;
            }
        }
        
        return array_values($uniquePermissions);
    }

    /**
     * Check if user has permission for specific action on module
     */
    public function hasPermission($userId, $moduleId, $actionName)
    {
        $builder = $this->builder();
        $builder->select('tbl_ion_permissions.*')
                ->join('tbl_ion_actions', 'tbl_ion_actions.id = tbl_ion_permissions.action_id')
                ->where('tbl_ion_permissions.module_id', $moduleId)
                ->where('tbl_ion_actions.name', $actionName)
                ->where('tbl_ion_permissions.is_granted', '1')
                ->groupStart()
                    ->where('tbl_ion_permissions.user_id', $userId)
                    ->orWhere('tbl_ion_permissions.group_id IN (SELECT group_id FROM users_groups WHERE user_id = ' . $userId . ')')
                ->groupEnd();

        return $builder->countAllResults() > 0;
    }

    /**
     * Grant permission to user or group
     */
    public function grantPermission($moduleId, $actionId, $groupId = null, $userId = null)
    {
        $data = [
            'module_id' => $moduleId,
            'action_id' => $actionId,
            'group_id' => $groupId,
            'user_id' => $userId,
            'is_granted' => '1',
        ];

        // Check if permission already exists
        $existing = $this->where($data)->first();
        if ($existing) {
            return $this->update($existing['id'], ['is_granted' => '1']);
        }

        return $this->insert($data);
    }

    /**
     * Revoke permission from user or group
     */
    public function revokePermission($moduleId, $actionId, $groupId = null, $userId = null)
    {
        $data = [
            'module_id' => $moduleId,
            'action_id' => $actionId,
            'group_id' => $groupId,
            'user_id' => $userId,
        ];

        return $this->where($data)->set(['is_granted' => '0'])->update();
    }
}
