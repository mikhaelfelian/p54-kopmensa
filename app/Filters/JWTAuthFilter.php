<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
use Config\JWT as JWTConfig;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-22
 * Github : github.com/mikhaelfelian
 * description : JWT Authentication filter for API endpoints
 * This file represents the Filter class for JWT Authentication.
 */
class JWTAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $header = $request->getHeader('Authorization');
        
        if (!$header) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON(['error' => 'Token tidak ditemukan']);
        }

        $token = str_replace('Bearer ', '', $header->getValue());
        
        if (empty($token)) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON(['error' => 'Token tidak valid']);
        }

        try {
            $jwtConfig = new JWTConfig();
            $decoded = JWT::decode($token, new Key($jwtConfig->key, $jwtConfig->alg));
            
            // Store user data in request for later use
            $request->user = $decoded->data;
            
        } catch (\Exception $e) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON(['error' => 'Token tidak valid atau expired']);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing after the request
    }
} 