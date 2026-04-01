<?php

namespace App\Models;

use CodeIgniter\Model;

class PelangganPoinLogModel extends Model
{
    protected $table            = 'tbl_pelanggan_poin_log';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_penjualan',
        'id_pelanggan',
        'poin',
        'created_at',
    ];

    protected $useTimestamps = false;
}
