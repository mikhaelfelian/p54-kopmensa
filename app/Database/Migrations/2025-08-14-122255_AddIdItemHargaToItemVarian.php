<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIdItemHargaToItemVarian extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tbl_m_item_varian', [
            'id_item_harga' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'default'    => null,
                'after'      => 'id_item'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_m_item_varian', 'id_item_harga');
    }
}
