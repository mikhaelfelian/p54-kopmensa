<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-07-06
 * Github : github.com/mikhaelfelian
 * description : API Controller for handling active outlet data (list & detail)
 * This file represents the Controller class for Store (Outlet) API.
 */

namespace App\Controllers\Api\Pos;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\OutletModel;

class Store extends BaseController
{
    use ResponseTrait;

    /**
     * Get all active outlets (status = 1) with formatted output and pagination
     */
    public function index()
    {
        $model = new OutletModel();
        $perPage = (int) ($this->request->getGet('per_page') ?? 10);
        $page = (int) ($this->request->getGet('page') ?? 1);
        $keyword = $this->request->getGet('keyword') ?? null;

        $builder = $model->where('status', '1');
        if ($keyword) {
            $builder->like('nama', $keyword);
        }

        // Get paginated results
        $outlets = $builder->orderBy('id', 'DESC')->paginate($perPage, 'outlets', $page);
        $pager = $model->pager->getDetails('outlets');

        // If there are no results, ensure the response is consistent
        $formattedItems = [];
        if (!empty($outlets)) {
            foreach ($outlets as $outlet) {
                $formattedItems[] = [
                    'id'         => (int) $outlet->id,
                    'id_user'    => (int) $outlet->id_user,
                    'kode'       => $outlet->kode,
                    'nama'       => $outlet->nama,
                    'deskripsi'  => $outlet->deskripsi,
                    'status'     => (int) $outlet->status,
                    'status_hps' => (int) $outlet->status_hps,
                    'created_at' => $outlet->created_at,
                    'updated_at' => $outlet->updated_at,
                ];
            }
        }

        // If there are no outlets, set total_page to 0 and total to 0
        $total = $pager['total'] ?? 0;
        $totalPage = $pager['pageCount'] ?? 0;

        $data = [
            'total'        => $total,
            'current_page' => (int) $page,
            'per_page'     => $pager['perPage'] ?? $perPage,
            'total_page'   => $totalPage,
            'outlets'      => $formattedItems,
        ];

        return $this->respond($data);
    }

    /**
     * Get detail of an active outlet by ID, formatted as items array
     * @param int $id
     */
    public function detail($id)
    {
        $model = new OutletModel();
        $outlet = $model->where('status', 1)->where('id', $id)->first();
        if (!$outlet) {
            return $this->failNotFound('Outlet not found or inactive.');
        }
        $item = [
            'id'         => (int) $outlet->id,
            'id_user'    => (int) $outlet->id_user,
            'kode'       => $outlet->kode,
            'nama'       => $outlet->nama,
            'deskripsi'  => $outlet->deskripsi,
            'status'     => (int) $outlet->status,
            'status_hps' => (int) $outlet->status_hps,
            'created_at' => $outlet->created_at,
            'updated_at' => $outlet->updated_at,
        ];
        $data = [
            'total'        => 1,
            'current_page' => 1,
            'per_page'     => 1,
            'total_page'   => 1,
            'items'        => [$item],
        ];
        return $this->respond($data);
    }
} 