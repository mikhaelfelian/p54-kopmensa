<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        
        $data = [
            'title'         => 'Dashboard',
            'Pengaturan'    => $this->pengaturan,
            'total_users'   => 1
        ];

        return view($this->theme->getThemePath() . '/dashboard', $data);
    }

    public function items()
    {
        $kategori = $this->kategoriModel->where('status', '1')->findAll();
        $merk     = $this->merkModel->where('status', '1')->findAll();

        // Mendapatkan filter dari URL
        $filters = [
            'kategori' => $this->request->getGet('filter_kategori'),
            'merk'     => $this->request->getGet('filter_merk'),
            'item'     => $this->request->getGet('filter_item'),
            'harga'    => $this->request->getGet('filter_harga')
        ];

        // Konfigurasi paginasi
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 10;

        // Mendapatkan total baris untuk paginasi
        $total = $this->itemModel->getStockable($filters, true);

        // Mendapatkan data yang dipaginasi
        $items = $this->itemModel->getStockable($filters, false, $perPage, ($page - 1) * $perPage);

        // Membuat pager
        $pager = service('pager');
        $pager->setPath('stock/items');
        $pager->makeLinks($page, $perPage, $total, 'default_full');

        $data = [
            'title'          => 'Data Item Stok',
            'kategoris'      => $kategori,
            'merks'          => $merk,
            'items'          => $items,
            'pager'          => $pager,
            'Pengaturan'     => $this->pengaturan,
            'user'           => $this->ionAuth->user()->row(),
            'itemStockModel' => $this->itemStokModel,
        ];
            
        return $this->view($this->theme->getThemePath() . '/gudang/item/index', $data);
    }
}
