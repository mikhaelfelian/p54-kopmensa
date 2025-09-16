<?php

namespace App\Controllers\Api\Pos;

use App\Controllers\BaseController;
use App\Models\PelangganModel;
use App\Models\TransJualModel;
use App\Models\KaryawanModel;
use CodeIgniter\API\ResponseTrait;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-18
 * Github: github.com/mikhaelfelian
 * Description: API controller for managing member (anggota) data for POS
 * This file represents the Anggota API controller.
 */
class Anggota extends BaseController
{
    use ResponseTrait;

    protected $mPelanggan;
    protected $mTransJual;
    protected $mKaryawan;
    protected $ionAuth;

    /**
     * Initialize all model properties in the constructor for reuse.
     */
    public function __construct()
    {
        $this->mPelanggan = new PelangganModel();
        $this->mTransJual = new TransJualModel();
        $this->mKaryawan = new KaryawanModel();
        $this->ionAuth = new \IonAuth\Libraries\IonAuth();
    }

    /**
     * Get member profile by user ID
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function profile()
    {
        // Get user_id from request or JWT token
        $userId = $this->request->getGet('user_id') ?? $this->request->getPost('user_id');
        
        if (!$userId) {
            return $this->failValidationErrors('User ID is required');
        }

        try {
            // Get member data by user_id
            $member = $this->mPelanggan
                ->select('tbl_m_pelanggan.*, tbl_ion_users.username, tbl_ion_users.email as user_email, tbl_ion_users.active')
                ->join('tbl_ion_users', 'tbl_ion_users.id = tbl_m_pelanggan.id_user', 'left')
                ->where('tbl_m_pelanggan.id_user', $userId)
                ->where('tbl_m_pelanggan.tipe', '1') // Only members (tipe = 1)
                ->first();

            if (!$member) {
                return $this->failNotFound('Member not found');
            }

            // Format member data
            $memberData = [
                'id'            => (int)$member->id,
                'id_user'       => (int)$member->id_user,
                'kode'          => $member->kode,
                'nama'          => $member->nama,
                'no_telp'       => $member->no_telp,
                'email'         => $member->email ?? $member->user_email,
                'alamat'        => $member->alamat,
                'kota'          => $member->kota,
                'provinsi'      => $member->provinsi,
                'tipe'          => (int)$member->tipe,
                'status'        => (int)$member->status,
                'limit'         => (float)($member->limit ?? 0),
                'username'      => $member->username,
                'is_active'     => (bool)($member->active ?? false),
                'tgl_masuk'     => $member->tgl_masuk,
                'created_at'    => $member->created_at,
                'updated_at'    => $member->updated_at
            ];

            return $this->respond([
                'success' => true,
                'message' => 'Member profile retrieved successfully',
                'member'  => $memberData
            ]);

        } catch (\Exception $e) {
            return $this->failServerError('Failed to retrieve member profile: ' . $e->getMessage());
        }
    }

    /**
     * Get member list with pagination and search
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function getMembers()
    {
        $page = $this->request->getGet('page') ?? 1;
        $perPage = $this->request->getGet('per_page') ?? 10;
        $search = $this->request->getGet('search') ?? '';

        try {
            $builder = $this->mPelanggan
                ->select('tbl_m_pelanggan.*, tbl_ion_users.username, tbl_ion_users.email as user_email, tbl_ion_users.active')
                ->join('tbl_ion_users', 'tbl_ion_users.id = tbl_m_pelanggan.id_user', 'left')
                ->where('tbl_m_pelanggan.tipe', '1'); // Only members (tipe = 1)

            // Apply search filter if provided
            if (!empty($search)) {
                $builder->groupStart()
                       ->like('tbl_m_pelanggan.nama', $search)
                       ->orLike('tbl_m_pelanggan.kode', $search)
                       ->orLike('tbl_m_pelanggan.no_telp', $search)
                       ->orLike('tbl_m_pelanggan.email', $search)
                       ->orLike('tbl_ion_users.username', $search)
                       ->groupEnd();
            }

            // Get total count for pagination
            $total = $builder->countAllResults(false);

            // Get paginated results
            $members = $builder->orderBy('tbl_m_pelanggan.created_at', 'DESC')
                              ->paginate($perPage, 'default', $page);

            // Format member data
            $formattedMembers = [];
            foreach ($members as $member) {
                $formattedMembers[] = [
                    'id'            => (int)$member->id,
                    'id_user'       => (int)$member->id_user,
                    'kode'          => $member->kode,
                    'nama'          => $member->nama,
                    'no_telp'       => $member->no_telp,
                    'email'         => $member->email ?? $member->user_email,
                    'alamat'        => $member->alamat,
                    'kota'          => $member->kota,
                    'provinsi'      => $member->provinsi,
                    'tipe'          => (int)$member->tipe,
                    'status'        => (int)$member->status,
                    'limit'         => (float)($member->limit ?? 0),
                    'username'      => $member->username,
                    'is_active'     => (bool)($member->active ?? false),
                    'tgl_masuk'     => $member->tgl_masuk,
                    'created_at'    => $member->created_at,
                    'updated_at'    => $member->updated_at
                ];
            }

            // Calculate pagination info
            $totalPages = ceil($total / $perPage);

            return $this->respond([
                'success'      => true,
                'message'      => 'Members retrieved successfully',
                'total'        => (int)$total,
                'current_page' => (int)$page,
                'per_page'     => (int)$perPage,
                'total_pages'  => (int)$totalPages,
                'members'      => $formattedMembers
            ]);

        } catch (\Exception $e) {
            return $this->failServerError('Failed to retrieve members: ' . $e->getMessage());
        }
    }

    /**
     * Get member detail by ID
     * 
     * @param int $id Member ID
     * @return \CodeIgniter\HTTP\Response
     */
    public function getMember($id)
    {
        if (!$id) {
            return $this->failValidationErrors('Member ID is required');
        }

        try {
            $member = $this->mPelanggan
                ->select('tbl_m_pelanggan.*, tbl_ion_users.username, tbl_ion_users.email as user_email, tbl_ion_users.active')
                ->join('tbl_ion_users', 'tbl_ion_users.id = tbl_m_pelanggan.id_user', 'left')
                ->where('tbl_m_pelanggan.id', $id)
                ->where('tbl_m_pelanggan.tipe', '1') // Only members (tipe = 1)
                ->first();

            if (!$member) {
                return $this->failNotFound('Member not found');
            }

            // Get member's transaction summary
            $transactionStats = $this->getMemberTransactionStats($id);

            // Format member data
            $memberData = [
                'id'            => (int)$member->id,
                'id_user'       => (int)$member->id_user,
                'kode'          => $member->kode,
                'nama'          => $member->nama,
                'no_telp'       => $member->no_telp,
                'email'         => $member->email ?? $member->user_email,
                'alamat'        => $member->alamat,
                'kota'          => $member->kota,
                'provinsi'      => $member->provinsi,
                'tipe'          => (int)$member->tipe,
                'status'        => (int)$member->status,
                'limit'         => (float)($member->limit ?? 0),
                'username'      => $member->username,
                'is_active'     => (bool)($member->active ?? false),
                'tgl_masuk'     => $member->tgl_masuk,
                'created_at'    => $member->created_at,
                'updated_at'    => $member->updated_at,
                'transaction_stats' => $transactionStats
            ];

            return $this->respond([
                'success' => true,
                'message' => 'Member details retrieved successfully',
                'member'  => $memberData
            ]);

        } catch (\Exception $e) {
            return $this->failServerError('Failed to retrieve member details: ' . $e->getMessage());
        }
    }

    /**
     * Search members by various criteria
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function searchMembers()
    {
        $search = $this->request->getGet('q') ?? $this->request->getPost('q');
        $limit = $this->request->getGet('limit') ?? 10;

        if (empty($search)) {
            return $this->failValidationErrors('Search query is required');
        }

        try {
            $members = $this->mPelanggan
                ->select('tbl_m_pelanggan.*, tbl_ion_users.username')
                ->join('tbl_ion_users', 'tbl_ion_users.id = tbl_m_pelanggan.id_user', 'left')
                ->where('tbl_m_pelanggan.tipe', '1') // Only members (tipe = 1)
                ->where('tbl_m_pelanggan.status', '1') // Only active members
                ->groupStart()
                    ->like('tbl_m_pelanggan.nama', $search)
                    ->orLike('tbl_m_pelanggan.kode', $search)
                    ->orLike('tbl_m_pelanggan.no_telp', $search)
                    ->orLike('tbl_m_pelanggan.email', $search)
                    ->orLike('tbl_ion_users.username', $search)
                ->groupEnd()
                ->orderBy('tbl_m_pelanggan.nama', 'ASC')
                ->limit($limit)
                ->findAll();

            // Format member data
            $formattedMembers = [];
            foreach ($members as $member) {
                $formattedMembers[] = [
                    'id'        => (int)$member->id,
                    'kode'      => $member->kode,
                    'nama'      => $member->nama,
                    'no_telp'   => $member->no_telp,
                    'email'     => $member->email,
                    'username'  => $member->username,
                    'status'    => (int)$member->status,
                    'limit'     => (float)($member->limit ?? 0)
                ];
            }

            return $this->respond([
                'success' => true,
                'message' => 'Members search completed successfully',
                'total'   => count($formattedMembers),
                'members' => $formattedMembers
            ]);

        } catch (\Exception $e) {
            return $this->failServerError('Failed to search members: ' . $e->getMessage());
        }
    }

    /**
     * Get member's transaction history
     * 
     * @param int $id Member ID
     * @return \CodeIgniter\HTTP\Response
     */
    public function getMemberTransactions($id)
    {
        if (!$id) {
            return $this->failValidationErrors('Member ID is required');
        }

        $page = $this->request->getGet('page') ?? 1;
        $perPage = $this->request->getGet('per_page') ?? 10;

        try {
            // Verify member exists
            $member = $this->mPelanggan->where('id', $id)->where('tipe', '1')->first();
            if (!$member) {
                return $this->failNotFound('Member not found');
            }

            // Get member's transactions
            $builder = $this->mTransJual
                ->select('id, no_nota, tgl_masuk, jml_gtotal, jml_bayar, jml_kembali, status, status_nota, status_bayar, metode_bayar, created_at')
                ->where('id_pelanggan', $id)
                ->where('status !=', '0'); // Exclude draft transactions

            // Get total count
            $total = $builder->countAllResults(false);

            // Get paginated results
            $transactions = $builder->orderBy('created_at', 'DESC')
                                  ->paginate($perPage, 'default', $page);

            // Format transaction data
            $formattedTransactions = [];
            foreach ($transactions as $transaction) {
                $formattedTransactions[] = [
                    'id'           => (int)$transaction->id,
                    'no_nota'      => $transaction->no_nota,
                    'tgl_masuk'    => $transaction->tgl_masuk,
                    'jml_gtotal'   => (float)$transaction->jml_gtotal,
                    'jml_bayar'    => (float)$transaction->jml_bayar,
                    'jml_kembali'  => (float)$transaction->jml_kembali,
                    'status'       => (int)$transaction->status,
                    'status_nota'  => (int)$transaction->status_nota,
                    'status_bayar' => (int)$transaction->status_bayar,
                    'metode_bayar' => $transaction->metode_bayar,
                    'created_at'   => $transaction->created_at
                ];
            }

            // Calculate pagination info
            $totalPages = ceil($total / $perPage);

            return $this->respond([
                'success'      => true,
                'message'      => 'Member transactions retrieved successfully',
                'member_id'    => (int)$id,
                'member_name'  => $member->nama,
                'total'        => (int)$total,
                'current_page' => (int)$page,
                'per_page'     => (int)$perPage,
                'total_pages'  => (int)$totalPages,
                'transactions' => $formattedTransactions
            ]);

        } catch (\Exception $e) {
            return $this->failServerError('Failed to retrieve member transactions: ' . $e->getMessage());
        }
    }

    /**
     * Validate member credentials (login validation)
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function validateMember()
    {
        $username = $this->request->getPost('username') ?? $this->request->getPost('user');
        $password = $this->request->getPost('password') ?? $this->request->getPost('pass');

        if (empty($username) || empty($password)) {
            return $this->failValidationErrors('Username and password are required');
        }

        try {
            // Validate using IonAuth
            if ($this->ionAuth->login($username, $password)) {
                $user = $this->ionAuth->user()->row();
                
                // Get member data
                $member = $this->mPelanggan
                    ->where('id_user', $user->id)
                    ->where('tipe', '1') // Only members
                    ->first();

                if (!$member) {
                    // Logout if not a member
                    $this->ionAuth->logout();
                    return $this->failValidationErrors('User is not a member');
                }

                // Check if member is active
                if ($member->status != '1') {
                    $this->ionAuth->logout();
                    return $this->failValidationErrors('Member account is inactive');
                }

                // Generate JWT token if needed (optional)
                $token = $this->generateJWTToken($user, $member);

                $memberData = [
                    'user_id'    => (int)$user->id,
                    'member_id'  => (int)$member->id,
                    'username'   => $user->username,
                    'email'      => $user->email,
                    'nama'       => $member->nama,
                    'kode'       => $member->kode,
                    'no_telp'    => $member->no_telp,
                    'status'     => (int)$member->status,
                    'limit'      => (float)($member->limit ?? 0),
                    'tipe'       => (int)$member->tipe
                ];

                return $this->respond([
                    'success'   => true,
                    'message'   => 'Member validation successful',
                    'token'     => $token,
                    'member'    => $memberData
                ]);

            } else {
                return $this->failUnauthorized('Invalid username or password');
            }

        } catch (\Exception $e) {
            return $this->failServerError('Failed to validate member: ' . $e->getMessage());
        }
    }

    /**
     * Get member statistics
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function getMemberStats()
    {
        try {
            // Total members
            $totalMembers = $this->mPelanggan->where('tipe', '1')->countAllResults();

            // Active members
            $activeMembers = $this->mPelanggan
                ->where('tipe', '1')
                ->where('status', '1')
                ->countAllResults();

            // New members this month
            $newMembersThisMonth = $this->mPelanggan
                ->where('tipe', '1')
                ->where('MONTH(created_at)', date('m'))
                ->where('YEAR(created_at)', date('Y'))
                ->countAllResults();

            // Members with transactions this month
            $activeMembersThisMonth = $this->mPelanggan
                ->select('tbl_m_pelanggan.id')
                ->join('tbl_trans_jual', 'tbl_trans_jual.id_pelanggan = tbl_m_pelanggan.id', 'inner')
                ->where('tbl_m_pelanggan.tipe', '1')
                ->where('MONTH(tbl_trans_jual.created_at)', date('m'))
                ->where('YEAR(tbl_trans_jual.created_at)', date('Y'))
                ->where('tbl_trans_jual.status !=', '0')
                ->groupBy('tbl_m_pelanggan.id')
                ->countAllResults();

            return $this->respond([
                'success' => true,
                'message' => 'Member statistics retrieved successfully',
                'stats'   => [
                    'total_members'            => (int)$totalMembers,
                    'active_members'           => (int)$activeMembers,
                    'inactive_members'         => (int)($totalMembers - $activeMembers),
                    'new_members_this_month'   => (int)$newMembersThisMonth,
                    'active_members_this_month' => (int)$activeMembersThisMonth
                ]
            ]);

        } catch (\Exception $e) {
            return $this->failServerError('Failed to retrieve member statistics: ' . $e->getMessage());
        }
    }

    /**
     * Test endpoint to check if authentication and routing works
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function testEndpoint()
    {
        return $this->respond([
            'success' => true,
            'message' => 'Anggota API Test endpoint working!',
            'method'  => $this->request->getMethod(),
            'uri'     => $this->request->getUri()->getPath(),
            'timestamp' => date('Y-m-d H:i:s'),
            'user'    => session()->get('user_id') ?? 'Not authenticated'
        ]);
    }

    /**
     * Get member transaction statistics
     * 
     * @param int $memberId
     * @return array
     */
    private function getMemberTransactionStats($memberId)
    {
        try {
            // Total transactions
            $totalTransactions = $this->mTransJual
                ->where('id_pelanggan', $memberId)
                ->where('status !=', '0')
                ->countAllResults();

            // Total spending
            $totalSpending = $this->mTransJual
                ->selectSum('jml_gtotal', 'total')
                ->where('id_pelanggan', $memberId)
                ->where('status !=', '0')
                ->get()
                ->getRow()
                ->total ?? 0;

            // Last transaction
            $lastTransaction = $this->mTransJual
                ->select('no_nota, tgl_masuk, jml_gtotal')
                ->where('id_pelanggan', $memberId)
                ->where('status !=', '0')
                ->orderBy('created_at', 'DESC')
                ->first();

            // This month transactions
            $thisMonthTransactions = $this->mTransJual
                ->where('id_pelanggan', $memberId)
                ->where('status !=', '0')
                ->where('MONTH(created_at)', date('m'))
                ->where('YEAR(created_at)', date('Y'))
                ->countAllResults();

            // This month spending
            $thisMonthSpending = $this->mTransJual
                ->selectSum('jml_gtotal', 'total')
                ->where('id_pelanggan', $memberId)
                ->where('status !=', '0')
                ->where('MONTH(created_at)', date('m'))
                ->where('YEAR(created_at)', date('Y'))
                ->get()
                ->getRow()
                ->total ?? 0;

            return [
                'total_transactions'     => (int)$totalTransactions,
                'total_spending'         => (float)$totalSpending,
                'last_transaction'       => $lastTransaction ? [
                    'no_nota'    => $lastTransaction->no_nota,
                    'tgl_masuk'  => $lastTransaction->tgl_masuk,
                    'jml_gtotal' => (float)$lastTransaction->jml_gtotal
                ] : null,
                'this_month_transactions' => (int)$thisMonthTransactions,
                'this_month_spending'     => (float)$thisMonthSpending
            ];

        } catch (\Exception $e) {
            log_message('error', 'Failed to get member transaction stats: ' . $e->getMessage());
            return [
                'total_transactions'      => 0,
                'total_spending'          => 0,
                'last_transaction'        => null,
                'this_month_transactions' => 0,
                'this_month_spending'     => 0
            ];
        }
    }

    /**
     * Generate JWT token for member
     * 
     * @param object $user
     * @param object $member
     * @return string|null
     */
    private function generateJWTToken($user, $member)
    {
        try {
            // This is a basic JWT implementation
            // You should use a proper JWT library like firebase/php-jwt
            
            $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
            $payload = json_encode([
                'user_id'    => $user->id,
                'member_id'  => $member->id,
                'username'   => $user->username,
                'tipe'       => $member->tipe,
                'iat'        => time(),
                'exp'        => time() + (24 * 60 * 60) // 24 hours
            ]);
            
            $headerEncoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
            $payloadEncoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
            
            $signature = hash_hmac('sha256', $headerEncoded . "." . $payloadEncoded, env('JWT_SECRET', 'your-secret-key'), true);
            $signatureEncoded = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
            
            return $headerEncoded . "." . $payloadEncoded . "." . $signatureEncoded;
            
        } catch (\Exception $e) {
            log_message('error', 'Failed to generate JWT token: ' . $e->getMessage());
            return null;
        }
    }
}
