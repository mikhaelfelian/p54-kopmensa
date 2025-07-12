<?php
/**
 * Pengaturan Controller
 * 
 * Controller for managing application settings (pengaturan)
 * Handles CRUD operations and other related functionalities
 * 
 * @author    Mikhael Felian Waskito <mikhaelfelian@gmail.com>
 * @date      2025-01-12
 */

namespace App\Controllers;

use App\Models\PengaturanModel;

class Pengaturan extends BaseController
{
    protected $pengaturanModel;
    protected $validation;

    public function __construct()
    {
        $this->pengaturanModel = new PengaturanModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $data = [
            'title'         => 'Pengaturan Aplikasi',
            'Pengaturan'    => $this->pengaturan,
            'user'          => $this->ionAuth->user()->row(),
            'validation'    => $this->validation
        ];

        return view($this->theme->getThemePath() . '/pengaturan/app/form_pengaturan', $data);
    }

    public function update()
    {
        // Validate input
        if (!$this->validate([
            'csrf_test_name' => 'required',
            'judul' => [
                'rules'  => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required'   => 'Judul Perusahaan harus diisi',
                    'min_length' => 'Judul Perusahaan minimal 3 karakter',
                    'max_length' => 'Judul Perusahaan maksimal 100 karakter'
                ]
            ],
            'judul_app' => [
                'rules'  => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required'   => 'Judul Aplikasi harus diisi',
                    'min_length' => 'Judul Aplikasi minimal 3 karakter',
                    'max_length' => 'Judul Aplikasi maksimal 100 karakter'
                ]
            ],
            'alamat' => [
                'rules'  => 'required|min_length[5]',
                'errors' => [
                    'required'   => 'Alamat Perusahaan harus diisi',
                    'min_length' => 'Alamat Perusahaan minimal 5 karakter'
                ]
            ],
            'kota' => [
                'rules'  => 'required|min_length[2]|max_length[100]',
                'errors' => [
                    'required'   => 'Kota harus diisi',
                    'min_length' => 'Kota minimal 2 karakter',
                    'max_length' => 'Kota maksimal 100 karakter'
                ]
            ],
            'deskripsi' => [
                'rules'  => 'required|min_length[10]',
                'errors' => [
                    'required'   => 'Deskripsi Aplikasi harus diisi',
                    'min_length' => 'Deskripsi Aplikasi minimal 10 karakter'
                ]
            ],
            'logo_header' => [
                'rules'  => 'permit_empty|is_image[logo_header]|mime_in[logo_header,image/jpg,image/jpeg,image/png]|max_size[logo_header,2048]',
                'errors' => [
                    'is_image'  => 'File harus berupa gambar',
                    'mime_in'   => 'Format file harus JPG, JPEG, atau PNG',
                    'max_size'  => 'Ukuran file maksimal 2MB'
                ]
            ],
            'favicon' => [
                'rules'  => 'permit_empty|is_image[favicon]|mime_in[favicon,image/x-icon,image/png]|max_size[favicon,1024]',
                'errors' => [
                    'is_image'  => 'File harus berupa gambar',
                    'mime_in'   => 'Format file harus ICO atau PNG',
                    'max_size'  => 'Ukuran file maksimal 1MB'
                ]
            ]
        ])) {
            return redirect()->back()->withInput()->with('toastr', [
                'type' => 'error',
                'message' => $this->validation->getErrors()
            ]);
        }

        // Handle file uploads
        $logo_header = $this->request->getFile('logo_header');
        $favicon = $this->request->getFile('favicon');

        $data = [
            'judul' => $this->request->getPost('judul'),
            'judul_app' => $this->request->getPost('judul_app'),
            'alamat' => $this->request->getPost('alamat'),
            'kota' => $this->request->getPost('kota'),
            'apt_apa' => $this->request->getPost('apt_apa'),
            'apt_sipa' => $this->request->getPost('apt_sipa'),
            'deskripsi' => $this->request->getPost('deskripsi')
        ];

        // Process logo header upload
        if ($logo_header->isValid() && !$logo_header->hasMoved()) {
            $newName = $logo_header->getRandomName();
            $logo_header->move(FCPATH . 'public/assets/img', $newName);
            $data['logo_header'] = 'public/assets/img/' . $newName;
            
            // Delete old file
            if ($this->pengaturan->logo_header && file_exists(FCPATH . $this->pengaturan->logo_header)) {
                unlink(FCPATH . $this->pengaturan->logo_header);
            }
        }

        // Process favicon upload
        if ($favicon->isValid() && !$favicon->hasMoved()) {
            $newName = $favicon->getRandomName();
            $favicon->move(FCPATH . 'public/assets/img', $newName);
            $data['favicon'] = 'public/assets/img/' . $newName;
            
            // Delete old file
            if ($this->pengaturan->favicon && file_exists(FCPATH . $this->pengaturan->favicon)) {
                unlink(FCPATH . $this->pengaturan->favicon);
            }
        }

        // Update settings
        if ($this->pengaturanModel->update(1, $data)) {
            return redirect()->back()->with('toastr', [
                'type' => 'success',
                'message' => 'Pengaturan berhasil diupdate'
            ]);
        }

        return redirect()->back()->withInput()->with('toastr', [
            'type' => 'error',
            'message' => 'Gagal mengupdate pengaturan'
        ]);
    }
} 