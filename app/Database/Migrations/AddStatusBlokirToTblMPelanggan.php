<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusBlokirToTblMPelanggan extends Migration
{
    public function up()
    {
        // Add status_blokir if not exists
        $fields = [
            'status_blokir' => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1'],
                'default'    => '0',
                'comment'    => '0=aktif, 1=diblokir',
                'after'      => 'status'
            ],
        ];

        // Check if column exists using query
        $db = \Config\Database::connect();
        $query = $db->query("SHOW COLUMNS FROM tbl_m_pelanggan LIKE 'status_blokir'");
        
        if ($query->getNumRows() === 0) {
            $this->forge->addColumn('tbl_m_pelanggan', $fields);
        }
    }

    public function down()
    {
        // Check if column exists before dropping
        $db = \Config\Database::connect();
        $query = $db->query("SHOW COLUMNS FROM tbl_m_pelanggan LIKE 'status_blokir'");
        
        if ($query->getNumRows() > 0) {
            $this->forge->dropColumn('tbl_m_pelanggan', 'status_blokir');
        }
    }
}


