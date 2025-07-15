<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-28
 * Github : github.com/mikhaelfelian
 * description : Model for managing tbl_util_so data
 * This file represents the UtilSOModel.
 */
class UtilSOModel extends Model
{
    protected $table            = 'tbl_util_so';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_gudang',
        'id_outlet',
        'id_user',
        'tgl_masuk',
        'created_at',
        'updated_at',
        'keterangan',
        'reset',
        'status',
        'tipe',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
} 