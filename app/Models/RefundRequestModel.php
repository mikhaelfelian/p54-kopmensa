<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Refund Request Model
 * 
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-18
 * Description: Model for managing refund requests
 */
class RefundRequestModel extends Model
{
    protected $table            = 'tbl_refund_requests';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_transaction',
        'id_user',
        'id_pelanggan',
        'no_nota',
        'amount',
        'reason',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'id_transaction' => 'required|integer',
        'id_user'        => 'required|integer',
        'id_pelanggan'   => 'required|integer',
        'no_nota'        => 'required|max_length[50]',
        'amount'         => 'required|numeric',
        'reason'         => 'required|min_length[10]',
        'status'         => 'permit_empty|in_list[pending,approved,rejected]'
    ];

    protected $validationMessages = [
        'id_transaction' => [
            'required' => 'ID transaksi wajib diisi',
            'integer' => 'ID transaksi harus berupa angka'
        ],
        'id_user' => [
            'required' => 'ID user wajib diisi',
            'integer' => 'ID user harus berupa angka'
        ],
        'id_pelanggan' => [
            'required' => 'ID pelanggan wajib diisi',
            'integer' => 'ID pelanggan harus berupa angka'
        ],
        'no_nota' => [
            'required' => 'Nomor nota wajib diisi',
            'max_length' => 'Nomor nota maksimal 50 karakter'
        ],
        'amount' => [
            'required' => 'Jumlah refund wajib diisi',
            'numeric' => 'Jumlah refund harus berupa angka'
        ],
        'reason' => [
            'required' => 'Alasan refund wajib diisi',
            'min_length' => 'Alasan refund minimal 10 karakter'
        ]
    ];

    /**
     * Get refund requests with relations
     */
    public function getRefundRequestsWithRelations($perPage = null, $offset = 0, $search = null, $status = null)
    {
        $builder = $this->select('
            tbl_refund_requests.*,
            tbl_trans_jual.no_nota as transaction_no,
            tbl_m_pelanggan.nama as customer_name,
            tbl_ion_users.first_name,
            tbl_ion_users.last_name,
            tbl_ion_users.username
        ')
        ->join('tbl_trans_jual', 'tbl_trans_jual.id = tbl_refund_requests.id_transaction', 'left')
        ->join('tbl_m_pelanggan', 'tbl_m_pelanggan.id = tbl_refund_requests.id_pelanggan', 'left')
        ->join('tbl_ion_users', 'tbl_ion_users.id = tbl_refund_requests.id_user', 'left')
        ->orderBy('tbl_refund_requests.created_at', 'DESC');

        if ($search) {
            $builder->groupStart()
                ->like('tbl_refund_requests.no_nota', $search)
                ->orLike('tbl_m_pelanggan.nama', $search)
                ->orLike('tbl_refund_requests.reason', $search)
                ->groupEnd();
        }

        if ($status) {
            $builder->where('tbl_refund_requests.status', $status);
        }

        if ($perPage) {
            // Ensure perPage and offset are integers
            $perPage = (int) $perPage;
            $offset = (int) $offset;
            return $builder->limit($perPage, $offset)->findAll();
        }

        return $builder->findAll();
    }

    /**
     * Get refund request by ID with relations
     */
    public function getRefundRequestWithRelations($id)
    {
        return $this->select('
            tbl_refund_requests.*,
            tbl_trans_jual.no_nota as transaction_no,
            tbl_trans_jual.jml_gtotal as transaction_amount,
            tbl_trans_jual.tgl_masuk as transaction_date,
            tbl_m_pelanggan.nama as customer_name,
            tbl_m_pelanggan.alamat as customer_address,
            tbl_m_pelanggan.no_telp as customer_phone,
            tbl_ion_users.first_name,
            tbl_ion_users.last_name,
            tbl_ion_users.username as cashier_name
        ')
        ->join('tbl_trans_jual', 'tbl_trans_jual.id = tbl_refund_requests.id_transaction', 'left')
        ->join('tbl_m_pelanggan', 'tbl_m_pelanggan.id = tbl_refund_requests.id_pelanggan', 'left')
        ->join('tbl_ion_users', 'tbl_ion_users.id = tbl_refund_requests.id_user', 'left')
        ->where('tbl_refund_requests.id', $id)
        ->first();
    }

    /**
     * Get pending refund requests count
     */
    public function getPendingCount()
    {
        return $this->where('status', 'pending')->countAllResults();
    }

    /**
     * Approve refund request
     */
    public function approveRefund($id, $approvedBy)
    {
        return $this->update($id, [
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Reject refund request
     */
    public function rejectRefund($id, $approvedBy, $rejectionReason)
    {
        return $this->update($id, [
            'status' => 'rejected',
            'approved_by' => $approvedBy,
            'rejection_reason' => $rejectionReason
        ]);
    }
}
