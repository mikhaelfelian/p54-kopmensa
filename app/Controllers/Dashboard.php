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
    protected $transBeliModel;

    public function __construct(){
        $this->itemModel = new \App\Models\ItemModel();
        $this->transJualModel = new \App\Models\TransJualModel();
        $this->transBeliModel = new \App\Models\TransBeliModel();
    }
    public function index()
    {        
        // Ambil 10 produk aktif terbaru untuk dashboard
        $items = $this->itemModel->getItemsWithRelationsActive(6);

        // Get paid sales transactions data
        $paidSalesTransactions = $this->transJualModel->where('status_bayar', '1')->findAll();
        $totalPaidSalesTransactions = count($paidSalesTransactions);
        
        // Calculate total revenue from paid sales transactions
        $totalRevenue = 0;
        foreach ($paidSalesTransactions as $transaction) {
            $totalRevenue += $transaction->jml_gtotal ?? 0;
        }

        // Get paid purchase transactions data
        $paidPurchaseTransactions = $this->transBeliModel->where('status_bayar', '1')->findAll();
        $totalPaidPurchaseTransactions = count($paidPurchaseTransactions);
        
        // Calculate total expenses from paid purchase transactions
        $totalExpenses = 0;
        foreach ($paidPurchaseTransactions as $transaction) {
            $totalExpenses += $transaction->jml_gtotal ?? 0;
        }

        // Calculate profit (revenue - expenses)
        $totalProfit = $totalRevenue - $totalExpenses;

        // Get recent transactions (both sales and purchases)
        $recentSalesTransactions = $this->transJualModel->where('status_bayar', '1')
                                                       ->orderBy('created_at', 'DESC')
                                                       ->limit(5)
                                                       ->findAll();
        
        $recentPurchaseTransactions = $this->transBeliModel->where('status_bayar', '1')
                                                          ->orderBy('created_at', 'DESC')
                                                          ->limit(5)
                                                          ->findAll();

        // Calculate additional dashboard metrics
        $totalStock = $this->itemModel->countAllResults(); // Total items in stock
        $totalLikes = 0; // Placeholder - can be connected to actual like system
        $totalMentions = 0; // Placeholder - can be connected to actual mention system
        $totalDownloads = 0; // Placeholder - can be connected to actual download system
        $totalDirectMessages = 0; // Placeholder - can be connected to actual message system

        $data = [
            'title'         => 'Dashboard',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'isMenuActive'  => isMenuActive('dashboard') ? 'active' : '',
            'total_users'   => 1,
            'items'         => $items,
            'paidSalesTransactions' => $paidSalesTransactions,
            'totalPaidSalesTransactions' => $totalPaidSalesTransactions,
            'totalRevenue' => $totalRevenue,
            'paidPurchaseTransactions' => $paidPurchaseTransactions,
            'totalPaidPurchaseTransactions' => $totalPaidPurchaseTransactions,
            'totalExpenses' => $totalExpenses,
            'totalProfit' => $totalProfit,
            'recentSalesTransactions' => $recentSalesTransactions,
            'recentPurchaseTransactions' => $recentPurchaseTransactions,
            'totalStock' => $totalStock,
            'totalLikes' => $totalLikes,
            'totalMentions' => $totalMentions,
            'totalDownloads' => $totalDownloads,
            'totalDirectMessages' => $totalDirectMessages
        ];

        return view($this->theme->getThemePath() . '/dashboard', $data);
    } 
} 