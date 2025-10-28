<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKategoriToSupplier extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tbl_m_supplier', [
            'kategori' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'tipe',
                'comment' => 'perorangan/pabrikan'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_m_supplier', 'kategori');
    }
}
