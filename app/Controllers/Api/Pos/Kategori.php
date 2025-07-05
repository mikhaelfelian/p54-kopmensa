<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2024-07-05
 * Github : github.com/mikhaelfelian
 * description : API controller for active product categories
 * This file represents the Kategori API controller.
 */

namespace App\Controllers\Api\Pos;

use App\Controllers\BaseController;
use App\Models\KategoriModel;
use CodeIgniter\API\ResponseTrait;

class Kategori extends BaseController
{
    use ResponseTrait;

    /**
     * Get a paginated list of active categories.
     * Supports search by keyword.
     *
     * Response format:
     * {
     *   "total": 527,
     *   "current_page": 1,
     *   "per_page": 10,
     *   "total_page": 53,
     *   "items": [
     *     {
     *       "id": 1,
     *       "kode": "KTG-001",
     *       "kategori": "MINUMAN",
     *       "keterangan": "Minuman dan sejenisnya",
     *       "status": 1,
     *       "created_at": "2025-07-05 12:06:02",
     *       "updated_at": "2025-07-05 12:06:02"
     *     }
     *   ]
     * }
     */
    public function index()
    {
        $kategoriModel = new KategoriModel();

        $perPage = (int) ($this->request->getGet('per_page') ?? 10);
        $page    = (int) ($this->request->getGet('page') ?? 1);
        $keyword = $this->request->getGet('keyword') ?? null;

        $builder = $kategoriModel->where('status', '1')->orderBy('id', 'DESC');
        if ($keyword) {
            $builder->groupStart()
                ->like('kategori', $keyword)
                ->orLike('kode', $keyword)
                ->orLike('keterangan', $keyword)
                ->groupEnd();
        }

        $items = $builder->paginate($perPage, 'categories', $page);
        $pager = $kategoriModel->pager->getDetails('categories');

        // Format items as array of objects with only the required fields
        $formattedItems = [];
        foreach ($items as $item) {
            $formattedItems[] = [
                'id'         => (int) $item->id,
                'kode'       => $item->kode,
                'kategori'   => $item->kategori,
                'keterangan' => $item->keterangan,
                'status'     => (int) $item->status,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ];
        }

        $data = [
            'total'        => $pager['total'],
            'current_page' => (int) $page,
            'per_page'     => $pager['perPage'],
            'total_page'   => $pager['pageCount'],
            'items'        => $formattedItems,
        ];

        return $this->respond($data);
    }

    // Get detail of a category by id
    public function detail($id = null)
    {
        $kategoriModel = new KategoriModel();
        $category = $kategoriModel->where('id', $id)->first();
        if (!$category) {
            return $this->failNotFound('Kategori dengan ID ' . $id . ' tidak ditemukan.');
        }
        return $this->respond(['category' => $category]);
    }
} 