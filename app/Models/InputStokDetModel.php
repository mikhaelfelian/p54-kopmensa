<?php

namespace App\Models;

use CodeIgniter\Model;

class InputStokDetModel extends Model
{
    protected $table = 'tbl_input_stok_det';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id_input_stok',
        'id_item',
        'id_satuan',
        'jml',
        'keterangan',
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'id_input_stok' => 'required|integer',
        'id_item'       => 'required|integer',
        'id_satuan'     => 'required|integer',
        'jml'           => 'required|decimal|greater_than[0]',
    ];

    protected $validationMessages = [
        'id_input_stok' => [
            'required' => 'Input stok ID harus diisi',
            'integer'  => 'Input stok ID tidak valid'
        ],
        'id_item' => [
            'required' => 'Item harus dipilih',
            'integer'  => 'Item tidak valid'
        ],
        'id_satuan' => [
            'required' => 'Satuan harus dipilih',
            'integer'  => 'Satuan tidak valid'
        ],
        'jml' => [
            'required'     => 'Jumlah harus diisi',
            'decimal'      => 'Jumlah harus berupa angka',
            'greater_than' => 'Jumlah harus lebih dari 0'
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
     * Get detail with related data
     */
    public function getWithRelations($idInputStok = null, $id = null)
    {
        $builder = $this->select('
                tbl_input_stok_det.*,
                tbl_m_item.item as item_nama,
                tbl_m_item.kode as item_kode,
                tbl_m_satuan.satuanBesar as satuan_nama
            ')
            ->join('tbl_m_item', 'tbl_m_item.id = tbl_input_stok_det.id_item', 'left')
            ->join('tbl_m_satuan', 'tbl_m_satuan.id = tbl_input_stok_det.id_satuan', 'left');

        if ($idInputStok) {
            $builder->where('tbl_input_stok_det.id_input_stok', $idInputStok);
        }

        if ($id) {
            return $builder->where('tbl_input_stok_det.id', $id)->first();
        }

        return $builder->orderBy('tbl_input_stok_det.id', 'ASC');
    }

    /**
     * Get items by input stok ID
     */
    public function getByInputStokId($idInputStok)
    {
        return $this->getWithRelations($idInputStok)->findAll();
    }
}
