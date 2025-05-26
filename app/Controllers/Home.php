<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        
        $data = [
            'title'         => 'Dashboard',
            'Pengaturan'    => $this->pengaturan,
            'total_users'   => 1
        ];

        return view($this->theme->getThemePath() . '/index', $data);
    }
}
