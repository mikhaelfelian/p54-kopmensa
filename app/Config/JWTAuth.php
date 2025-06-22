<?php
namespace Config;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Config\JWT as JWTConfig;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2024-06-09
 * Github : github.com/mikhaelfelian
 * description : JWT Authentication helper using firebase/php-jwt
 * This file represents the Libraries class for JWTAuth.
 */
class JWTAuth
{
    protected $config;

    public function __construct()
    {
        $this->config = new JWTConfig();
    }

    public function encode(array $payload)
    {
        $issuedAt = time();
        $expire = $issuedAt + $this->config->exp;
        $payload = array_merge($payload, [
            'iat' => $issuedAt,
            'exp' => $expire,
        ]);
        return JWT::encode($payload, $this->config->key, $this->config->alg);
    }

    public function decode($jwt)
    {
        return JWT::decode($jwt, new Key($this->config->key, $this->config->alg));
    }
} 