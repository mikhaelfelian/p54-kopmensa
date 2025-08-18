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
use App\Models\GudangModel;

class Store extends BaseController
{
    use ResponseTrait;
    protected $gudangModel;
    public function __construct()
    {
        $this->gudangModel = new GudangModel();
    }
    
    /**
     * Get all active outlets (status=1, status_otl=1)
     * GET /api/pos/outlets
     */
    public function getOutlets($id = null)
    {
        // Only select the columns: id, created_at, kode, nama, deskripsi
        $builder = $this->gudangModel
            ->select('id, created_at, kode, nama, deskripsi')
            ->where('status', '1')
            ->where('status_otl', '1');

        // Support both "page" and "current_page" as query params, default to 1
        $page = (int) ($this->request->getGet('current_page') ?? $this->request->getGet('page') ?? 1);
        $perPage = (int) ($this->request->getGet('per_page') ?? 10);

        if ($id !== null) {
            $outlet = $builder->where('id', $id)->first();
            if ($outlet) {
                $data = [
                    'total'        => 1,
                    'current_page' => 1,
                    'per_page'     => 1,
                    'total_page'   => 1,
                    'outlets'      => [[
                        'id'         => (int) $outlet->id,
                        'created_at' => $outlet->created_at,
                        'kode'       => $outlet->kode,
                        'nama'       => $outlet->nama,
                        'deskripsi'  => $outlet->deskripsi,
                    ]]
                ];
                return $this->respond($data);
            } else {
                return $this->respond([
                    'total'        => 0,
                    'current_page' => 1,
                    'per_page'     => 1,
                    'total_page'   => 0,
                    'outlets'      => [],
                    'message'      => 'Outlet tidak ditemukan'
                ], 404);
            }
        } else {
            // Get total count for pagination
            $total = $builder->countAllResults(false);

            // Apply pagination
            $offset = ($page - 1) * $perPage;
            $outlets = $builder->orderBy('id', 'ASC')->findAll($perPage, $offset);

            // Format as array of items
            $items = [];
            foreach ($outlets as $outlet) {
                $items[] = [
                    'id'         => (int) $outlet->id,
                    'created_at' => $outlet->created_at,
                    'kode'       => $outlet->kode,
                    'nama'       => $outlet->nama,
                    'deskripsi'  => $outlet->deskripsi,
                ];
            }

            $totalPage = $perPage > 0 ? (int) ceil($total / $perPage) : 1;

            $data = [
                'total'        => $total,
                'current_page' => (int) $page,
                'per_page'     => $perPage,
                'total_page'   => $totalPage,
                'outlets'      => $items,
            ];
            return $this->respond($data);
        }
    }
} 