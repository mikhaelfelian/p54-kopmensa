<?php

namespace App\Controllers\Pengaturan;

use App\Controllers\BaseController;
use App\Models\IonModulesModel;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-05-30
 * This file represents the Modules Controller.
 */
class Modules extends BaseController
{
    protected $ionModulesModel;

    public function __construct()
    {
        $this->ionModulesModel = new IonModulesModel();
    }

    public function index()
    {
        $data = [
            'title'         => 'Modules Management',
            'modules'       => $this->ionModulesModel->findAll(),
            'user'          => $this->ionAuth->user()->row(),
            'validation'    => $this->validation,
            'Pengaturan'    => $this->pengaturan
        ];

        return view($this->theme->getThemePath() . '/pengaturan/modules/index', $data);
    }
} 