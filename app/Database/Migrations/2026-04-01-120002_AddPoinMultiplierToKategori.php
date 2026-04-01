<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPoinMultiplierToKategori extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('tbl_m_kategori')) {
            return;
        }
        $fields = $this->db->getFieldData('tbl_m_kategori');
        $names = array_column($fields, 'name');
        if (! in_array('poin_multiplier', $names, true)) {
            $this->forge->addColumn('tbl_m_kategori', [
                'poin_multiplier' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '10,4',
                    'default'    => '1.0000',
                    'null'       => false,
                    'comment'    => 'Pengali poin loyalitas per kategori',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('tbl_m_kategori') && $this->db->fieldExists('poin_multiplier', 'tbl_m_kategori')) {
            $this->forge->dropColumn('tbl_m_kategori', 'poin_multiplier');
        }
    }
}
