<?php
/**
 * Dashboard Controller
 * 
 * Created by Mikhael Felian Waskito
 * Created at 2024-01-09
 */

namespace App\Controllers;

class Dashboard extends BaseController
{
    protected $medTransModel;

    public function __construct(){
        $this->itemModel = new \App\Models\ItemModel();
    }
    public function index()
    {        
        // Ambil 10 produk aktif terbaru untuk dashboard
        $items = $this->itemModel->getItemsWithRelationsActive(6);

        $data = [
            'title'         => 'Dashboard',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'isMenuActive'  => isMenuActive('dashboard') ? 'active' : '',
            'total_users'   => 1,
            'items'         => $items
        ];

        return view($this->theme->getThemePath() . '/dashboard', $data);
    } 
} 