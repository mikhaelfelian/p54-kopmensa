<?php

namespace App\Services;

use App\Models\IonPermissionModel;
use App\Models\IonModuleModel;
use App\Models\IonActionModel;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-01
 * This file represents the Permission Service for handling access control.
 */
class PermissionService
{
    protected $permissionModel;
    protected $moduleModel;
    protected $actionModel;
    protected $ionAuth;

    public function __construct()
    {
        $this->permissionModel = new IonPermissionModel();
        $this->moduleModel = new IonModuleModel();
        $this->actionModel = new IonActionModel();
        $this->ionAuth = \IonAuth\Libraries\IonAuth::getInstance();
    }

    /**
     * Check if current user has permission for specific action on module
     */
    public function can($action, $moduleRoute = null)
    {
        $user = $this->ionAuth->user()->row();
        if (!$user) {
            return false;
        }

        // If no module specified, try to get from current route
        if (!$moduleRoute) {
            $moduleRoute = $this->getCurrentModuleRoute();
        }

        if (!$moduleRoute) {
            return false;
        }

        $module = $this->moduleModel->getModuleByRoute($moduleRoute);
        if (!$module) {
            return false;
        }

        return $this->permissionModel->hasPermission($user->id, $module['id'], $action);
    }

    /**
     * Check if current user can create in module
     */
    public function canCreate($moduleRoute = null)
    {
        return $this->can('create', $moduleRoute);
    }

    /**
     * Check if current user can read in module
     */
    public function canRead($moduleRoute = null)
    {
        return $this->can('read', $moduleRoute);
    }

    /**
     * Check if current user can read all records in module
     */
    public function canReadAll($moduleRoute = null)
    {
        return $this->can('read_all', $moduleRoute);
    }

    /**
     * Check if current user can update in module
     */
    public function canUpdate($moduleRoute = null)
    {
        return $this->can('update', $moduleRoute);
    }

    /**
     * Check if current user can update all records in module
     */
    public function canUpdateAll($moduleRoute = null)
    {
        return $this->can('update_all', $moduleRoute);
    }

    /**
     * Check if current user can delete in module
     */
    public function canDelete($moduleRoute = null)
    {
        return $this->can('delete', $moduleRoute);
    }

    /**
     * Check if current user can delete all records in module
     */
    public function canDeleteAll($moduleRoute = null)
    {
        return $this->can('delete_all', $moduleRoute);
    }

    /**
     * Check if current user can export from module
     */
    public function canExport($moduleRoute = null)
    {
        return $this->can('export', $moduleRoute);
    }

    /**
     * Check if current user can import to module
     */
    public function canImport($moduleRoute = null)
    {
        return $this->can('import', $moduleRoute);
    }

    /**
     * Check if current user can approve in module
     */
    public function canApprove($moduleRoute = null)
    {
        return $this->can('approve', $moduleRoute);
    }

    /**
     * Check if current user can reject in module
     */
    public function canReject($moduleRoute = null)
    {
        return $this->can('reject', $moduleRoute);
    }

    /**
     * Get current module route from URI
     */
    protected function getCurrentModuleRoute()
    {
        $uri = service('uri');
        $segments = $uri->getSegments();
        
        if (count($segments) >= 2) {
            return $segments[0] . '/' . $segments[1];
        }
        
        return null;
    }

    /**
     * Get all permissions for current user
     */
    public function getUserPermissions($userId = null)
    {
        if (!$userId) {
            $user = $this->ionAuth->user()->row();
            if (!$user) {
                return [];
            }
            $userId = $user->id;
        }

        return $this->permissionModel->getAllUserPermissions($userId);
    }

    /**
     * Get accessible modules for current user
     */
    public function getUserAccessibleModules($userId = null)
    {
        if (!$userId) {
            $user = $this->ionAuth->user()->row();
            if (!$user) {
                return [];
            }
            $userId = $user->id;
        }

        return $this->moduleModel->getUserAccessibleModules($userId);
    }

    /**
     * Grant permission to user or group
     */
    public function grantPermission($moduleRoute, $action, $groupId = null, $userId = null)
    {
        $module = $this->moduleModel->getModuleByRoute($moduleRoute);
        if (!$module) {
            return false;
        }

        $actionRecord = $this->actionModel->getActionByName($action);
        if (!$actionRecord) {
            return false;
        }

        return $this->permissionModel->grantPermission(
            $module['id'], 
            $actionRecord['id'], 
            $groupId, 
            $userId
        );
    }

    /**
     * Revoke permission from user or group
     */
    public function revokePermission($moduleRoute, $action, $groupId = null, $userId = null)
    {
        $module = $this->moduleModel->getModuleByRoute($moduleRoute);
        if (!$module) {
            return false;
        }

        $actionRecord = $this->actionModel->getActionByName($action);
        if (!$actionRecord) {
            return false;
        }

        return $this->permissionModel->revokePermission(
            $module['id'], 
            $actionRecord['id'], 
            $groupId, 
            $userId
        );
    }

    /**
     * Grant multiple permissions at once
     */
    public function grantMultiplePermissions($moduleRoute, $actions, $groupId = null, $userId = null)
    {
        $results = [];
        foreach ($actions as $action) {
            $results[$action] = $this->grantPermission($moduleRoute, $action, $groupId, $userId);
        }
        return $results;
    }

    /**
     * Revoke multiple permissions at once
     */
    public function revokeMultiplePermissions($moduleRoute, $actions, $groupId = null, $userId = null)
    {
        $results = [];
        foreach ($actions as $action) {
            $results[$action] = $this->revokePermission($moduleRoute, $action, $groupId, $userId);
        }
        return $results;
    }

    /**
     * Grant CRUD permissions to user or group
     */
    public function grantCrudPermissions($moduleRoute, $groupId = null, $userId = null)
    {
        $crudActions = ['create', 'read', 'update', 'delete'];
        return $this->grantMultiplePermissions($moduleRoute, $crudActions, $groupId, $userId);
    }

    /**
     * Grant full permissions to user or group
     */
    public function grantFullPermissions($moduleRoute, $groupId = null, $userId = null)
    {
        $allActions = ['create', 'read', 'read_all', 'update', 'update_all', 'delete', 'delete_all', 'export', 'import', 'approve', 'reject'];
        return $this->grantMultiplePermissions($moduleRoute, $allActions, $groupId, $userId);
    }

    /**
     * Check if user is admin (has all permissions)
     */
    public function isAdmin($userId = null)
    {
        if (!$userId) {
            $user = $this->ionAuth->user()->row();
            if (!$user) {
                return false;
            }
            $userId = $user->id;
        }

        // Check if user is in admin group
        $groups = $this->ionAuth->getUsersGroups($userId)->result();
        foreach ($groups as $group) {
            if ($group->name === 'admin') {
                return true;
            }
        }

        return false;
    }

    /**
     * Get permission summary for user
     */
    public function getPermissionSummary($userId = null)
    {
        if (!$userId) {
            $user = $this->ionAuth->user()->row();
            if (!$user) {
                return [];
            }
            $userId = $user->id;
        }

        $permissions = $this->getUserPermissions($userId);
        $summary = [];

        foreach ($permissions as $permission) {
            $moduleName = $permission['module_name'];
            $actionName = $permission['action_name'];
            
            if (!isset($summary[$moduleName])) {
                $summary[$moduleName] = [];
            }
            
            $summary[$moduleName][] = $actionName;
        }

        return $summary;
    }
}
