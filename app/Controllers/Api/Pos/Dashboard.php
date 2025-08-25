<?php
/**
 * Dashboard API Controller for POS Users
 * 
 * Created by Mikhael Felian Waskito
 * Created at 2025-01-18
 * Based on web Dashboard.php
 */

namespace App\Controllers\Api\Pos;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class Dashboard extends BaseController
{
    use ResponseTrait;

    protected $medTransModel;
    protected $transJualModel;
    protected $transBeliModel;
    protected $transJualDetModel;
    protected $db;

    public function __construct()
    {
        $this->itemModel = new \App\Models\ItemModel();
        $this->transJualModel = new \App\Models\TransJualModel();
        $this->transBeliModel = new \App\Models\TransBeliModel();
        $this->transJualDetModel = new \App\Models\TransJualDetModel();
        $this->db = \Config\Database::connect();
    }
    
    /**
     * Get main dashboard data
     */
    public function index()
    {        
        try {
            // Get basic metrics
            $basicMetrics = $this->getBasicMetrics();
            
            // Get enhanced analytics
            $analytics = $this->getEnhancedAnalytics();
            
            // Get recent transactions
            $recentData = $this->getRecentData();
            
            // Get performance metrics
            $performance = $this->getPerformanceMetrics();
            
            $data = [
                'success' => true,
                'message' => 'Dashboard data retrieved successfully',
                'data' => [
                    'basic_metrics' => $basicMetrics,
                    'analytics' => $analytics,
                    'recent_data' => $recentData,
                    'performance' => $performance
                ]
            ];

            return $this->respond($data, 200);

        } catch (\Exception $e) {
            return $this->respond([
                'success' => false,
                'message' => 'Failed to retrieve dashboard data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get basic dashboard metrics
     */
    public function basicMetrics()
    {
        try {
            $metrics = $this->getBasicMetrics();
            
            return $this->respond([
                'success' => true,
                'message' => 'Basic metrics retrieved successfully',
                'data' => $metrics
            ], 200);

        } catch (\Exception $e) {
            return $this->respond([
                'success' => false,
                'message' => 'Failed to retrieve basic metrics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sales analytics data
     */
    public function salesAnalytics()
    {
        try {
            $analytics = $this->getEnhancedAnalytics();
            
            return $this->respond([
                'success' => true,
                'message' => 'Sales analytics retrieved successfully',
                'data' => $analytics
            ], 200);

        } catch (\Exception $e) {
            return $this->respond([
                'success' => false,
                'message' => 'Failed to retrieve sales analytics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent transactions data
     */
    public function recentTransactions()
    {
        try {
            $recentData = $this->getRecentData();
            
            return $this->respond([
                'success' => true,
                'message' => 'Recent transactions retrieved successfully',
                'data' => $recentData
            ], 200);

        } catch (\Exception $e) {
            return $this->respond([
                'success' => false,
                'message' => 'Failed to retrieve recent transactions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get performance metrics
     */
    public function performanceMetrics()
    {
        try {
            $performance = $this->getPerformanceMetrics();
            
            return $this->respond([
                'success' => true,
                'message' => 'Performance metrics retrieved successfully',
                'data' => $performance
            ], 200);

        } catch (\Exception $e) {
            return $this->respond([
                'success' => false,
                'message' => 'Failed to retrieve performance metrics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get monthly sales data for charts
     */
    public function monthlySales()
    {
        try {
            $monthlyData = $this->getMonthlySalesData();
            
            return $this->respond([
                'success' => true,
                'message' => 'Monthly sales data retrieved successfully',
                'data' => $monthlyData
            ], 200);

        } catch (\Exception $e) {
            return $this->respond([
                'success' => false,
                'message' => 'Failed to retrieve monthly sales data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get daily sales data for current month
     */
    public function dailySales()
    {
        try {
            $dailyData = $this->getDailySalesData();
            
            return $this->respond([
                'success' => true,
                'message' => 'Daily sales data retrieved successfully',
                'data' => $dailyData
            ], 200);

        } catch (\Exception $e) {
            return $this->respond([
                'success' => false,
                'message' => 'Failed to retrieve daily sales data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sales by category
     */
    public function salesByCategory()
    {
        try {
            $categoryData = $this->getSalesByCategory();
            
            return $this->respond([
                'success' => true,
                'message' => 'Sales by category retrieved successfully',
                'data' => $categoryData
            ], 200);

        } catch (\Exception $e) {
            return $this->respond([
                'success' => false,
                'message' => 'Failed to retrieve sales by category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get top selling products
     */
    public function topProducts($limit = 5)
    {
        try {
            $topProducts = $this->getTopSellingProducts($limit);
            
            return $this->respond([
                'success' => true,
                'message' => 'Top selling products retrieved successfully',
                'data' => $topProducts
            ], 200);

        } catch (\Exception $e) {
            return $this->respond([
                'success' => false,
                'message' => 'Failed to retrieve top selling products: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get basic metrics
     */
    private function getBasicMetrics()
    {
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

        // Get total stock count
        $totalStock = $this->itemModel->countAllResults();

        return [
            'total_sales_transactions' => $totalPaidSalesTransactions,
            'total_revenue' => $totalRevenue,
            'total_purchase_transactions' => $totalPaidPurchaseTransactions,
            'total_expenses' => $totalExpenses,
            'total_profit' => $totalProfit,
            'total_stock' => $totalStock
        ];
    }

    /**
     * Get enhanced analytics
     */
    private function getEnhancedAnalytics()
    {
        // Monthly Sales Data for Chart (last 12 months)
        $monthlySalesData = $this->getMonthlySalesData();
        
        // Daily Sales Data for Current Month
        $dailySalesData = $this->getDailySalesData();
        
        // Sales by Category
        $salesByCategory = $this->getSalesByCategory();
        
        // Top Selling Products
        $topSellingProducts = $this->getTopSellingProducts(5);

        return [
            'monthly_sales' => $monthlySalesData,
            'daily_sales' => $dailySalesData,
            'sales_by_category' => $salesByCategory,
            'top_selling_products' => $topSellingProducts
        ];
    }

    /**
     * Get recent data
     */
    private function getRecentData()
    {
        // Get recent transactions (both sales and purchases)
        $recentSalesTransactions = $this->transJualModel->where('status_bayar', '1')
                                                       ->orderBy('created_at', 'DESC')
                                                       ->limit(5)
                                                       ->findAll();
        
        $recentPurchaseTransactions = $this->transBeliModel->where('status_bayar', '1')
                                                          ->orderBy('created_at', 'DESC')
                                                          ->limit(5)
                                                          ->findAll();

        // Get 6 latest active products
        $items = $this->itemModel->getItemsWithRelationsActive(6);

        return [
            'recent_sales' => $recentSalesTransactions,
            'recent_purchases' => $recentPurchaseTransactions,
            'latest_products' => $items
        ];
    }

    /**
     * Get performance metrics
     */
    private function getPerformanceMetrics()
    {
        $currentMonth = date('Y-m');
        $previousMonth = date('Y-m', strtotime('-1 month'));
        
        $currentMonthSales = $this->getMonthSales($currentMonth);
        $previousMonthSales = $this->getMonthSales($previousMonth);
        
        $salesGrowth = $previousMonthSales > 0 ? 
            (($currentMonthSales - $previousMonthSales) / $previousMonthSales) * 100 : 0;
        
        // Get total revenue for average calculation
        $paidSalesTransactions = $this->transJualModel->where('status_bayar', '1')->findAll();
        $totalRevenue = 0;
        foreach ($paidSalesTransactions as $transaction) {
            $totalRevenue += $transaction->jml_gtotal ?? 0;
        }
        
        // Average Order Value
        $avgOrderValue = count($paidSalesTransactions) > 0 ? 
            $totalRevenue / count($paidSalesTransactions) : 0;
        
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

        return [
            'current_month_sales' => $currentMonthSales,
            'previous_month_sales' => $previousMonthSales,
            'sales_growth_percentage' => round($salesGrowth, 2),
            'average_order_value' => $avgOrderValue,
            'monthly_target' => $monthlyTarget,
            'daily_target' => $dailyTarget,
            'today_sales' => $todaySales,
            'monthly_progress_percentage' => round($monthlyProgress, 2),
            'daily_progress_percentage' => round($dailyProgress, 2),
            'total_customers' => $totalCustomers,
            'new_customers_this_month' => $newCustomersThisMonth
        ];
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
                'month_code' => $month,
                'total_sales' => (float)($sales->total ?? 0),
                'transaction_count' => (int)($sales->count ?? 0)
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
                'date' => $date,
                'total_sales' => (float)($sales->total ?? 0),
                'transaction_count' => (int)($sales->count ?? 0)
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
                COALESCE(mk.id, 0) as category_id,
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
        
        $results = $query->getResult();
        
        // Convert to array and format data
        $formattedResults = [];
        foreach ($results as $result) {
            $formattedResults[] = [
                'category_id' => (int)$result->category_id,
                'category_name' => $result->kategori,
                'total_sales' => (float)$result->total_sales,
                'total_items' => (int)$result->total_items
            ];
        }
        
        return $formattedResults;
    }
    
    /**
     * Get top selling products
     */
    private function getTopSellingProducts($limit = 5)
    {
        $query = $this->db->query("
            SELECT 
                tjd.produk,
                tjd.id_item,
                SUM(tjd.jml) as total_qty,
                SUM(tjd.subtotal) as total_sales,
                COUNT(tjd.id) as transactions
            FROM tbl_trans_jual_det tjd
            LEFT JOIN tbl_trans_jual tj ON tjd.id_penjualan = tj.id
            WHERE tj.status_bayar = '1'
            GROUP BY tjd.produk, tjd.id_item
            ORDER BY total_qty DESC
            LIMIT $limit
        ");
        
        $results = $query->getResult();
        
        // Convert to array and format data
        $formattedResults = [];
        foreach ($results as $result) {
            $formattedResults[] = [
                'product_id' => (int)$result->id_item,
                'product_name' => $result->produk,
                'total_quantity' => (int)$result->total_qty,
                'total_sales' => (float)$result->total_sales,
                'transaction_count' => (int)$result->transactions
            ];
        }
        
        return $formattedResults;
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
        
        return (float)($result->total ?? 0);
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
        
        return (float)($result->total ?? 0);
    }
}
