<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class VarianSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'kode' => 'VAR001',
                'nama' => 'Warna Merah',
                'keterangan' => 'Varian warna merah untuk produk',
                'status' => '1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'kode' => 'VAR002',
                'nama' => 'Warna Biru',
                'keterangan' => 'Varian warna biru untuk produk',
                'status' => '1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'kode' => 'VAR003',
                'nama' => 'Ukuran S',
                'keterangan' => 'Varian ukuran kecil (Small)',
                'status' => '1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'kode' => 'VAR004',
                'nama' => 'Ukuran M',
                'keterangan' => 'Varian ukuran sedang (Medium)',
                'status' => '1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'kode' => 'VAR005',
                'nama' => 'Ukuran L',
                'keterangan' => 'Varian ukuran besar (Large)',
                'status' => '1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'kode' => 'VAR006',
                'nama' => 'Rasa Coklat',
                'keterangan' => 'Varian rasa coklat untuk makanan',
                'status' => '1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'kode' => 'VAR007',
                'nama' => 'Rasa Vanilla',
                'keterangan' => 'Varian rasa vanilla untuk makanan',
                'status' => '1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'kode' => 'VAR008',
                'nama' => 'Material Katun',
                'keterangan' => 'Varian material katun untuk pakaian',
                'status' => '1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'kode' => 'VAR009',
                'nama' => 'Material Polyester',
                'keterangan' => 'Varian material polyester untuk pakaian',
                'status' => '1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'kode' => 'VAR010',
                'nama' => 'Inactive Test',
                'keterangan' => 'Varian tidak aktif untuk testing',
                'status' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('tbl_m_varian')->insertBatch($data);
    }
}
