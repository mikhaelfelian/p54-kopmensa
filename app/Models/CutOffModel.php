<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-09-11
 * Github: github.com/mikhaelfelian
 * Description: Model for handling cut-off data
 * This file represents the CutOffModel.
 */

namespace App\Models;

use CodeIgniter\Model;

class CutOffModel extends Model
{
    protected $table = 'tbl_cut_off';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        'no_cutoff',
        'tgl_cutoff',
        'id_gudang',
        'id_user',
        'total_penjualan',
        'total_pembelian',
        'total_kas_masuk',
        'total_kas_keluar',
        'saldo_kas',
        'keterangan',
        'status',
        'tgl_finalisasi',
        'id_user_finalisasi',
        'tgl_masuk',
        'tgl_ubah'
    ];

    protected $useTimestamps = false;
    protected $createdField = 'tgl_masuk';
    protected $updatedField = 'tgl_ubah';

    protected $validationRules = [
        'no_cutoff' => 'required|max_length[50]',
        'tgl_cutoff' => 'required',
        'id_gudang' => 'required|integer',
        'id_user' => 'required|integer',
        'total_penjualan' => 'permit_empty|decimal',
        'total_pembelian' => 'permit_empty|decimal',
        'total_kas_masuk' => 'permit_empty|decimal',
        'total_kas_keluar' => 'permit_empty|decimal',
        'saldo_kas' => 'permit_empty|decimal',
        'keterangan' => 'permit_empty|max_length[500]',
        'status' => 'required|in_list[0,1,2]' // 0=inactive, 1=active, 2=finalized
    ];

    protected $validationMessages = [
        'no_cutoff' => [
            'required' => 'Nomor cut-off harus diisi',
            'max_length' => 'Nomor cut-off maksimal 50 karakter'
        ],
        'tgl_cutoff' => [
            'required' => 'Tanggal cut-off harus diisi'
        ],
        'id_gudang' => [
            'required' => 'Gudang harus dipilih',
            'integer' => 'ID gudang harus berupa angka'
        ],
        'id_user' => [
            'required' => 'User harus diisi',
            'integer' => 'ID user harus berupa angka'
        ],
        'status' => [
            'required' => 'Status harus diisi',
            'in_list' => 'Status harus 0, 1, atau 2'
        ]
    ];

    /**
     * Get cut-off with related data
     */
    public function getWithRelations($id = null)
    {
        $builder = $this->select('
                tbl_cut_off.*,
                tbl_m_gudang.nama as gudang_nama,
                users.first_name as user_name,
                finalizer.first_name as finalizer_name
            ')
            ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_cut_off.id_gudang', 'left')
            ->join('users', 'users.id = tbl_cut_off.id_user', 'left')
            ->join('users as finalizer', 'finalizer.id = tbl_cut_off.id_user_finalisasi', 'left');

        if ($id) {
            return $builder->where('tbl_cut_off.id', $id)->first();
        }

        return $builder->orderBy('tbl_cut_off.tgl_cutoff', 'DESC');
    }

    /**
     * Get cut-off summary by date range
     */
    public function getSummaryByDateRange($startDate, $endDate, $idGudang = null)
    {
        $builder = $this->select('
                COUNT(id) as total_cutoffs,
                SUM(total_penjualan) as total_penjualan,
                SUM(total_pembelian) as total_pembelian,
                SUM(total_kas_masuk) as total_kas_masuk,
                SUM(total_kas_keluar) as total_kas_keluar,
                SUM(saldo_kas) as total_saldo_kas
            ')
            ->where('DATE(tgl_cutoff) >=', $startDate)
            ->where('DATE(tgl_cutoff) <=', $endDate)
            ->where('status !=', '0');

        if ($idGudang) {
            $builder->where('id_gudang', $idGudang);
        }

        return $builder->first();
    }

    /**
     * Check if cut-off exists for date and warehouse
     */
    public function existsForDate($date, $idGudang)
    {
        return $this->where('DATE(tgl_cutoff)', $date)
                   ->where('id_gudang', $idGudang)
                   ->countAllResults() > 0;
    }

    /**
     * Get latest cut-off for warehouse
     */
    public function getLatestForWarehouse($idGudang)
    {
        return $this->where('id_gudang', $idGudang)
                   ->where('status !=', '0')
                   ->orderBy('tgl_cutoff', 'DESC')
                   ->first();
    }
}
