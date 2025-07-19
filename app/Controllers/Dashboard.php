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
    protected $transJualModel;

    public function __construct(){
        $this->itemModel = new \App\Models\ItemModel();
        $this->transJualModel = new \App\Models\TransJualModel();
    }
    public function index()
    {        
        // Ambil 10 produk aktif terbaru untuk dashboard
        $items = $this->itemModel->getItemsWithRelationsActive(6);

        // Get paid transactions data
        $paidTransactions = $this->transJualModel->where('status_bayar', '1')->findAll();
        $totalPaidTransactions = count($paidTransactions);
        
        // Calculate total revenue from paid transactions
        $totalRevenue = 0;
        foreach ($paidTransactions as $transaction) {
            $totalRevenue += $transaction->jml_gtotal ?? 0;
        }

        $data = [
            'title'         => 'Dashboard',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'isMenuActive'  => isMenuActive('dashboard') ? 'active' : '',
            'total_users'   => 1,
            'items'         => $items,
            'paidTransactions' => $paidTransactions,
            'totalPaidTransactions' => $totalPaidTransactions,
            'totalRevenue' => $totalRevenue
        ];

        return view($this->theme->getThemePath() . '/dashboard', $data);
    } 
} 