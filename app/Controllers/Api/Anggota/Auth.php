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
            // Since this is an API, we get the last error message for a cleaner response.
            $errorMessage = !empty($errors) ? end($errors) : 'Login failed';
            return $this->failUnauthorized($errorMessage);
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
            return $this->failValidationError('Nomor kartu harus diisi');
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
            return $this->failNotFound('Anggota tidak ditemukan');
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
            return $this->failValidationError('PIN harus diisi');
        }
        
        if (empty($confirmPin)) {
            return $this->failValidationError('Konfirmasi PIN harus diisi');
        }
        
        if ($pin !== $confirmPin) {
            return $this->failValidationError('PIN dan konfirmasi PIN tidak cocok');
        }
        
        if (strlen($pin) !== 6) {
            return $this->failValidationError('PIN harus 6 digit');
        }
        
        if (!is_numeric($pin)) {
            return $this->failValidationError('PIN harus berupa angka');
        }
        
        // Check if PIN already exists
        $ionAuth = new \IonAuth\Libraries\IonAuth();
        $existingUser = $ionAuth->user($user['id'])->row();
        
        if ($existingUser->pin) {
            return $this->failValidationError('PIN sudah diatur sebelumnya. Gunakan fungsi ubah PIN untuk mengubah PIN yang ada.');
        }
        
        // Hash the PIN for security
        $hashedPin = password_hash($pin, PASSWORD_DEFAULT);
        
        // Update user PIN in database
        $db = \Config\Database::connect();
        $updated = $db->table('tbl_ion_users')
                     ->where('id', $user['id'])
                     ->update(['pin' => $hashedPin]);
        
        if ($updated) {
            return $this->respond([
                'success' => true,
                'message' => 'PIN berhasil diatur',
                'data' => [
                    'user_id' => $user['id'],
                    'pin_set' => true
                ]
            ]);
        } else {
            return $this->failServerError('Gagal mengatur PIN');
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
            return $this->failValidationError('PIN harus diisi');
        }
        
        if (strlen($pin) !== 6) {
            return $this->failValidationError('PIN harus 6 digit');
        }
        
        if (!is_numeric($pin)) {
            return $this->failValidationError('PIN harus berupa angka');
        }
        
        // Get user from database
        $ionAuth = new \IonAuth\Libraries\IonAuth();
        $existingUser = $ionAuth->user($user['id'])->row();
        
        if (!$existingUser->pin) {
            return $this->failValidationError('PIN belum diatur. Silakan atur PIN terlebih dahulu.');
        }
        
        // Verify PIN
        if (password_verify($pin, $existingUser->pin)) {
            return $this->respond([
                'success' => true,
                'message' => 'PIN valid',
                'data' => [
                    'user_id' => $user['id'],
                    'pin_valid' => true,
                    'user_info' => [
                        'first_name' => $user['first_name'],
                        'username' => $user['username'],
                        'email' => $user['email'],
                        'tipe' => $user['tipe']
                    ]
                ]
            ]);
        } else {
            return $this->failUnauthorized('PIN tidak valid');
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
            return $this->failValidationError('PIN saat ini harus diisi');
        }
        
        if (empty($newPin)) {
            return $this->failValidationError('PIN baru harus diisi');
        }
        
        if (empty($confirmNewPin)) {
            return $this->failValidationError('Konfirmasi PIN baru harus diisi');
        }
        
        if ($newPin !== $confirmNewPin) {
            return $this->failValidationError('PIN baru dan konfirmasi PIN tidak cocok');
        }
        
        if (strlen($newPin) !== 6) {
            return $this->failValidationError('PIN baru harus 6 digit');
        }
        
        if (!is_numeric($newPin)) {
            return $this->failValidationError('PIN baru harus berupa angka');
        }
        
        if ($currentPin === $newPin) {
            return $this->failValidationError('PIN baru tidak boleh sama dengan PIN saat ini');
        }
        
        // Get user from database
        $ionAuth = new \IonAuth\Libraries\IonAuth();
        $existingUser = $ionAuth->user($user['id'])->row();
        
        if (!$existingUser->pin) {
            return $this->failValidationError('PIN belum diatur. Gunakan fungsi set PIN untuk mengatur PIN pertama kali.');
        }
        
        // Verify current PIN
        if (!password_verify($currentPin, $existingUser->pin)) {
            return $this->failUnauthorized('PIN saat ini tidak valid');
        }
        
        // Hash the new PIN
        $hashedNewPin = password_hash($newPin, PASSWORD_DEFAULT);
        
        // Update user PIN in database
        $db = \Config\Database::connect();
        $updated = $db->table('tbl_ion_users')
                     ->where('id', $user['id'])
                     ->update(['pin' => $hashedNewPin]);
        
        if ($updated) {
            return $this->respond([
                'success' => true,
                'message' => 'PIN berhasil diubah',
                'data' => [
                    'user_id' => $user['id'],
                    'pin_changed' => true
                ]
            ]);
        } else {
            return $this->failServerError('Gagal mengubah PIN');
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
        $existingUser = $ionAuth->user($user['id'])->row();
        
        return $this->respond([
            'success' => true,
            'data' => [
                'user_id' => $user['id'],
                'pin_set' => !empty($existingUser->pin),
                'user_info' => [
                    'first_name' => $user['first_name'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'tipe' => $user['tipe']
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
        
        // Validation
        if (empty($email) && empty($username)) {
            return $this->failValidationError('Email atau username harus diisi');
        }
        
        // Get user from database
        $ionAuth = new \IonAuth\Libraries\IonAuth();
        $existingUser = null;
        
        if (!empty($email)) {
            $existingUser = $ionAuth->user($email)->row();
        } elseif (!empty($username)) {
            $existingUser = $ionAuth->user($username)->row();
        }
        
        if (!$existingUser) {
            return $this->failNotFound('User tidak ditemukan');
        }
        
        // Verify that the requesting user matches the user being reset
        if ($existingUser->id != $user['id']) {
            return $this->failForbidden('Tidak dapat mereset PIN user lain');
        }
        
        // Generate new random PIN
        $newPin = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Hash the new PIN
        $hashedNewPin = password_hash($newPin, PASSWORD_DEFAULT);
        
        // Update user PIN in database
        $db = \Config\Database::connect();
        $updated = $db->table('tbl_ion_users')
                     ->where('id', $existingUser->id)
                     ->update(['pin' => $hashedNewPin]);
        
        if ($updated) {
            return $this->respond([
                'success' => true,
                'message' => 'PIN berhasil direset',
                'data' => [
                    'user_id' => $existingUser->id,
                    'new_pin' => $newPin, // Return plain PIN for user to see
                    'pin_reset' => true,
                    'note' => 'Simpan PIN baru ini dengan aman. PIN akan di-hash setelah digunakan.'
                ]
            ]);
        } else {
            return $this->failServerError('Gagal mereset PIN');
        }
    }
} 