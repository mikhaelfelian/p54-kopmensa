<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIsStockableToTblMItem extends Migration
{
    public function up()
    {
        $fields = [
            'is_stockable' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'null'       => false,
                'comment'    => '1=stock item, 0=non-stock/service',
                'after'      => 'status_hps'
            ],
        ];

        $db = \Config\Database::connect();
        $query = $db->query("SHOW COLUMNS FROM tbl_m_item LIKE 'is_stockable'");
        if ($query->getNumRows() === 0) {
            $this->forge->addColumn('tbl_m_item', $fields);
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        $query = $db->query("SHOW COLUMNS FROM tbl_m_item LIKE 'is_stockable'");
        if ($query->getNumRows() > 0) {
            $this->forge->dropColumn('tbl_m_item', 'is_stockable');
        }
    }
}


