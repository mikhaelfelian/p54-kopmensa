<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-05-26
 * This file represents the seeder for tbl_pengaturan_api table.
 */
class PengaturanApiSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id_pengaturan' => 1, // Assuming this ID exists in tbl_pengaturan
                'nama'          => 'Recaptcha 3',
                'pub_key'       => null,
                'priv_key'      => null,
                'status'        => '1'
            ],
            [
                'id_pengaturan' => 1, // Assuming this ID exists in tbl_pengaturan
                'nama'          => 'Chat GPT',
                'pub_key'       => null,
                'priv_key'      => null,
                'status'        => '1'
            ]
        ];

        $this->db->table('tbl_pengaturan_api')->insertBatch($data);
    }
} 