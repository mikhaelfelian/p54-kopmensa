<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-01
 * This file represents the IonAction Model.
 */
class IonActionModel extends Model
{
    protected $table            = 'tbl_ion_actions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name', 'description', 'is_active', 'created_at', 'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'name' => 'required|max_length[50]',
        'is_active' => 'required|in_list[0,1]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Get all active actions
     */
    public function getActiveActions()
    {
        return $this->where('is_active', '1')
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }

    /**
     * Get action by name
     */
    public function getActionByName($name)
    {
        return $this->where('name', $name)
                    ->where('is_active', '1')
                    ->first();
    }

    /**
     * Get actions by names array
     */
    public function getActionsByNames($names)
    {
        return $this->whereIn('name', $names)
                    ->where('is_active', '1')
                    ->findAll();
    }

    /**
     * Get CRUD actions (create, read, update, delete)
     */
    public function getCrudActions()
    {
        $crudActions = ['create', 'read', 'update', 'delete'];
        return $this->whereIn('name', $crudActions)
                    ->where('is_active', '1')
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }

    /**
     * Get advanced actions (export, import, approve, reject)
     */
    public function getAdvancedActions()
    {
        $advancedActions = ['export', 'import', 'approve', 'reject'];
        return $this->whereIn('name', $advancedActions)
                    ->where('is_active', '1')
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }
}
