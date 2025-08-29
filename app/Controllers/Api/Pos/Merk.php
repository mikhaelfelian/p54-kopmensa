<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2024-07-05
 * Github : github.com/mikhaelfelian
 * description : API controller for active product brands (merk)
 * This file represents the Merk API controller for mobile Android.
 */

namespace App\Controllers\Api\Pos;

use App\Controllers\BaseController;
use App\Models\MerkModel;
use CodeIgniter\API\ResponseTrait;

class Merk extends BaseController
{
    use ResponseTrait;

    /**
     * Get a paginated list of active brands.
     * Supports search by keyword.
     *
     * Response format:
     * {
     *   "total": 127,
     *   "current_page": 1,
     *   "per_page": 10,
     *   "total_page": 13,
     *   "items": [
     *     {
     *       "id": 1,
     *       "kode": "NI0001",
     *       "merk": "Nike",
     *       "keterangan": "Nike sports brand",
     *       "status": 1,
     *       "created_at": "2025-07-05 12:06:02",
     *       "updated_at": "2025-07-05 12:06:02"
     *     }
     *   ]
     * }
     */
    public function index()
    {
        $merkModel = new MerkModel();

        $perPage = (int) ($this->request->getGet('per_page') ?? 10);
        $page    = (int) ($this->request->getGet('page') ?? 1);
        $keyword = $this->request->getGet('keyword') ?? null;

        $builder = $merkModel->where('status', '1')->orderBy('id', 'DESC');
        if ($keyword) {
            $builder->groupStart()
                ->like('merk', $keyword)
                ->orLike('kode', $keyword)
                ->orLike('keterangan', $keyword)
                ->groupEnd();
        }

        $items = $builder->paginate($perPage, 'brands', $page);
        $pager = $merkModel->pager->getDetails('brands');

        // Format items as array of objects with only the required fields
        $formattedItems = [];
        foreach ($items as $item) {
            $formattedItems[] = [
                'id'         => (int) $item->id,
                'kode'       => $item->kode,
                'merk'       => $item->merk,
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

    /**
     * Get detail of a brand by id
     * 
     * Response format:
     * {
     *   "category": {
     *     "id": 1,
     *     "kode": "NI0001",
     *     "merk": "Nike",
     *     "keterangan": "Nike sports brand",
     *     "status": 1,
     *     "created_at": "2025-07-05 12:06:02",
     *     "updated_at": "2025-07-05 12:06:02"
     *   }
     * }
     */
    public function detail($id = null)
    {
        $merkModel = new MerkModel();
        $brand = $merkModel->where('id', $id)->first();
        if (!$brand) {
            return $this->failNotFound('Merk dengan ID ' . $id . ' tidak ditemukan.');
        }
        return $this->respond(['brand' => $brand]);
    }

    /**
     * Get all active brands (no pagination) for dropdown/select purposes
     * Useful for mobile forms that need brand selection
     * 
     * Response format:
     * {
     *   "brands": [
     *     {
     *       "id": 1,
     *       "kode": "NI0001",
     *       "merk": "Nike"
     *     }
     *   ]
     * }
     */
    public function all()
    {
        $merkModel = new MerkModel();
        
        $brands = $merkModel->select('id, kode, merk')
                           ->where('status', '1')
                           ->orderBy('merk', 'ASC')
                           ->findAll();

        $formattedBrands = [];
        foreach ($brands as $brand) {
            $formattedBrands[] = [
                'id'   => (int) $brand->id,
                'kode' => $brand->kode,
                'merk' => $brand->merk,
            ];
        }

        return $this->respond(['brands' => $formattedBrands]);
    }

    /**
     * Search brands by keyword (quick search for mobile)
     * Returns limited results for better mobile performance
     * 
     * Response format:
     * {
     *   "brands": [
     *     {
     *       "id": 1,
     *       "kode": "NI0001",
     *       "merk": "Nike"
     *     }
     *   ]
     * }
     */
    public function search()
    {
        $keyword = $this->request->getGet('q') ?? '';
        
        if (empty($keyword)) {
            return $this->respond(['brands' => []]);
        }

        $merkModel = new MerkModel();
        
        $brands = $merkModel->select('id, kode, merk')
                           ->where('status', '1')
                           ->groupStart()
                           ->like('merk', $keyword)
                           ->orLike('kode', $keyword)
                           ->groupEnd()
                           ->orderBy('merk', 'ASC')
                           ->limit(20) // Limit results for mobile performance
                           ->findAll();

        $formattedBrands = [];
        foreach ($brands as $brand) {
            $formattedBrands[] = [
                'id'   => (int) $brand->id,
                'kode' => $brand->kode,
                'merk' => $brand->merk,
            ];
        }

        return $this->respond(['brands' => $formattedBrands]);
    }
}
