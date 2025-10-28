<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-18
 * Github: github.com/mikhaelfelian
 * Description: Model for handling sales transactions (tbl_trans_jual)
 * This file represents the Model.
 */

namespace App\Models;

use CodeIgniter\Model;

class TransJualModel extends Model
{
    protected $table            = 'tbl_trans_jual';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_user',
        'id_sales',
        'id_pelanggan',
        'id_gudang',
        'id_shift',
        'no_nota',
        'created_at',
        'updated_at',
        'deleted_at',
        'tgl_bayar',
        'tgl_masuk',
        'tgl_keluar',
        'jml_total',
        'jml_biaya',
        'jml_ongkir',
        'jml_retur',
        'diskon',
        'jml_diskon',
        'jml_subtotal',
        'ppn',
        'jml_ppn',
        'jml_gtotal',
        'jml_bayar',
        'jml_kembali',
        'jml_kurang',
        'disk1',
        'jml_disk1',
        'disk2',
        'jml_disk2',
        'disk3',
        'jml_disk3',
        'metode_bayar',
        'voucher_code',
        'voucher_discount',
        'voucher_id',
        'voucher_type',
        'voucher_discount_amount',
        'qr_scanned',
        'qr_scan_time',
        'status',
        'status_nota',
        'status_ppn',
        'status_bayar',
        'status_retur'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'no_nota' => 'required|max_length[50]',
        'id_user' => 'permit_empty|integer',
        'id_sales' => 'permit_empty|integer',
        'id_pelanggan' => 'permit_empty|integer',
        'id_gudang' => 'permit_empty|integer'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Generate invoice number in SAP style (e.g., 6000004567)
     * 10 digits, starts with '6', incrementing number per day
     * Example: 6000000001, 6000000002, etc.
     * 
     * @return string
     */
    /**
     * Generate invoice number in SAP style:
     * Format: 6 + YYMMDD + 4 digit sequence (e.g. 62406190001)
     *  - 6: static prefix
     *  - YYMMDD: year, month, day (2 digit each)
     *  - 4 digit: running number per day (0001, 0002, ...)
     */
    public function generateKode()
    {
        // Get today's date in yymmdd format (6 digits)
        $datePart = date('ymd');
        $prefix = $datePart;

        // Find the last transaction for today with this 6 digit prefix
        $last = $this->where('no_nota LIKE', $prefix . '%')
                     ->orderBy('no_nota', 'DESC')
                     ->first();

        if ($last && preg_match('/^(\d{6})(\d{2})$/', $last->no_nota, $matches)) {
            $lastNumber = (int)$matches[2];
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        // Pad to 2 digits (so no_nota is always 8 digits: 6 date + 2 running)
        $kode = $prefix . str_pad($newNumber, 2, '0', STR_PAD_LEFT);
        return $kode;
    }

    /**
     * Get sales transaction by ID
     */
    public function getTransactionById($id)
    {
        return $this->find($id);
    }

    /**
     * Get sales transactions by customer ID
     */
    public function getTransactionsByCustomer($customerId)
    {
        return $this->where('id_pelanggan', $customerId)->findAll();
    }

    /**
     * Get sales transactions by date range
     */
    public function getTransactionsByDateRange($startDate, $endDate)
    {
        return $this->where('created_at >=', $startDate)
                    ->where('created_at <=', $endDate)
                    ->findAll();
    }

    /**
     * Get transactions by nota number
     */
    public function getTransactionByNota($noNota)
    {
        return $this->where('no_nota', $noNota)->first();
    }

    /**
     * Get total sales by date
     */
    public function getTotalSalesByDate($date)
    {
        return $this->selectSum('jml_gtotal')
                    ->where('DATE(created_at)', $date)
                    ->where('status', '1')
                    ->first();
    }

    /**
     * Get last 5 transactions with customer and user information
     */
    public function getLastTransactions($limit = 5)
    {
        return $this->select('
                tbl_trans_jual.*,
                tbl_m_pelanggan.nama as customer_name,
                tbl_ion_users.first_name as user_name
            ')
            ->join('tbl_m_pelanggan', 'tbl_m_pelanggan.id = tbl_trans_jual.id_pelanggan', 'left')
            ->join('tbl_ion_users', 'tbl_ion_users.id = tbl_trans_jual.id_user', 'left')
            ->orderBy('tbl_trans_jual.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get sales summary by shift with payment method breakdown
     */
    public function getSalesSummaryByShift($shift_id)
    {
        // Count transactions and sum sales for the given shift_id
        $builder = $this->db->table('tbl_trans_jual');
        $builder->select([
            'COUNT(id) as total_transactions',
            'SUM(CASE WHEN metode_bayar = "cash" THEN jml_gtotal ELSE 0 END) as total_cash_sales',
            'SUM(CASE WHEN metode_bayar != "cash" THEN jml_gtotal ELSE 0 END) as total_non_cash_sales',
            'SUM(jml_gtotal) as total_sales'
        ]);
        $builder->where('id_shift', $shift_id);
        $builder->where('status', '1'); // Only finalized/valid transactions

        $result = $builder->get()->getRowArray();

        // Get detailed payment method breakdown
        $paymentMethods = $this->getPaymentMethodBreakdownByShift($shift_id);

        // Ensure all keys exist and are numeric
        return [
            'total_transactions'   => (int)($result['total_transactions'] ?? 0),
            'total_cash_sales'     => (float)($result['total_cash_sales'] ?? 0),
            'total_non_cash_sales' => (float)($result['total_non_cash_sales'] ?? 0),
            'total_sales'          => (float)($result['total_sales'] ?? 0),
            'payment_methods'      => $paymentMethods
        ];
    }

    /**
     * Get payment method breakdown by shift
     */
    public function getPaymentMethodBreakdownByShift($shift_id)
    {
        $builder = $this->db->table('tbl_trans_jual_platform tjp');
        $builder->select([
            'tjp.platform as payment_method',
            'SUM(tjp.nominal) as total_amount',
            'COUNT(tjp.id) as transaction_count'
        ]);
        $builder->join('tbl_trans_jual tj', 'tj.id = tjp.id_penjualan', 'inner');
        $builder->where('tj.id_shift', $shift_id);
        $builder->where('tj.status', '1'); // Only finalized transactions
        $builder->groupBy('tjp.platform');
        $builder->orderBy('total_amount', 'DESC');

        $result = $builder->get()->getResultArray();

        // Format the result
        $paymentMethods = [];
        foreach ($result as $row) {
            $paymentMethods[] = [
                'method' => $row['payment_method'],
                'amount' => (float)$row['total_amount'],
                'count' => (int)$row['transaction_count']
            ];
        }

        return $paymentMethods;
    }

    /**
     * Get sales transactions by shift (placeholder - shift_id not yet implemented in sales table)
     */
    public function getSalesByShift($shift_id)
    {
        // TODO: Add shift_id field to tbl_trans_jual table
        // For now, return empty array
        return [];
    }
}