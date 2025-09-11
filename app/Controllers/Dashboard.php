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
    protected $transJualDetModel;
    protected $db;

    public function __construct(){
        $this->itemModel = new \App\Models\ItemModel();
        $this->transJualModel = new \App\Models\TransJualModel();
        $this->transBeliModel = new \App\Models\TransBeliModel();
        $this->transJualDetModel = new \App\Models\TransJualDetModel();
        $this->db = \Config\Database::connect();
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

        // === ENHANCED ANALYTICS ===
        
        // Monthly Sales Data for Chart (last 12 months)
        $monthlySalesData = $this->getMonthlySalesData();
        
        // Daily Sales Data for Current Month
        $dailySalesData = $this->getDailySalesData();
        
        // Sales by Category
        $salesByCategory = $this->getSalesByCategory();
        
        // Top Selling Products
        $topSellingProducts = $this->getTopSellingProducts(5);
        
        // Sales Performance Metrics
        $currentMonth = date('Y-m');
        $previousMonth = date('Y-m', strtotime('-1 month'));
        
        $currentMonthSales = $this->getMonthSales($currentMonth);
        $previousMonthSales = $this->getMonthSales($previousMonth);
        
        $salesGrowth = $previousMonthSales > 0 ? 
            (($currentMonthSales - $previousMonthSales) / $previousMonthSales) * 100 : 0;
        
        // Average Order Value
        $avgOrderValue = $totalPaidSalesTransactions > 0 ? 
            $totalRevenue / $totalPaidSalesTransactions : 0;
        
        // Sales Targets (you can make these configurable)
        $monthlyTarget = 50000000; // 50 juta
        $dailyTarget = $monthlyTarget / date('t'); // target per hari
        $todaySales = $this->getTodaySales();
        
        $monthlyProgress = ($currentMonthSales / $monthlyTarget) * 100;
        $dailyProgress = ($todaySales / $dailyTarget) * 100;
        
        // Customer Analytics
        $totalCustomers = $this->db->table('tbl_trans_jual')
            ->where('status_bayar', '1')
            ->where('id_pelanggan IS NOT NULL')
            ->countAllResults();
        
        $newCustomersThisMonth = $this->db->table('tbl_trans_jual')
            ->where('status_bayar', '1')
            ->where('id_pelanggan IS NOT NULL')
            ->where('DATE_FORMAT(tgl_masuk, "%Y-%m")', $currentMonth)
            ->countAllResults();

        // Calculate additional dashboard metrics
        $totalStock = $this->itemModel->countAllResults(); // Total items in stock
        $totalLikes = $newCustomersThisMonth; // Use new customers as likes
        $totalMentions = $totalPaidSalesTransactions; // Use total transactions as mentions
        $totalDownloads = count($topSellingProducts); // Use top products count
        $totalDirectMessages = $totalCustomers; // Use total customers as messages

        $data = [
            'title'         => 'Dashboard',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'isMenuActive'  => isMenuActive('dashboard') ? 'active' : '',
            'total_users'   => 1,
            'items'         => $items,
            
            // Basic metrics
            'paidSalesTransactions' => $paidSalesTransactions,
            'totalPaidSalesTransactions' => $totalPaidSalesTransactions,
            'totalRevenue' => $totalRevenue,
            'paidPurchaseTransactions' => $paidPurchaseTransactions,
            'totalPaidPurchaseTransactions' => $totalPaidPurchaseTransactions,
            'totalExpenses' => $totalExpenses,
            'totalProfit' => $totalProfit,
            'recentSalesTransactions' => $recentSalesTransactions,
            'recentPurchaseTransactions' => $recentPurchaseTransactions,
            
            // Enhanced analytics
            'monthlySalesData' => $monthlySalesData,
            'dailySalesData' => $dailySalesData,
            'salesByCategory' => $salesByCategory,
            'topSellingProducts' => $topSellingProducts,
            'currentMonthSales' => $currentMonthSales,
            'previousMonthSales' => $previousMonthSales,
            'salesGrowth' => $salesGrowth,
            'avgOrderValue' => $avgOrderValue,
            'monthlyTarget' => $monthlyTarget,
            'dailyTarget' => $dailyTarget,
            'todaySales' => $todaySales,
            'monthlyProgress' => $monthlyProgress,
            'dailyProgress' => $dailyProgress,
            'totalCustomers' => $totalCustomers,
            'newCustomersThisMonth' => $newCustomersThisMonth,
            
            'totalStock' => $totalStock,
            'totalLikes' => $totalLikes,
            'totalMentions' => $totalMentions,
            'totalDownloads' => $totalDownloads,
            'totalDirectMessages' => $totalDirectMessages
        ];

        return view($this->theme->getThemePath() . '/dashboard', $data);
    }
    
    /**
     * Get monthly sales data for the last 12 months
     */
    private function getMonthlySalesData()
    {
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $monthName = date('M Y', strtotime("-$i months"));
            
            $sales = $this->db->table('tbl_trans_jual')
                ->select('SUM(jml_gtotal) as total, COUNT(*) as count')
                ->where('status_bayar', '1')
                ->where('DATE_FORMAT(tgl_masuk, "%Y-%m")', $month)
                ->get()
                ->getRow();
            
            $data[] = [
                'month' => $monthName,
                'total' => $sales->total ?? 0,
                'count' => $sales->count ?? 0
            ];
        }
        return $data;
    }
    
    /**
     * Get daily sales data for current month
     */
    private function getDailySalesData()
    {
        $currentMonth = date('Y-m');
        $daysInMonth = date('t');
        $data = [];
        
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = $currentMonth . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
            
            $sales = $this->db->table('tbl_trans_jual')
                ->select('SUM(jml_gtotal) as total, COUNT(*) as count')
                ->where('status_bayar', '1')
                ->where('tgl_masuk', $date)
                ->get()
                ->getRow();
            
            $data[] = [
                'day' => $day,
                'total' => $sales->total ?? 0,
                'count' => $sales->count ?? 0
            ];
        }
        return $data;
    }
    
    /**
     * Get sales by category
     */
    private function getSalesByCategory()
    {
        $query = $this->db->query("
            SELECT 
                COALESCE(mk.kategori, 'Tanpa Kategori') as kategori,
                SUM(tjd.subtotal) as total_sales,
                COUNT(tjd.id) as total_items
            FROM tbl_trans_jual_det tjd
            LEFT JOIN tbl_trans_jual tj ON tjd.id_penjualan = tj.id
            LEFT JOIN tbl_m_kategori mk ON tjd.id_kategori = mk.id
            WHERE tj.status_bayar = '1'
            GROUP BY tjd.id_kategori, mk.kategori
            ORDER BY total_sales DESC
            LIMIT 5
        ");
        
        return $query->getResult();
    }
    
    /**
     * Get top selling products
     */
    private function getTopSellingProducts($limit = 5)
    {
        $query = $this->db->query("
            SELECT 
                tjd.produk,
                SUM(tjd.jml) as total_qty,
                SUM(tjd.subtotal) as total_sales,
                COUNT(tjd.id) as transactions
            FROM tbl_trans_jual_det tjd
            LEFT JOIN tbl_trans_jual tj ON tjd.id_penjualan = tj.id
            WHERE tj.status_bayar = '1'
            GROUP BY tjd.produk
            ORDER BY total_qty DESC
            LIMIT $limit
        ");
        
        return $query->getResult();
    }
    
    /**
     * Get sales for specific month
     */
    private function getMonthSales($month)
    {
        $result = $this->db->table('tbl_trans_jual')
            ->select('SUM(jml_gtotal) as total')
            ->where('status_bayar', '1')
            ->where('DATE_FORMAT(tgl_masuk, "%Y-%m")', $month)
            ->get()
            ->getRow();
        
        return $result->total ?? 0;
    }
    
    /**
     * Get today's sales
     */
    private function getTodaySales()
    {
        $today = date('Y-m-d');
        $result = $this->db->table('tbl_trans_jual')
            ->select('SUM(jml_gtotal) as total')
            ->where('status_bayar', '1')
            ->where('tgl_masuk', $today)
            ->get()
            ->getRow();
        
        return $result->total ?? 0;
    }

    /**
     * Enhanced Features Dashboard
     * Shows all 21 implemented features from the original request
     */
    public function enhancedFeatures()
    {
        $data = [
            'title' => 'Enhanced POS System Features',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item active">Enhanced Features</li>
            '
        ];

        return view($this->theme->getThemePath() . '/dashboard/enhanced_menu', $data);
    }

    /**
     * System Overview Dashboard
     * Comprehensive overview of all implemented features
     */
    public function systemOverview()
    {
        $data = [
            'title' => 'System Overview - All Features',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item active">System Overview</li>
            '
        ];

        return view($this->theme->getThemePath() . '/dashboard/system_overview', $data);
    }
} 