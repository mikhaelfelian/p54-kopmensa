<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterTblMPelangganStatusblokirLimit extends Migration
{
    public function up()
    {
        $fields = [
            'status_blokir' => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1'],
                'default'    => '0',
                'null'       => true,
                'after'      => 'status_hps',
                'comment'    => '0=tidak diblokir; 1=diblokir',
            ],
            'limit' => [
                'type'       => 'FLOAT',
                'constraint' => '10,2',
                'default'    => 0,
                'null'       => true,
                'after'      => 'status_blokir',
            ],
        ];
        $this->forge->addColumn('tbl_m_pelanggan', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_m_pelanggan', 'status_blokir');
        $this->forge->dropColumn('tbl_m_pelanggan', 'limit');
    }
} 