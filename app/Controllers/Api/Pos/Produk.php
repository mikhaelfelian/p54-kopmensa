<?php

namespace App\Controllers\Api\Pos;

use App\Controllers\BaseController;
use App\Models\ItemModel;
use App\Models\ItemHargaModel;
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
        $mItem        = new ItemModel();
        $mItemHarga   = new ItemHargaModel();
        $selectPrices = 'id, nama, jml_min, CAST(harga AS FLOAT) AS harga';

        $perPage      = $this->request->getGet('per_page') ?? 10;
        $keyword      = $this->request->getGet('keyword') ?? null;
        $page         = $this->request->getGet('page') ?? 1; // Allow any page, default to 1
        $categoryId   = $this->request->getGet('CategoryId') ?? null;
        $stok         = $this->request->getGet('stok') ?? null;

        // Get items for the specific page
        $items        = $mItem->getItemsWithRelationsActive($perPage, $keyword, $page, $categoryId, $stok);
        $pager        = $mItem->pager->getDetails('items');

        // Transform the data to match the desired format
        $formattedItems = [];
        foreach ($items as $item) {
            $formattedItems[] = [
                'id'         => (int) $item->id,
                'id_kategori'=> (int) $item->id_kategori,
                'id_merk'    => (int) $item->id_merk,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
                'merk'       => $item->merk,
                'kategori'   => $item->kategori,
                'kode'       => $item->kode,
                'barcode'    => $item->barcode,
                'item'       => $item->item,
                'deskripsi'  => $item->deskripsi,
                'jml_min'    => (int) $item->jml_min,
                'harga_jual' => (float) $item->harga_jual,
                'harga_beli' => (float) $item->harga_beli,
                'foto'       => $item->foto ? base_url($item->foto) : null,
                'options'    => [
                    'harga'  => $mItemHarga->getPricesByItemId($item->id, $selectPrices),
                    'varian' => null,
                    'galeri' => null,
                ],
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
            'created_at' => $item->created_at,
            'updated_at' => $item->updated_at,
            'merk'       => $item->merk,
            'kategori'   => $item->kategori,
            'kode'       => $item->kode,
            'barcode'    => $item->barcode,
            'item'       => $item->item,
            'deskripsi'  => $item->deskripsi,
            'jml_min'    => (int) $item->jml_min,
            'harga_jual' => (float) $item->harga_jual,
            'harga_beli' => (float) $item->harga_beli,
            'foto'       => $item->foto ? base_url($item->foto) : null,
        ];

        return $this->respond($data);
    }
} 