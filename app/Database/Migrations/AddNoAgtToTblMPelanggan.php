<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNoAgtToTblMPelanggan extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        $query = $db->query("SHOW COLUMNS FROM tbl_m_pelanggan LIKE 'no_agt'");
        
        if ($query->getNumRows() === 0) {
            $fields = [
                'no_agt' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                    'comment'    => 'Nomor Anggota',
                    'after'      => 'kode'
                ],
            ];
            $this->forge->addColumn('tbl_m_pelanggan', $fields);
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        $query = $db->query("SHOW COLUMNS FROM tbl_m_pelanggan LIKE 'no_agt'");
        
        if ($query->getNumRows() > 0) {
            $this->forge->dropColumn('tbl_m_pelanggan', 'no_agt');
        }
    }
}

