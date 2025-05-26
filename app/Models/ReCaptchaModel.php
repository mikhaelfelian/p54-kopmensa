<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-05-26
 * This file represents the ReCaptcha model.
 */
class ReCaptchaModel
{
    protected $apiModel;
    protected $secretKey;
    protected $siteKey;

    public function __construct()
    {
        $this->apiModel = model('PengaturanApiModel');
        $this->loadKeys();
    }

    protected function loadKeys()
    {
        $recaptcha = $this->apiModel->getRecaptchaKeys();
        if ($recaptcha) {
            $this->siteKey = $recaptcha['pub_key'];
            $this->secretKey = $recaptcha['priv_key'];
        }
    }

    public function getSecretKey()
    {
        return $this->secretKey;
    }

    public function getSiteKey()
    {
        return $this->siteKey;
    }
} 