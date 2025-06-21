<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2024-07-15
 * Github : github.com/mikhaelfelian
 * description : Controller for managing inventory.
 * This file represents the Inventori controller.
 */

namespace App\Controllers\Gudang;

use App\Controllers\BaseController;
use App\Models\ItemModel;

class Inventori extends BaseController
{
    protected $itemModel;

    public function __construct()
    {
        parent::__construct();
        $this->itemModel = new ItemModel();
    }

    public function index()
    {
        $currentPage = $this->request->getVar('page_items') ?? 1;
        $perPage = 10;
        $keyword = $this->request->getVar('keyword');

        $data = [
            'title'       => 'Data Inventori',
            'Pengaturan'  => $this->pengaturan,
            'user'        => $this->ionAuth->user()->row(),
            'items'       => $this->itemModel->itemStockable($perPage, $keyword),
            'pager'       => $this->itemModel->pager,
            'currentPage' => $currentPage,
            'perPage'     => $perPage,
            'keyword'     => $keyword,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item">Gudang</li>
                <li class="breadcrumb-item active">Inventori</li>
            '
        ];

        return view($this->theme->getThemePath() . '/gudang/inventori/index', $data);
    }

    public function detail($id)
    {
        $item = $this->itemModel->find($id);

        if (!$item) {
            return redirect()->to(base_url('gudang/stok'))->with('error', 'Item tidak ditemukan.');
        }

        // TODO: Fetch real stock data from a new model
        $stokData = []; 

        $data = [
            'title'       => 'Detail Stok Item: ' . $item->item,
            'Pengaturan'  => $this->pengaturan,
            'user'        => $this->ionAuth->user()->row(),
            'item'        => $item,
            'stokData'    => $stokData,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('gudang/stok') . '">Inventori</a></li>
                <li class="breadcrumb-item active">Detail Stok</li>
            '
        ];

        return view($this->theme->getThemePath() . '/gudang/inventori/detail', $data);
    }
} 