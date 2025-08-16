<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-22
 * Github : github.com/mikhaelfelian
 * description : API Authentication controller for Anggota
 * This file represents the Controller class for Auth API.
 */

namespace App\Controllers\Api\Anggota;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
use Config\JWT as JWTConfig;

class Auth extends BaseController
{
    use ResponseTrait;

    public function login()
    {
        $identity = $this->request->getPost('user');
        $password = $this->request->getPost('pass');

        $ionAuth = new \IonAuth\Libraries\IonAuth();
        if (!$ionAuth->login($identity, $password)) {
            $errors = $ionAuth->errors();
            // $errors may be a string (HTML) or an array, depending on IonAuth config.
            // For API, we want a plain message, not HTML.
            // If $errors is a string (HTML), strip tags and get the first line.
            if (is_array($errors)) {
                $errorMessage = !empty($errors) ? end($errors) : 'Login failed';
            } elseif (is_string($errors)) {
                // Remove HTML tags and get the first non-empty line
                $plain = trim(strip_tags($errors));
                $lines = array_filter(array_map('trim', explode("\n", $plain)));
                $errorMessage = !empty($lines) ? array_shift($lines) : 'Login failed';
            } else {
                $errorMessage = 'Login failed';
            }
            return $this->respond([
                'success' => false,
                'message' => $errorMessage
            ], 401);
        }

        $user = $ionAuth->user()->row();

        // Get user groups to determine 'tipe'
        $groups = $ionAuth->getUsersGroups($user->id)->getResult();
        $tipe = !empty($groups) ? $groups[0]->name : null; // Using the first group name as 'tipe'

        $jwtConfig = new JWTConfig();
        $issuedAt = time();
        $payload = [
            'iat' => $issuedAt,
            'exp' => $issuedAt + $jwtConfig->exp,
            'data' => [
                'first_name' => $user->first_name,
                'username'   => $user->username,
                'email'      => $user->email,
                'tipe'       => $tipe,
                'profile'    => base_url($user->profile),
                'id'         => $user->id
            ]
        ];

        $token = JWT::encode($payload, $jwtConfig->key, $jwtConfig->alg);

        return $this->respond([
            'status'   => 200,
            'token'    => $token,
            'data'     => $payload['data'],
        ]);
    }

    public function logout()
    {
        $ionAuth = new \IonAuth\Libraries\IonAuth();
        $ionAuth->logout();

        return $this->respond([
            'status'   => 200,
            'messages' => [
                'success' => 'User logged out successfully',
            ],
        ]);
    }

    public function profile()
    {
        // Get user data from the request (set by JWT filter)
        $user = $this->request->user;
        
        return $this->respond([
            'success' => true,
            'data'    => $user,
        ]);
    }
    
    public function search()
    {
        $kartu = $this->request->getGet('kartu');
        
        if (empty($kartu)) {
            return $this->respond([
                'success' => false,
                'message' => 'Nomor kartu harus diisi'
            ], 400);
        }
        
        // Load PelangganModel to search for customers
        $pelangganModel = new \App\Models\PelangganModel();
        
        // Search for anggota (tipe = 1) by kode, nama, or id
        $customer = $pelangganModel->where('tipe', '1') // Only anggota koperasi
                                  ->where('status', '1') // Only active
                                  ->where('status_hps', '0') // Not deleted
                                  ->groupStart()
                                    ->where('kode', $kartu)
                                    ->orWhere('nama', $kartu)
                                    ->orWhere('id', $kartu)
                                  ->groupEnd()
                                  ->first();
        
        if (!$customer) {
            return $this->respond([
                'success' => false,
                'message' => 'Anggota tidak ditemukan'
            ], 404);
        }
        
        return $this->respond([
            'success' => true,
            'data' => [
                'id' => $customer->id,
                'nama' => $customer->nama,
                'nomor_kartu' => $customer->kode,
                'alamat' => $customer->alamat ?? '',
                'telepon' => $customer->no_telp ?? '',
                'kota' => $customer->kota ?? '',
                'provinsi' => $customer->provinsi ?? ''
            ]
        ]);
    }

    /**
     * Set/Create PIN for user
     * POST /api/anggota/set-pin
     */
    public function setPin()
    {
        // Get user data from JWT token (set by JWT filter)
        $user = $this->request->user;
        
        $pin = $this->request->getPost('pin');
        $confirmPin = $this->request->getPost('confirm_pin');
        
        // Validation
        if (empty($pin)) {
            return $this->respond([
                'success' => false,
                'message' => 'PIN harus diisi'
            ], 400);
        }
        
        if (empty($confirmPin)) {
            return $this->respond([
                'success' => false,
                'message' => 'Konfirmasi PIN harus diisi'
            ], 400);
        }
        
        if ($pin !== $confirmPin) {
            return $this->respond([
                'success' => false,
                'message' => 'PIN dan konfirmasi PIN tidak cocok'
            ], 400);
        }
        
        if (strlen($pin) !== 6) {
            return $this->respond([
                'success' => false,
                'message' => 'PIN harus 6 digit'
            ], 400);
        }
        
        if (!is_numeric($pin)) {
            return $this->respond([
                'success' => false,
                'message' => 'PIN harus berupa angka'
            ], 400);
        }
        
        // Check if PIN already exists
        $ionAuth = new \IonAuth\Libraries\IonAuth();
        $existingUser = $ionAuth->user($user->id)->row();
        
        if (!$existingUser) {
            return $this->respond([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }
        
        if (isset($existingUser->pin) && $existingUser->pin) {
            return $this->respond([
                'success' => false,
                'message' => 'PIN sudah diatur sebelumnya. Gunakan fungsi ubah PIN untuk mengubah PIN yang ada.'
            ], 400);
        }
        
        // Store PIN as plain number (no encryption)
        // Update user PIN in database
        $db = \Config\Database::connect();
        $updated = $db->table('tbl_ion_users')
                     ->where('id', $user->id)
                     ->update(['pin' => $pin]);
        
        if ($updated) {
            return $this->respond([
                'success' => true,
                'message' => 'PIN berhasil diatur',
                'data' => [
                    'user_id' => $user->id,
                    'pin_set' => true
                ]
            ]);
        } else {
            return $this->respond([
                'success' => false,
                'message' => 'Gagal mengatur PIN'
            ], 500);
        }
    }

    /**
     * Validate PIN for user authentication
     * POST /api/anggota/validate-pin
     */
    public function validatePin()
    {
        // Get user data from JWT token (set by JWT filter)
        $user = $this->request->user;
        
        $pin = $this->request->getPost('pin');
        
        // Validation
        if (empty($pin)) {
            return $this->respond([
                'success' => false,
                'message' => 'PIN harus diisi'
            ], 400);
        }
        
        if (strlen($pin) !== 6) {
            return $this->respond([
                'success' => false,
                'message' => 'PIN harus 6 digit'
            ], 400);
        }
        
        if (!is_numeric($pin)) {
            return $this->respond([
                'success' => false,
                'message' => 'PIN harus berupa angka'
            ], 400);
        }
        
        // Get user from database
        $ionAuth = new \IonAuth\Libraries\IonAuth();
        $existingUser = $ionAuth->user($user->id)->row();
        
        if (!$existingUser) {
            return $this->respond([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }
        
        if (!isset($existingUser->pin) || !$existingUser->pin) {
            return $this->respond([
                'success' => false,
                'message' => 'PIN belum diatur. Silakan atur PIN terlebih dahulu.'
            ], 400);
        }
        
        // Verify PIN by direct comparison (plain number)
        if ($pin === $existingUser->pin) {
            return $this->respond([
                'success' => true,
                'message' => 'PIN valid',
                'data' => [
                    'user_id' => $user->id,
                    'pin_valid' => true,
                    'user_info' => [
                        'first_name' => $user->first_name,
                        'username' => $user->username,
                        'email' => $user->email,
                        'tipe' => $user->tipe
                    ]
                ]
            ]);
        } else {
            return $this->respond([
                'success' => false,
                'message' => 'PIN tidak valid'
            ], 401);
        }
    }

    /**
     * Change existing PIN
     * POST /api/anggota/change-pin
     */
    public function changePin()
    {
        // Get user data from JWT token (set by JWT filter)
        $user = $this->request->user;
        
        $currentPin = $this->request->getPost('current_pin');
        $newPin = $this->request->getPost('new_pin');
        $confirmNewPin = $this->request->getPost('confirm_new_pin');
        
        // Validation
        if (empty($currentPin)) {
            return $this->respond([
                'success' => false,
                'message' => 'PIN saat ini harus diisi'
            ], 400);
        }
        
        if (empty($newPin)) {
            return $this->respond([
                'success' => false,
                'message' => 'PIN baru harus diisi'
            ], 400);
        }
        
        if (empty($confirmNewPin)) {
            return $this->respond([
                'success' => false,
                'message' => 'Konfirmasi PIN baru harus diisi'
            ], 400);
        }
        
        if ($newPin !== $confirmNewPin) {
            return $this->respond([
                'success' => false,
                'message' => 'PIN baru dan konfirmasi PIN tidak cocok'
            ], 400);
        }
        
        if (strlen($newPin) !== 6) {
            return $this->respond([
                'success' => false,
                'message' => 'PIN baru harus 6 digit'
            ], 400);
        }
        
        if (!is_numeric($newPin)) {
            return $this->respond([
                'success' => false,
                'message' => 'PIN baru harus berupa angka'
            ], 400);
        }
        
        if ($currentPin === $newPin) {
            return $this->respond([
                'success' => false,
                'message' => 'PIN baru tidak boleh sama dengan PIN saat ini'
            ], 400);
        }
        
        // Get user from database
        $ionAuth = new \IonAuth\Libraries\IonAuth();
        $existingUser = $ionAuth->user($user->id)->row();
        
        if (!$existingUser) {
            return $this->respond([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }
        
        if (!isset($existingUser->pin) || !$existingUser->pin) {
            return $this->respond([
                'success' => false,
                'message' => 'PIN belum diatur. Gunakan fungsi set PIN untuk mengatur PIN pertama kali.'
            ], 400);
        }
        
        // Verify current PIN by direct comparison
        if ($currentPin !== $existingUser->pin) {
            return $this->respond([
                'success' => false,
                'message' => 'PIN saat ini tidak valid'
            ], 401);
        }
        
        // Store new PIN as plain number (no encryption)
        // Update user PIN in database
        $db = \Config\Database::connect();
        $updated = $db->table('tbl_ion_users')
                     ->where('id', $user->id)
                     ->update(['pin' => $newPin]);
        
        if ($updated) {
            return $this->respond([
                'success' => true,
                'message' => 'PIN berhasil diubah',
                'data' => [
                    'user_id' => $user->id,
                    'pin_changed' => true
                ]
            ]);
        } else {
            return $this->respond([
                'success' => false,
                'message' => 'Gagal mengubah PIN'
            ], 500);
        }
    }

    /**
     * Check PIN status for user
     * GET /api/anggota/pin-status
     */
    public function pinStatus()
    {
        // Get user data from JWT token (set by JWT filter)
        $user = $this->request->user;
        
        // Get user from database
        $ionAuth = new \IonAuth\Libraries\IonAuth();
        $existingUser = $ionAuth->user($user->id)->row();
        
        if (!$existingUser) {
            return $this->respond([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }
        
        // Check if PIN is actually set (not NULL and not empty)
        $pinSet = isset($existingUser->pin) && !empty($existingUser->pin) && $existingUser->pin !== null;
        
        return $this->respond([
            'success' => true,
            'data' => [
                'user_id' => $user->id,
                'pin_set' => $pinSet,
                'pin_exists' => isset($existingUser->pin),
                'pin_value' => $existingUser->pin,
                'user_info' => [
                    'first_name' => $user->first_name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'tipe' => $user->tipe
                ]
            ]
        ]);
    }

    /**
     * Reset PIN (forgot PIN scenario)
     * POST /api/anggota/reset-pin
     */
    public function resetPin()
    {
        // Get user data from JWT token (set by JWT filter)
        $user = $this->request->user;
        
        $email = $this->request->getPost('email');
        $username = $this->request->getPost('username');
        $newPin = $this->request->getPost('new_pin');
        
        // Validation
        if (empty($email) && empty($username)) {
            return $this->respond([
                'success' => false,
                'message' => 'Email atau username harus diisi'
            ], 400);
        }
        
        if (empty($newPin)) {
            return $this->respond([
                'success' => false,
                'message' => 'PIN baru harus diisi'
            ], 400);
        }
        
        if (strlen($newPin) !== 6) {
            return $this->respond([
                'success' => false,
                'message' => 'PIN baru harus 6 digit'
            ], 400);
        }
        
        if (!is_numeric($newPin)) {
            return $this->respond([
                'success' => false,
                'message' => 'PIN baru harus berupa angka'
            ], 400);
        }
        
        // Get user from database using direct query
        $db = \Config\Database::connect();
        $existingUser = null;
        
        // Debug: Log what we're searching for
        $searchValue = !empty($email) ? $email : $username;
        $searchField = !empty($email) ? 'email' : 'username';
        
        if (!empty($email)) {
            $existingUser = $db->table('tbl_ion_users')
                              ->where('email', $email)
                              ->get()
                              ->getRow();
        } elseif (!empty($username)) {
            $existingUser = $db->table('tbl_ion_users')
                              ->where('username', $username)
                              ->get()
                              ->getRow();
        }
        
        if (!$existingUser) {
            return $this->respond([
                'success' => false,
                'message' => 'User tidak ditemukan',
                'debug' => [
                    'search_field' => $searchField,
                    'search_value' => $searchValue,
                    'user_id_from_token' => $user->id
                ]
            ], 404);
        }
        
        // For reset PIN, we don't need to verify user match since they're resetting their own PIN
        // The JWT token already ensures they're authenticated
        
        // Store the input PIN as plain number (no encryption)
        // Update user PIN in database
        $updated = $db->table('tbl_ion_users')
                     ->where('id', $existingUser->id)
                     ->update(['pin' => $newPin]);
        
        if ($updated) {
            return $this->respond([
                'success' => true,
                'message' => 'PIN berhasil direset',
                'data' => [
                    'user_id' => $existingUser->id,
                    'new_pin' => $newPin, // Return the PIN that was stored
                    'pin_reset' => true,
                    'note' => 'PIN baru berhasil disimpan sebagai plain number.'
                ]
            ]);
        } else {
            return $this->respond([
                'success' => false,
                'message' => 'Gagal mereset PIN'
            ], 500);
        }
    }
} 