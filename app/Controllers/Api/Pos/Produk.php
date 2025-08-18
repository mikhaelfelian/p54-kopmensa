<?php

namespace App\Controllers\Api\Pos;

use App\Controllers\BaseController;
use App\Models\ItemModel;
use App\Models\ItemHargaModel;
use App\Models\ItemVarianModel;
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
    protected $mItem;
    protected $mItemHarga;
    protected $mItemVarian;
    protected $selectPrices;
    protected $perPage;
    protected $keyword;
    protected $page;

    /**
     * Get a paginated list of active products.
     * Supports search by keyword.
     *
     * @return \CodeIgniter\HTTP\Response
     */

    public function __construct()
    {
        $this->mItem        = new ItemModel();
        $this->mItemHarga   = new ItemHargaModel();
        $this->mItemVarian = new ItemVarianModel();
        $this->selectPrices = 'id, nama, jml_min, CAST(harga AS FLOAT) AS harga';
    }
    public function getAll()
    {

        $perPage      = $this->request->getGet('per_page') ?? 10;
        $keyword      = $this->request->getGet('keyword') ?? null;
        $page         = $this->request->getGet('page') ?? 1; // Allow any page, default to 1
        $categoryId   = $this->request->getGet('CategoryId') ?? $this->request->getGet('id_kategori') ?? null;
        $stok         = $this->request->getGet('stok') ?? null;

        // Get items for the specific page (supports stok filter)
        $items        = $this->mItem->getItemsWithRelations($perPage, $keyword, $page, $categoryId, $stok);
        $pager        = $this->mItem->pager->getDetails('items');

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
                    'harga'  => $this->mItemHarga->getPricesByItemId($item->id, $this->selectPrices),
                    'varian' => $this->mItemVarian->getVariantsWithPrice($item->id),
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
    public function getById($id = null)
    {
        
        $item = $this->mItem->getItemWithRelations($id);

        if (!$item) {
            return $this->failNotFound('Produk dengan ID ' . $id . ' tidak ditemukan.');
        }

        // Format the response to match the documentation
        $data = [
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
                    'harga'  => $this->mItemHarga->getPricesByItemId($item->id, $this->selectPrices),
                    'varian' => $this->mItemVarian->getVariantsWithPrice($item->id),
                ],
        ];

        return $this->respond($data);
    }

    /**
     * Get products by category.
     *
     * Query params:
     * - id_kategori (required): category ID
     * - search (optional): search keyword
     * - limit (optional): limit for pagination
     * - offset (optional): offset for pagination
     *
     * @return \CodeIgniter\HTTP\Response
     */
    public function getByCategory($id = null)
    {
        $selectPrices = 'id, nama, jml_min, CAST(harga AS FLOAT) AS harga';

        // Pagination and filter params (same as getAll)
        $perPage    = $this->request->getGet('per_page') ?? 10;
        $page       = $this->request->getGet('page') ?? 1;
        $keyword    = $this->request->getGet('keyword') ?? null;
        $categoryId = $id ?? $this->request->getGet('id_kategori') ?? null;
        $stok       = $this->request->getGet('stok') ?? null;

        if (!$categoryId) {
            return $this->failValidationErrors('Parameter id_kategori is required');
        }

        // Calculate offset from page number
        $offset = ($page - 1) * $perPage;
        
        // Use the same paginated method as getAll, but filter by category
        $items = $this->mItem->getItemsByCategory($categoryId, $keyword, $perPage, $offset);
        
        // Since getItemsByCategory doesn't use pagination, we need to manually calculate pager info
        // For now, we'll use a simple approach - get total count separately
        $totalItems = $this->mItem->getItemsByCategory($categoryId, $keyword);
        $total = count($totalItems);
        $pageCount = ceil($total / $perPage);
        
        $pager = [
            'total' => $total,
            'perPage' => (int)$perPage,
            'pageCount' => $pageCount,
        ];

        // Format the data as in getAll
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
                    'harga'  => $this->mItemHarga->getPricesByItemId($item->id, $this->selectPrices),
                    'varian' => $this->mItemVarian->getVariantsWithPrice($item->id),
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
     * Get the details of a single variant by its ID.
     *
     * @param int $id The variant ID
     * @return \CodeIgniter\HTTP\Response
     */
    public function getVariant($id = null)
    {
        
        $variant = $this->mItemVarian->find($id);

        if (!$variant) {
            return $this->failNotFound('Varian dengan ID ' . $id . ' tidak ditemukan.');
        }

        // Format the response
        $data = [
            'id'           => (int) $variant->id,
            'id_item'      => (int) $variant->id_item,
            'id_item_harga'=> (int) $variant->id_item_harga,
            'kode'         => $variant->kode,
            'barcode'      => $variant->barcode,
            'varian'       => $variant->varian,
            'harga_beli'   => (float) $variant->harga_beli,
            'harga_dasar'  => (float) $variant->harga_dasar,
            'harga_jual'   => (float) $variant->harga_jual,
            'foto'         => $variant->foto ? base_url($variant->foto) : null,
            'status'       => $variant->status,
            'status_label' => $this->mItemVarian->getStatusLabel($variant->status),
            'created_at'   => $variant->created_at,
            'updated_at'   => $variant->updated_at,
        ];

        return $this->respond($data);
    }
} 