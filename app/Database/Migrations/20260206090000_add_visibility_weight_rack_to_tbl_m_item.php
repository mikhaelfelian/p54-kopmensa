<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVisibilityWeightRackToTblMItem extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tbl_m_item', [
            'is_visible' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => false,
                'default' => 1,
            ],
            'berat' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
                'null' => true,
                'default' => null,
            ],
            'lokasi_rak' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
                'default' => null,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_m_item', 'is_visible');
        $this->forge->dropColumn('tbl_m_item', 'berat');
        $this->forge->dropColumn('tbl_m_item', 'lokasi_rak');
    }
}

