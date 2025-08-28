<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-28
 * Github: github.com/mikhaelfelian
 * Description: API Controller for Petty Cash Categories management via mobile app
 * This file represents the Controller.
 */

namespace App\Controllers\Api\Pos;

use App\Controllers\BaseController;
use App\Models\PettyCategoryModel;

class PettyCategory extends BaseController
{
    protected $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new PettyCategoryModel();
    }

    /**
     * Get all petty cash categories
     */
    public function getCategories()
    {
        $categories = $this->categoryModel->getActiveCategories();
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Get petty cash categories with usage count
     */
    public function getCategoriesWithUsage()
    {
        $categories = $this->categoryModel->getCategoriesWithUsage();
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Get petty cash category by ID
     */
    public function getCategory($id = null)
    {
        if (!$id) {
            $id = $this->request->getPost('id');
        }

        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Category ID required'
            ]);
        }

        $category = $this->categoryModel->find($id);
        if (!$category) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Category not found'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $category
        ]);
    }

    /**
     * Create new petty cash category
     */
    public function create()
    {
        $name = $this->request->getPost('name');
        $description = $this->request->getPost('description');
        $is_active = $this->request->getPost('is_active') ?? 1;

        if (!$name) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Category name is required'
            ]);
        }

        // Check if name already exists
        $existingCategory = $this->categoryModel->where('name', $name)->first();
        if ($existingCategory) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Category name already exists'
            ]);
        }

        $data = [
            'name' => $name,
            'description' => $description ?: null,
            'is_active' => $is_active
        ];

        if ($this->categoryModel->insert($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Category created successfully',
                'data' => [
                    'id' => $this->categoryModel->insertID,
                    'name' => $name
                ]
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create category'
            ]);
        }
    }

    /**
     * Update petty cash category
     */
    public function update($id = null)
    {
        if (!$id) {
            $id = $this->request->getPost('id');
        }

        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Category ID required'
            ]);
        }

        $category = $this->categoryModel->find($id);
        if (!$category) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Category not found'
            ]);
        }

        $name = $this->request->getPost('name');
        $description = $this->request->getPost('description');
        $is_active = $this->request->getPost('is_active');

        if (!$name) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Category name is required'
            ]);
        }

        // Check if name already exists (excluding current category)
        $existingCategory = $this->categoryModel->where('name', $name)->where('id !=', $id)->first();
        if ($existingCategory) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Category name already exists'
            ]);
        }

        $data = [
            'name' => $name,
            'description' => $description ?: null
        ];

        // Only update is_active if provided
        if ($is_active !== null) {
            $data['is_active'] = $is_active;
        }

        if ($this->categoryModel->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Category updated successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update category'
            ]);
        }
    }

    /**
     * Toggle category status
     */
    public function toggleStatus($id = null)
    {
        if (!$id) {
            $id = $this->request->getPost('id');
        }

        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Category ID required'
            ]);
        }

        if ($this->categoryModel->toggleStatus($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Category status updated successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update category status'
            ]);
        }
    }

    /**
     * Delete petty cash category
     */
    public function delete($id = null)
    {
        if (!$id) {
            $id = $this->request->getPost('id');
        }

        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Category ID required'
            ]);
        }

        if (!$this->categoryModel->canDelete($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cannot delete category that is still in use'
            ]);
        }

        if ($this->categoryModel->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Category deleted successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete category'
            ]);
        }
    }

    /**
     * Search categories
     */
    public function search()
    {
        $keyword = $this->request->getPost('keyword') ?? $this->request->getGet('keyword');
        
        if (!$keyword) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Search keyword required'
            ]);
        }

        $categories = $this->categoryModel->searchCategories($keyword);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $categories
        ]);
    }
}
