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
} 