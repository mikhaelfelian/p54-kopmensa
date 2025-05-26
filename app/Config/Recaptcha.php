<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-05-26
 * This file represents the Recaptcha configuration.
 */
class Recaptcha extends BaseConfig
{
    public $siteKey = '';
    public $secretKey = '';

    public function __construct()
    {
        parent::__construct();
        
        // Get keys from database
        $apiModel = model('PengaturanApiModel');
        $recaptcha = $apiModel->getRecaptchaKeys();
        
        if ($recaptcha) {
            $this->siteKey = $recaptcha['pub_key'];
            $this->secretKey = $recaptcha['priv_key'];
        }
    }
} 