<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-18
 * Github: github.com/mikhaelfelian
 * Description: Model for handling sales transaction platforms (tbl_trans_jual_plat)
 * This file represents the Model.
 */

namespace App\Models;

use CodeIgniter\Model;

class TransJualPlatModel extends Model
{
    protected $table            = 'tbl_trans_jual_plat';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_penjualan',
        'id_platform',
        'created_at',
        'modified_at',
        'no_nota',
        'platform',
        'keterangan',
        'nominal'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'modified_at';

    // Validation
    protected $validationRules      = [
        'id_penjualan' => 'required|integer',
        'id_platform' => 'required|integer',
        'no_nota' => 'required|max_length[50]',
        'platform' => 'required|max_length[160]',
        'keterangan' => 'permit_empty|max_length[160]',
        'nominal' => 'required|decimal'
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
     * Get platform transactions by sales transaction ID
     */
    public function getPlatformsByPenjualanId($penjualanId)
    {
        return $this->where('id_penjualan', $penjualanId)->findAll();
    }

    /**
     * Get platform transactions by nota number
     */
    public function getPlatformsByNota($noNota)
    {
        return $this->where('no_nota', $noNota)->findAll();
    }

    /**
     * Get platform transactions by platform ID
     */
    public function getPlatformsByPlatformId($platformId)
    {
        return $this->where('id_platform', $platformId)->findAll();
    }

    /**
     * Get platform transactions with platform information
     */
    public function getPlatformsWithInfo($penjualanId = null)
    {
        $builder = $this->db->table('tbl_trans_jual_plat tjp');
        $builder->select('tjp.*, mp.platform as nama_platform, mp.kode as platform_kode, mp.keterangan as keterangan_platform');
        $builder->join('tbl_m_platform mp', 'mp.id = tjp.id_platform', 'left');
        
        if ($penjualanId) {
            $builder->where('tjp.id_penjualan', $penjualanId);
        }
        
        return $builder->get()->getResult();
    }

    /**
     * Calculate total nominal for a sales transaction
     */
    public function getTotalNominalByPenjualanId($penjualanId)
    {
        return $this->selectSum('nominal')
                    ->where('id_penjualan', $penjualanId)
                    ->first();
    }

    /**
     * Get platform summary by date range
     */
    public function getPlatformSummaryByDateRange($startDate, $endDate)
    {
        return $this->select('tjp.platform, SUM(tjp.nominal) as total_nominal, COUNT(*) as total_transactions')
                    ->join('tbl_trans_jual tj', 'tj.id = tbl_trans_jual_plat.id_penjualan')
                    ->where('tj.created_at >=', $startDate)
                    ->where('tj.created_at <=', $endDate)
                    ->where('tj.status', '1')
                    ->groupBy('tjp.platform')
                    ->findAll();
    }

    /**
     * Get total payments by platform
     */
    public function getTotalPaymentsByPlatform($startDate = null, $endDate = null)
    {
        $builder = $this->db->table('tbl_trans_jual_plat tjp');
        $builder->select('tjp.platform, SUM(tjp.nominal) as total_payment, COUNT(*) as transaction_count');
        $builder->join('tbl_trans_jual tj', 'tj.id = tjp.id_penjualan');
        
        if ($startDate && $endDate) {
            $builder->where('tj.created_at >=', $startDate);
            $builder->where('tj.created_at <=', $endDate);
        }
        
        $builder->where('tj.status', '1');
        $builder->groupBy('tjp.platform');
        
        return $builder->get()->getResult();
    }
} 