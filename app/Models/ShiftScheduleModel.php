<?php

namespace App\Models;

use CodeIgniter\Model;

class ShiftScheduleModel extends Model
{
    protected $table            = 'tbl_m_shift_schedule';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'outlet_id',
        'day_of_week',
        'jam_buka',
        'jam_tutup',
        'keterangan',
        'status',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
