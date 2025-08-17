<?php

namespace App\Controllers\Pengaturan;

use App\Controllers\BaseController;

/**
 * PU Menu Controller
 * 
 * Controller for managing Public User Menu settings
 * 
 * @author    Mikhael Felian Waskito <mikhaelfelian@gmail.com>
 * @date      2025-01-16
 */
class PuMenu extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data = [
            'title' => 'PU Menu',
            'user' => $this->ionAuth->user()->row(),
            'Pengaturan' => $this->pengaturan
        ];

        return view('admin-lte-3/pengaturan/pu_menu/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah PU Menu',
            'user' => $this->ionAuth->user()->row(),
            'Pengaturan' => $this->pengaturan
        ];

        return view('admin-lte-3/pengaturan/pu_menu/form', $data);
    }

    public function store()
    {
        // TODO: Implement store logic
        return redirect()->to('pengaturan/pu-menu')->with('success', 'PU Menu berhasil ditambahkan');
    }

    public function edit($id = null)
    {
        $data = [
            'title' => 'Edit PU Menu',
            'user' => $this->ionAuth->user()->row(),
            'Pengaturan' => $this->pengaturan
        ];

        return view('admin-lte-3/pengaturan/pu_menu/form', $data);
    }

    public function update($id = null)
    {
        // TODO: Implement update logic
        return redirect()->to('pengaturan/pu-menu')->with('success', 'PU Menu berhasil diupdate');
    }

    public function delete($id = null)
    {
        // TODO: Implement update logic
        return redirect()->to('pengaturan/pu-menu')->with('success', 'PU Menu berhasil dihapus');
    }
}
