<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PengaturanSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'judul'            => 'KOPERASI KARYAWAN SYARIAH SULTAN AGUNG',
            'judul_app'        => 'KOPMENSA PO',
            'alamat'           => 'Jl. Sultan Agung No. 1, Kota Semarang',
            'deskripsi'        => 'Sistem informasi manajemen penjualan dan pembelian',
            'kota'             => 'Semarang',
            'url'              => 'http://localhost/p54-kopmensa',
            'theme'            => 'quirk',
            'pagination_limit' => 10,
            'favicon'          => 'favicon.ico',
            'logo'            => 'logo.png',
            'logo_header'     => 'logo_header.png',
            'apt_apa'         => 'APT. UNGSARI RIZKI EKA PURWANTO, M.SC',
            'apt_sipa'        => '449.1/61/DPM-PTSP/SIPA/II/2022',
            'ppn'             => 11,
        ];

        // Check if data exists
        $exists = $this->db->table('tbl_pengaturan')->get()->getRow();
        
        if (!$exists) {
            $this->db->table('tbl_pengaturan')->insert($data);
        }
    }
} 