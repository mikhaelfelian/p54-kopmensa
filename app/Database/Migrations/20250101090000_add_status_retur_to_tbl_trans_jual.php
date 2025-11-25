<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusReturToTblTransJual extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('status_retur', 'tbl_trans_jual')) {
            $fields = [
                'status_retur' => [
                    'type'    => "ENUM('0','1','2')",
                    'default' => '0',
                    'after'   => 'status_bayar'
                ],
            ];

            $this->forge->addColumn('tbl_trans_jual', $fields);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('status_retur', 'tbl_trans_jual')) {
            $this->forge->dropColumn('tbl_trans_jual', 'status_retur');
        }
    }
}

