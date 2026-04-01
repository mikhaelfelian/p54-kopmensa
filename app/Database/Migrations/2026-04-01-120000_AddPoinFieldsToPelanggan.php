<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPoinFieldsToPelanggan extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('tbl_m_pelanggan')) {
            return;
        }
        $fields = $this->db->getFieldData('tbl_m_pelanggan');
        $names = array_column($fields, 'name');
        if (! in_array('poin', $names, true)) {
            $this->forge->addColumn('tbl_m_pelanggan', [
                'poin' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '14,2',
                    'default'    => '0.00',
                    'null'       => false,
                ],
            ]);
        }
        $fields = $this->db->getFieldData('tbl_m_pelanggan');
        $names = array_column($fields, 'name');
        if (! in_array('poin_updated_at', $names, true)) {
            $this->forge->addColumn('tbl_m_pelanggan', [
                'poin_updated_at' => [
                    'type'    => 'DATETIME',
                    'null'    => true,
                    'default' => null,
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('tbl_m_pelanggan')) {
            if ($this->db->fieldExists('poin_updated_at', 'tbl_m_pelanggan')) {
                $this->forge->dropColumn('tbl_m_pelanggan', 'poin_updated_at');
            }
            if ($this->db->fieldExists('poin', 'tbl_m_pelanggan')) {
                $this->forge->dropColumn('tbl_m_pelanggan', 'poin');
            }
        }
    }
}
