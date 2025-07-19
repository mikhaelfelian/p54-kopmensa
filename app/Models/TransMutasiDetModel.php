<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-28
 * Github : github.com/mikhaelfelian
 * description : Model for managing tbl_trans_mutasi_det data
 * This file represents the TransMutasiDetModel.
 */
class TransMutasiDetModel extends Model
{
    protected $table            = 'tbl_trans_mutasi_det';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_mutasi',
        'id_satuan',
        'id_item',
        'id_user',
        'created_at',
        'updated_at',
        'no_nota',
        'kode',
        'item',
        'satuan',
        'keterangan',
        'jml',
        'jml_diterima',
        'jml_satuan',
        'status_brg',
        'status_terima',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
} 