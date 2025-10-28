<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPpnAmountToTblTransJualDet extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        $query = $db->query("SHOW COLUMNS FROM tbl_trans_jual_det LIKE 'ppn_amount'");
        
        if ($query->getNumRows() === 0) {
            $fields = [
                'ppn_amount' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '12,2',
                    'default'    => 0,
                    'null'       => false,
                    'comment'    => 'PPN amount for this line item',
                    'after'      => 'subtotal'
                ],
            ];
            $this->forge->addColumn('tbl_trans_jual_det', $fields);
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        $query = $db->query("SHOW COLUMNS FROM tbl_trans_jual_det LIKE 'ppn_amount'");
        
        if ($query->getNumRows() > 0) {
            $this->forge->dropColumn('tbl_trans_jual_det', 'ppn_amount');
        }
    }
}

