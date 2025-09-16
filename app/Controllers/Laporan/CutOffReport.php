<?php

namespace App\Controllers\Laporan;

use App\Controllers\BaseController;

class CutOffReport extends BaseController
{
    protected $cutOffModel;
    protected $transJualModel;
    protected $transBeliModel;
    protected $shiftModel;
    protected $db;
    protected $ionAuth;
    protected $pengaturan;

    public function __construct()
    {
        $this->transJualModel = new \App\Models\TransJualModel();
        $this->transBeliModel = new \App\Models\TransBeliModel();
        $this->shiftModel = new \App\Models\ShiftModel();
        $this->db = \Config\Database::connect();
        $this->ionAuth = new \IonAuth\Libraries\IonAuth();
        $this->pengaturan = new \App\Models\PengaturanModel();
    }

    public function index()
    {
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?: date('Y-m-d');
        $outlet = $this->request->getGet('outlet');

        try {
            // First, let's get the structure of your view to see available columns
            $testQuery = $this->db->table('v_trans_jual_cutoff')->limit(1)->get();
            $columns = array_keys((array)$testQuery->getRow());
            
            // Determine which date field to use
            $dateField = 'created_at'; // default
            if (in_array('tgl_masuk', $columns)) {
                $dateField = 'tgl_masuk';
            } elseif (in_array('created_at', $columns)) {
                $dateField = 'created_at';
            } elseif (in_array('tgl_bayar', $columns)) {
                $dateField = 'tgl_bayar';
            }
            
            // Determine which ID field to use
            $idField = 'id'; // default
            if (in_array('id', $columns)) {
                $idField = 'id';
            } elseif (in_array('id_trans_jual', $columns)) {
                $idField = 'id_trans_jual';
            } elseif (in_array('trans_id', $columns)) {
                $idField = 'trans_id';
            }
            
            // Get cut-off data from your existing view
            $builder = $this->db->table('v_trans_jual_cutoff');
            $builder->select('*');
            
            // Apply date filter
            $builder->where("DATE($dateField) >=", $startDate)
                   ->where("DATE($dateField) <=", $endDate);

            if ($outlet && in_array('id_gudang', $columns)) {
                $builder->where('id_gudang', $outlet);
            }

            // Apply ordering
            $builder->orderBy($dateField, 'DESC');

            $cutoffs = $builder->get()->getResult();
            
        } catch (\Exception $e) {
            // If there's an error, show debug info
            $cutoffs = [];
            $dateField = 'created_at'; // fallback
            $idField = 'id'; // fallback
            session()->setFlashdata('debug_info', 'Error: ' . $e->getMessage());
        }

        // Calculate summary based on available columns in the view
        $summary = [
            'total_cutoffs' => count($cutoffs),
            'total_sales' => array_sum(array_column($cutoffs, 'jml_gtotal')),
            'total_purchases' => 0, // Will be calculated separately if needed
            'net_amount' => 0
        ];
        $summary['net_amount'] = $summary['total_sales'] - $summary['total_purchases'];

        // Get outlets for filter
        $gudangModel = new \App\Models\GudangModel();
        $outlets = $gudangModel->where('status', '1')->where('status_hps', '0')->findAll();

        $data = [
            'title' => 'Cut-off Report',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'cutoffs' => $cutoffs,
            'summary' => $summary,
            'outlets' => $outlets,
            'dateField' => $dateField,
            'idField' => $idField,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'outlet' => $outlet
            ],
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('laporan') . '">Laporan</a></li>
                <li class="breadcrumb-item active">Cut-off Report</li>
            '
        ];

        return view(get_active_theme() . '/laporan/cutoff_report', $data);
    }

    public function export()
    {
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?: date('Y-m-d');
        $outlet = $this->request->getGet('outlet');

        // Determine which date field to use (same logic as index)
        try {
            $testQuery = $this->db->table('v_trans_jual_cutoff')->limit(1)->get();
            $columns = array_keys((array)$testQuery->getRow());
            
            $dateField = 'created_at'; // default
            if (in_array('tgl_masuk', $columns)) {
                $dateField = 'tgl_masuk';
            } elseif (in_array('created_at', $columns)) {
                $dateField = 'created_at';
            } elseif (in_array('tgl_bayar', $columns)) {
                $dateField = 'tgl_bayar';
            }
        } catch (\Exception $e) {
            $dateField = 'created_at'; // fallback
        }

        // Get cut-off data for export from your existing view
        $builder = $this->db->table('v_trans_jual_cutoff');
        $builder->select('*')
            ->where("DATE($dateField) >=", $startDate)
            ->where("DATE($dateField) <=", $endDate);

        if ($outlet) {
            $builder->where('id_gudang', $outlet);
        }

        $cutoffs = $builder->orderBy($dateField, 'DESC')
                          ->get()
                          ->getResult();

        // Set headers for CSV download
        $filename = 'cutoff_report_' . date('Y-m-d_H-i-s') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Open output stream
        $output = fopen('php://output', 'w');

        // CSV headers
        fputcsv($output, [
            'Transaction Date',
            'Invoice Number',
            'Outlet',
            'User',
            'Customer',
            'Total Amount',
            'Payment Amount',
            'Payment Method',
            'Status'
        ]);

        // CSV data
        foreach ($cutoffs as $cutoff) {
            fputcsv($output, [
                tgl_indo2($cutoff->{$dateField}),
                $cutoff->no_nota ?? 'N/A',
                $cutoff->gudang ?? 'N/A',
                $cutoff->first_name ?? 'N/A',
                $cutoff->nama_pelanggan ?? 'Walk-in',
                number_format($cutoff->jml_gtotal ?? 0, 2),
                number_format($cutoff->jml_bayar ?? 0, 2),
                $cutoff->metode_bayar ?? 'Cash',
                $cutoff->status_bayar == '1' ? 'Paid' : 'Pending'
            ]);
        }

        fclose($output);
        exit;
    }

    public function detail($id)
    {
        // Determine which date field and ID field to use (same logic as index)
        try {
            $testQuery = $this->db->table('v_trans_jual_cutoff')->limit(1)->get();
            $columns = array_keys((array)$testQuery->getRow());
            
            $dateField = 'created_at'; // default
            if (in_array('tgl_masuk', $columns)) {
                $dateField = 'tgl_masuk';
            } elseif (in_array('created_at', $columns)) {
                $dateField = 'created_at';
            } elseif (in_array('tgl_bayar', $columns)) {
                $dateField = 'tgl_bayar';
            }
            
            // Determine which ID field to use
            $idField = 'id'; // default
            if (in_array('id', $columns)) {
                $idField = 'id';
            } elseif (in_array('id_trans_jual', $columns)) {
                $idField = 'id_trans_jual';
            } elseif (in_array('trans_id', $columns)) {
                $idField = 'trans_id';
            }
        } catch (\Exception $e) {
            $dateField = 'created_at'; // fallback
            $idField = 'id'; // fallback
        }

        $cutoff = $this->db->table('v_trans_jual_cutoff')
                          ->where($idField, $id)
                          ->get()
                          ->getRow();

        if (!$cutoff) {
            return redirect()->to(base_url('laporan/cutoff'))->with('error', 'Transaction data not found.');
        }

        // Get transactions for this date and outlet
        $cutoffDate = $cutoff->{$dateField};
        $outletId = $cutoff->id_gudang;

        // Sales transactions
        $sales = $this->transJualModel->select('
                tbl_trans_jual.*,
                tbl_m_pelanggan.nama as customer_name
            ')
            ->join('tbl_m_pelanggan', 'tbl_m_pelanggan.id = tbl_trans_jual.id_pelanggan', 'left')
            ->where('DATE(tbl_trans_jual.created_at)', date('Y-m-d', strtotime($cutoffDate)))
            ->where('tbl_trans_jual.id_gudang', $outletId)
            ->where('tbl_trans_jual.status_bayar', '1')
            ->where('tbl_trans_jual.deleted_at IS NULL')
            ->findAll();

        // Purchase transactions
        $purchases = $this->transBeliModel->select('
                tbl_trans_beli.*,
                tbl_m_supplier.nama as supplier_name
            ')
            ->join('tbl_m_supplier', 'tbl_m_supplier.id = tbl_trans_beli.id_supplier', 'left')
            ->where('DATE(tbl_trans_beli.created_at)', date('Y-m-d', strtotime($cutoffDate)))
            ->where('tbl_trans_beli.id_gudang', $outletId)
            ->where('tbl_trans_beli.status_bayar', '1')
            ->where('tbl_trans_beli.deleted_at IS NULL')
            ->findAll();

        $data = [
            'title' => 'Cut-off Detail',
            'Pengaturan' => $this->pengaturan,
            'user' => $this->ionAuth->user()->row(),
            'cutoff' => $cutoff,
            'sales' => $sales,
            'purchases' => $purchases,
            'breadcrumbs' => '
                <li class="breadcrumb-item"><a href="' . base_url() . '">Beranda</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('laporan') . '">Laporan</a></li>
                <li class="breadcrumb-item"><a href="' . base_url('laporan/cutoff') . '">Cut-off Report</a></li>
                <li class="breadcrumb-item active">Detail</li>
            '
        ];

        return view(get_active_theme() . '/laporan/cutoff_detail', $data);
    }

    /**
     * Debug method to check view structure
     */
    public function debug()
    {
        try {
            // Get a sample record from the view
            $sample = $this->db->table('v_trans_jual_cutoff')->limit(5)->get()->getResult();
            
            $debug_info = [
                'view_exists' => !empty($sample),
                'record_count' => count($sample),
                'columns' => !empty($sample) ? array_keys((array)$sample[0]) : [],
                'sample_data' => $sample
            ];
            
            echo '<h2>Debug Info for v_trans_jual_cutoff</h2>';
            echo '<pre>' . print_r($debug_info, true) . '</pre>';
            exit;
            
        } catch (\Exception $e) {
            echo '<h2>Error accessing v_trans_jual_cutoff</h2>';
            echo '<p>Error: ' . $e->getMessage() . '</p>';
            echo '<p>This suggests the view might not exist or have a different name.</p>';
            
            // Try to list available views/tables
            try {
                $tables = $this->db->listTables();
                echo '<h3>Available Tables:</h3>';
                echo '<pre>' . print_r($tables, true) . '</pre>';
            } catch (\Exception $e2) {
                echo '<p>Could not list tables: ' . $e2->getMessage() . '</p>';
            }
            exit;
        }
    }
}
