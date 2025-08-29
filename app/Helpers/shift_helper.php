<?php

if (!function_exists('get_shift_transaction_summary')) {
    /**
     * Get complete summary of transactions for a specific shift
     * 
     * @param int|null $shiftId Shift ID (if null, gets from session)
     * @param array $options Additional options for filtering
     * @return array Summary data with success status
     */
    function get_shift_transaction_summary($shiftId = null, $options = [])
    {
        try {
            // Get shift ID from session if not provided
            if ($shiftId === null) {
                $shiftId = session()->get('kasir_shift');
            }
            
            if (!$shiftId) {
                return [
                    'success' => false, 
                    'message' => 'Shift ID not found in session',
                    'data' => null
                ];
            }
            
            $db = \Config\Database::connect();
            $builder = $db->table('tbl_trans_jual');
            
            // Apply base filter for shift
            $builder->where('id_shift', $shiftId);
            
            // Apply additional filters if provided
            if (isset($options['status'])) {
                $builder->where('status', $options['status']);
            }
            
            if (isset($options['date_from'])) {
                $builder->where('tgl_masuk >=', $options['date_from']);
            }
            
            if (isset($options['date_to'])) {
                $builder->where('tgl_masuk <=', $options['date_to']);
            }
            
            // Get basic count
            $totalTransactions = $builder->countAllResults();
            
            // Reset builder for sum operations
            $builder->resetQuery();
            $builder->where('id_shift', $shiftId);
            
            // Apply same filters for sum
            if (isset($options['status'])) {
                $builder->where('status', $options['status']);
            }
            
            if (isset($options['date_from'])) {
                $builder->where('tgl_masuk >=', $options['date_from']);
            }
            
            if (isset($options['date_to'])) {
                $builder->where('tgl_masuk <=', $options['date_to']);
            }
            
            // Get sum of total amount
            $totalAmount = $builder->selectSum('jml_gtotal')
                                  ->get()
                                  ->getRow()
                                  ->jml_gtotal ?? 0;
            
            // Get sum of subtotal
            $subtotal = $builder->selectSum('jml_subtotal')
                               ->get()
                               ->getRow()
                               ->jml_subtotal ?? 0;
            
            // Get sum of discount
            $totalDiscount = $builder->selectSum('jml_diskon')
                                    ->get()
                                    ->getRow()
                                    ->jml_diskon ?? 0;
            
            // Get sum of PPN
            $totalPPN = $builder->selectSum('jml_ppn')
                                ->get()
                                ->getRow()
                                ->jml_ppn ?? 0;
            
            // Get payment method breakdown
            $paymentMethods = $db->table('tbl_trans_jual_platform')
                                ->select('platform, SUM(nominal) as total_nominal, COUNT(*) as count')
                                ->where('no_nota IN (SELECT no_nota FROM tbl_trans_jual WHERE id_shift = ?)', [$shiftId])
                                ->groupBy('platform')
                                ->get()
                                ->getResultArray();
            
            // Get recent transactions (last 10)
            $recentTransactions = $db->table('tbl_trans_jual')
                                    ->select('no_nota, jml_gtotal, tgl_masuk, status')
                                    ->where('id_shift', $shiftId)
                                    ->orderBy('tgl_masuk', 'DESC')
                                    ->limit(10)
                                    ->get()
                                    ->getResultArray();
            
            return [
                'success' => true,
                'shift_id' => $shiftId,
                'summary' => [
                    'total_transactions' => $totalTransactions,
                    'total_amount' => $totalAmount,
                    'subtotal' => $subtotal,
                    'total_discount' => $totalDiscount,
                    'total_ppn' => $totalPPN,
                    'net_amount' => $totalAmount - $totalDiscount
                ],
                'payment_methods' => $paymentMethods,
                'recent_transactions' => $recentTransactions,
                'generated_at' => date('Y-m-d H:i:s')
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false, 
                'message' => 'Error: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }
}

if (!function_exists('get_shift_transaction_count')) {
    /**
     * Get simple count of transactions for a shift
     * 
     * @param int|null $shiftId Shift ID (if null, gets from session)
     * @return int Transaction count
     */
    function get_shift_transaction_count($shiftId = null)
    {
        try {
            if ($shiftId === null) {
                $shiftId = session()->get('kasir_shift');
            }
            
            if (!$shiftId) {
                return 0;
            }
            
            $db = \Config\Database::connect();
            $builder = $db->table('tbl_trans_jual');
            
            return $builder->where('id_shift', $shiftId)->countAllResults();
            
        } catch (\Exception $e) {
            return 0;
        }
    }
}

if (!function_exists('get_shift_total_amount')) {
    /**
     * Get total amount for a shift
     * 
     * @param int|null $shiftId Shift ID (if null, gets from session)
     * @return float Total amount
     */
    function get_shift_total_amount($shiftId = null)
    {
        try {
            if ($shiftId === null) {
                $shiftId = session()->get('kasir_shift');
            }
            
            if (!$shiftId) {
                return 0;
            }
            
            $db = \Config\Database::connect();
            $builder = $db->table('tbl_trans_jual');
            
            $result = $builder->selectSum('jml_gtotal')
                             ->where('id_shift', $shiftId)
                             ->get()
                             ->getRow();
            
            return $result->jml_gtotal ?? 0;
            
        } catch (\Exception $e) {
            return 0;
        }
    }
}

if (!function_exists('format_shift_summary')) {
    /**
     * Format shift summary for display
     * 
     * @param array $summary Summary data from get_shift_transaction_summary
     * @return string Formatted HTML
     */
    function format_shift_summary($summary)
    {
        if (!$summary['success']) {
            return '<div class="alert alert-warning">' . $summary['message'] . '</div>';
        }
        
        $data = $summary['summary'];
        
        $html = '<div class="shift-summary">';
        $html .= '<div class="row">';
        
        // Transaction count
        $html .= '<div class="col-md-3">';
        $html .= '<div class="info-box bg-info">';
        $html .= '<span class="info-box-icon"><i class="fas fa-shopping-cart"></i></span>';
        $html .= '<div class="info-box-content">';
        $html .= '<span class="info-box-text">Total Transaksi</span>';
        $html .= '<span class="info-box-number">' . number_format($data['total_transactions']) . '</span>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        
        // Total amount
        $html .= '<div class="col-md-3">';
        $html .= '<div class="info-box bg-success">';
        $html .= '<span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>';
        $html .= '<div class="info-box-content">';
        $html .= '<span class="info-box-text">Total Pendapatan</span>';
        $html .= '<span class="info-box-number">' . format_angka($data['total_amount'], 0) . '</span>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        
        // Discount
        $html .= '<div class="col-md-3">';
        $html .= '<div class="info-box bg-warning">';
        $html .= '<span class="info-box-icon"><i class="fas fa-tags"></i></span>';
        $html .= '<div class="info-box-content">';
        $html .= '<span class="info-box-text">Total Diskon</span>';
        $html .= '<span class="info-box-number">' . format_angka($data['total_discount'], 0) . '</span>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        
        // Net amount
        $html .= '<div class="col-md-3">';
        $html .= '<div class="info-box bg-primary">';
        $html .= '<span class="info-box-icon"><i class="fas fa-calculator"></i></span>';
        $html .= '<div class="info-box-content">';
        $html .= '<span class="info-box-text">Pendapatan Bersih</span>';
        $html .= '<span class="info-box-number">' . format_angka($data['net_amount'], 0) . '</span>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
}
