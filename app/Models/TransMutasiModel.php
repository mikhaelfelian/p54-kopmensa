<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-28
 * Github : github.com/mikhaelfelian
 * description : Model for managing tbl_trans_mutasi data
 * This file represents the TransMutasiModel.
 */
class TransMutasiModel extends Model
{
    protected $table            = 'tbl_trans_mutasi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_user',
        'id_user_terima',
        'no_nota',
        'created_at',
        'updated_at',
        'kode_nota_dpn',
        'kode_nota_blk',
        'tgl_masuk',
        'tgl_keluar',
        'id_gd_asal',
        'id_gd_tujuan',
        'id_outlet',
        'keterangan',
        'status_nota',
        'status_terima',
        'tipe',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
} 