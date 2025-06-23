<?php

namespace App\Controllers\Api\Pos;

use App\Controllers\BaseController;
use App\Models\ItemModel;
use CodeIgniter\API\ResponseTrait;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2024-07-31
 * Github: github.com/mikhaelfelian
 * description: API controller for managing Products (Produk/Item) for the POS.
 * This file represents the Produk API controller.
 */
class Produk extends BaseController
{
    use ResponseTrait;

    /**
     * Get a paginated list of active products.
     * Supports search by keyword.
     *
     * @return \CodeIgniter\HTTP\Response
     */
    public function index()
    {
        $model = new ItemModel();

        $perPage = $this->request->getGet('per_page') ?? 10;
        $keyword = $this->request->getGet('keyword') ?? null;

        $items = $model->getItemsWithRelationsActive($perPage, $keyword);
        $pager = $model->pager->getDetails('items');

        // Transform the data to match the desired format
        $formattedItems = [];
        foreach ($items as $item) {
            $formattedItems[] = [
                'created_at' => $item->created_at,
                'merk'       => $item->merk,
                'kategori'   => $item->kategori,
                'kode'       => $item->kode,
                'barcode'    => $item->barcode,
                'item'       => $item->item,
                'deskripsi'  => $item->deskripsi,
                'jml_min'    => (int) $item->jml_min,
                'harga_jual' => (float) $item->harga_jual,
                'harga_beli' => (float) $item->harga_beli,
                'foto'       => $item->foto,
            ];
        }

        $data = [
            'total'        => $pager['total'],
            'current_page' => $pager['currentPage'],
            'per_page'     => $pager['perPage'],
            'total_page'   => $pager['pageCount'],
            'items'        => $formattedItems,
        ];

        return $this->respond($data);
    }

    /**
     * Get the details of a single product by its ID.
     *
     * @param int $id The product ID
     * @return \CodeIgniter\HTTP\Response
     */
    public function detail($id = null)
    {
        $model = new ItemModel();
        $item = $model->getItemWithRelations($id);

        if (!$item) {
            return $this->failNotFound('Produk dengan ID ' . $id . ' tidak ditemukan.');
        }

        // Format the response to match the documentation
        $data = [
            'id'         => (int) $item->id,
            'kode'       => $item->kode,
            'barcode'    => $item->barcode,
            'item'       => $item->item,
            'deskripsi'  => $item->deskripsi,
            'jml_min'    => (int) $item->jml_min,
            'harga_jual' => (float) $item->harga_jual,
            'harga_beli' => (float) $item->harga_beli,
            'foto'       => $item->foto,
            'kategori'   => $item->kategori,
            'merk'       => $item->merk,
        ];

        return $this->respond($data);
    }
} 