<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PengaturanThemeSeeder extends Seeder
{
    public function run()
    {
        // Get the first pengaturan id
        $pengaturan = $this->db->table('tbl_pengaturan')->get()->getRow();
        
        if ($pengaturan) {
            $data = [
                'id_pengaturan' => $pengaturan->id,
                'nama'          => 'AdminLTE 3',
                'path'          => 'admin-lte-3',
                'status'        => 1,
            ];
            
            // Check if theme already exists
            $exists = $this->db->table('tbl_pengaturan_theme')
                ->where('id_pengaturan', $data['id_pengaturan'])
                ->where('path', $data['path'])
                ->get()
                ->getRow();

            // Only insert if theme doesn't exist
            if (!$exists) {
                $this->db->table('tbl_pengaturan_theme')->insert($data);
            }
        }
    }
} 