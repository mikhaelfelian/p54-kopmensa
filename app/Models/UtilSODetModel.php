<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-28
 * Github : github.com/mikhaelfelian
 * description : Model for managing tbl_util_so_det data
 * This file represents the UtilSODetModel.
 */
class UtilSODetModel extends Model
{
    protected $table            = 'tbl_util_so_det';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_so',
        'id_item',
        'id_satuan',
        'id_user',
        'created_at',
        'updated_at',
        'tgl_masuk',
        'kode',
        'item',
        'satuan',
        'keterangan',
        'jml',
        'jml_sys',
        'jml_so',
        'jml_sls',
        'jml_satuan',
        'sp',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
} 