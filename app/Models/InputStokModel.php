<?php

namespace App\Models;

use CodeIgniter\Model;

class InputStokModel extends Model
{
    protected $table = 'tbl_input_stok';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'no_terima',
        'tgl_terima',
        'id_supplier',
        'id_gudang',
        'id_penerima',
        'keterangan',
        'status',
        'status_hps',
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'no_terima'    => 'required|max_length[50]',
        'tgl_terima'   => 'required|valid_date',
        'id_supplier'  => 'required|integer',
        'id_gudang'    => 'required|integer',
        'id_penerima'  => 'required|integer',
    ];

    protected $validationMessages = [
        'no_terima' => [
            'required'   => 'Nomor terima harus diisi',
            'max_length' => 'Nomor terima maksimal 50 karakter'
        ],
        'tgl_terima' => [
            'required'   => 'Tanggal terima harus diisi',
            'valid_date' => 'Format tanggal tidak valid'
        ],
        'id_supplier' => [
            'required' => 'Supplier harus dipilih',
            'integer'  => 'Supplier tidak valid'
        ],
        'id_gudang' => [
            'required' => 'Gudang harus dipilih',
            'integer'  => 'Gudang tidak valid'
        ],
        'id_penerima' => [
            'required' => 'Penerima harus dipilih',
            'integer'  => 'Penerima tidak valid'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Generate next no_terima
     */
    public function generateNoTerima()
    {
        $prefix = 'IST' . date('Ym');
        $lastRecord = $this->select('no_terima')
            ->where('no_terima LIKE', $prefix . '%')
            ->orderBy('no_terima', 'DESC')
            ->first();

        if ($lastRecord) {
            $lastNumber = (int) substr($lastRecord->no_terima, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get input stok with related data
     */
    public function getWithRelations($id = null)
    {
        $builder = $this->select('
                tbl_input_stok.*,
                tbl_m_supplier.nama as supplier_nama,
                tbl_m_gudang.nama as gudang_nama,
                tbl_m_karyawan.nama as penerima_nama
            ')
            ->join('tbl_m_supplier', 'tbl_m_supplier.id = tbl_input_stok.id_supplier', 'left')
            ->join('tbl_m_gudang', 'tbl_m_gudang.id = tbl_input_stok.id_gudang', 'left')
            ->join('tbl_m_karyawan', 'tbl_m_karyawan.id = tbl_input_stok.id_penerima', 'left')
            ->where('tbl_input_stok.status_hps', '0');

        if ($id) {
            return $builder->where('tbl_input_stok.id', $id)->first();
        }

        return $builder->orderBy('tbl_input_stok.tgl_terima', 'DESC');
    }
}
