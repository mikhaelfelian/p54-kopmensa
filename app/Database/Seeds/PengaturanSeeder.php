<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PengaturanSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'judul'            => 'SIMEDIS',
            'judul_app'        => 'MEDKIT 3',
            'alamat'           => 'Perum Mutiara Pandanaran Blok D11, Mangunharjo, Tembalang, Semarang',
            'deskripsi'        => 'Sistem Informasi Manajemen Rumah Sakit',
            'kota'             => 'Jakarta',
            'url'              => 'http://localhost/medkit3-v2',
            'theme'            => 'admin-lte-3',
            'pagination_limit' => 10,
            'favicon'          => 'favicon.ico',
            'logo'            => 'logo.png',
            'logo_header'     => 'logo_header.png',
            'apt_apa'         => 'APA123456',
            'apt_sipa'        => 'SIPA123456',
            'ppn'             => 11,
        ];

        // Check if data exists
        $exists = $this->db->table('tbl_pengaturan')->get()->getRow();
        
        if (!$exists) {
            $this->db->table('tbl_pengaturan')->insert($data);
        }
    }
} 