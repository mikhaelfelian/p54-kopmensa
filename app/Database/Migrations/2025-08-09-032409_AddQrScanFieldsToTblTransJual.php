<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddQrScanFieldsToTblTransJual extends Migration
{
    public function up()
    {
        $fields = [
            'qr_scanned' => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1'],
                'default'    => '0',
                'after'      => 'status',
            ],
            'qr_scan_time' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'qr_scanned',
            ],
        ];

        $this->forge->addColumn('tbl_trans_jual', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_trans_jual', ['qr_scanned', 'qr_scan_time']);
    }
}
