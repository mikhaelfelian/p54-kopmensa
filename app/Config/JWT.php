<?php
namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-06-22
 * Github : github.com/mikhaelfelian
 * description : JWT configuration for authentication
 * This file represents the Config class for JWT.
 */
class JWT extends BaseConfig
{
    public $key;
    public $alg = 'HS256';
    public $exp = 3600; // token berlaku 1 jam

    public function __construct()
    {
        $this->key = getenv('JWT_SECRET_KEY');
    }
} 