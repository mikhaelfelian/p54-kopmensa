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
use CodeIgniter\API\ResponseTrait;

class PettyCategory extends BaseController
{
    use ResponseTrait;

    protected $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new PettyCategoryModel();
    }

    /**
     * Get petty cash categories overview (GET endpoint)
     */
    public function index()
    {
        $categories = $this->categoryModel->getActiveCategories();
        
        return $this->respond([
            'categories' => $categories,
            'total' => count($categories)
        ]);
    }

    /**
     * Get all petty cash categories
     */
    public function getCategories()
    {
        $categories = $this->categoryModel->getActiveCategories();
        
        return $this->respond($categories);
    }

    /**
     * Get petty cash categories with usage count
     */
    public function getCategoriesWithUsage()
    {
        $categories = $this->categoryModel->getCategoriesWithUsage();
        
        return $this->respond($categories);
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
            return $this->failValidationErrors('Category ID required');
        }

        $category = $this->categoryModel->find($id);
        if (!$category) {
            return $this->failNotFound('Category not found');
        }

        // Convert to array if it's an object
        if (is_object($category)) {
            $category = (array) $category;
        }

        return $this->respond($category);
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
            return $this->failValidationErrors('Category name is required');
        }

        // Check if name already exists
        $existingCategory = $this->categoryModel->where('name', $name)->first();
        if ($existingCategory) {
            return $this->failValidationErrors('Category name already exists');
        }

        $data = [
            'name' => $name,
            'description' => $description ?: null,
            'is_active' => $is_active
        ];

        if ($this->categoryModel->insert($data)) {
            return $this->respond([
                'id' => $this->categoryModel->insertID,
                'name' => $name
            ]);
        } else {
            return $this->failServerError('Failed to create category');
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
            return $this->failValidationErrors('Category ID required');
        }

        $category = $this->categoryModel->find($id);
        if (!$category) {
            return $this->failNotFound('Category not found');
        }

        // Convert to array if it's an object
        if (is_object($category)) {
            $category = (array) $category;
        }

        $name = $this->request->getPost('name');
        $description = $this->request->getPost('description');
        $is_active = $this->request->getPost('is_active');

        if (!$name) {
            return $this->failValidationErrors('Category name is required');
        }

        // Check if name already exists (excluding current category)
        $existingCategory = $this->categoryModel->where('name', $name)->where('id !=', $id)->first();
        if ($existingCategory) {
            return $this->failValidationErrors('Category name already exists');
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
            return $this->respond(['message' => 'Category updated successfully']);
        } else {
            return $this->failServerError('Failed to update category');
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
            return $this->failValidationErrors('Category ID required');
        }

        $category = $this->categoryModel->find($id);
        if (!$category) {
            return $this->failNotFound('Category not found');
        }

        // Convert to array if it's an object
        if (is_object($category)) {
            $category = (array) $category;
        }

        // Toggle status (1 to 0, 0 to 1)
        $newStatus = $category['status'] == '1' ? '0' : '1';
        
        $data = [
            'status' => $newStatus,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->categoryModel->update($id, $data)) {
            return $this->respond([
                'message' => 'Category status updated successfully',
                'new_status' => $newStatus
            ]);
        } else {
            return $this->failServerError('Failed to update category status');
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
            return $this->failValidationErrors('Category ID required');
        }

        $category = $this->categoryModel->find($id);
        if (!$category) {
            return $this->failNotFound('Category not found');
        }

        // Convert to array if it's an object
        if (is_object($category)) {
            $category = (array) $category;
        }

        // Check if category is used in petty cash entries
        $pettyModel = new \App\Models\PettyModel();
        $isUsed = $pettyModel->where('category_id', $id)->first();
        if ($isUsed) {
            return $this->failValidationErrors('Cannot delete category that is still in use');
        }

        if ($this->categoryModel->delete($id)) {
            return $this->respond(['message' => 'Category deleted successfully']);
        } else {
            return $this->failServerError('Failed to delete category');
        }
    }

    /**
     * Search categories
     */
    public function search()
    {
        $keyword = $this->request->getPost('keyword') ?? $this->request->getGet('keyword');
        
        if (!$keyword) {
            return $this->failValidationErrors('Search keyword required');
        }

        $categories = $this->categoryModel->where('status', '1')
            ->groupStart()
            ->like('nama', $keyword)
            ->orLike('kode', $keyword)
            ->orLike('deskripsi', $keyword)
            ->groupEnd()
            ->orderBy('nama', 'ASC')
            ->findAll();
        
        return $this->respond($categories);
    }

    /**
     * Create new petty cash category (store method for POST)
     */
    public function store()
    {
        return $this->create();
    }
}
