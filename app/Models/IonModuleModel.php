<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-01
 * This file represents the IonModule Model.
 */
class IonModuleModel extends Model
{
    protected $table            = 'tbl_ion_modules';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'parent_id', 'name', 'route', 'icon', 'is_menu', 
        'is_active', 'sort_order', 'default_permissions'
    ];

    // Dates
    protected $useTimestamps = false;

    // Validation
    protected $validationRules      = [
        'name' => 'required|max_length[100]',
        'route' => 'required|max_length[100]',
        'is_menu' => 'required|in_list[0,1]',
        'is_active' => 'required|in_list[0,1]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Get all active modules
     */
    public function getActiveModules()
    {
        return $this->where('is_active', '1')
                    ->orderBy('sort_order', 'ASC')
                    ->findAll();
    }

    /**
     * Get menu modules (for sidebar)
     */
    public function getMenuModules()
    {
        return $this->where('is_active', '1')
                    ->where('is_menu', '1')
                    ->orderBy('sort_order', 'ASC')
                    ->findAll();
    }

    /**
     * Get modules with hierarchy (parent-child)
     */
    public function getModulesHierarchy()
    {
        $modules = $this->getActiveModules();
        $hierarchy = [];
        
        foreach ($modules as $module) {
            if ($module['parent_id'] == 0) {
                $module['children'] = $this->getChildModules($module['id']);
                $hierarchy[] = $module;
            }
        }
        
        return $hierarchy;
    }

    /**
     * Get child modules for a parent
     */
    public function getChildModules($parentId)
    {
        return $this->where('parent_id', $parentId)
                    ->where('is_active', '1')
                    ->orderBy('sort_order', 'ASC')
                    ->findAll();
    }

    /**
     * Get module by route
     */
    public function getModuleByRoute($route)
    {
        return $this->where('route', $route)
                    ->where('is_active', '1')
                    ->first();
    }

    /**
     * Get module permissions as array
     */
    public function getModulePermissions($moduleId)
    {
        $module = $this->find($moduleId);
        if (!$module || empty($module['default_permissions'])) {
            return [];
        }
        
        return json_decode($module['default_permissions'], true) ?: [];
    }

    /**
     * Set module permissions
     */
    public function setModulePermissions($moduleId, $permissions)
    {
        $data = [
            'default_permissions' => json_encode($permissions)
        ];
        
        return $this->update($moduleId, $data);
    }

    /**
     * Get modules accessible by user
     */
    public function getUserAccessibleModules($userId)
    {
        $permissionModel = new IonPermissionModel();
        $userPermissions = $permissionModel->getAllUserPermissions($userId);
        
        $accessibleModules = [];
        foreach ($userPermissions as $permission) {
            $module = $this->find($permission['module_id']);
            if ($module && $module['is_active'] == '1') {
                $accessibleModules[$module['id']] = $module;
            }
        }
        
        return array_values($accessibleModules);
    }
}
