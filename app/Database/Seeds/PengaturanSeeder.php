<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PengaturanSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'judul'            => 'KOPMENSA',
            'judul_app'        => 'KOPMENSA POS',
            'alamat'           => 'Jl. Raya No. 1',
            'deskripsi'        => 'sistem informasi manajemen penjualan',
            'kota'             => 'semarang',
            'url'              => 'http://localhost/p54-kopmensa',
            'theme'            => 'quirk',
            'pagination_limit' => 10,
            'favicon'          => 'favicon.ico',
            'logo'            => 'logo.png',
            'logo_header'     => 'logo_header.png',
            'ppn'             => 11,
        ];

        // Check if data exists
        $exists = $this->db->table('tbl_pengaturan')->get()->getRow();
        
        if (!$exists) {
            $this->db->table('tbl_pengaturan')->insert($data);
        }
    }
} 