<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblPelangganPoinLog extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('tbl_pelanggan_poin_log')) {
            return;
        }

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_penjualan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'id_pelanggan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'poin' => [
                'type'       => 'DECIMAL',
                'constraint' => '14,2',
                'default'    => '0.00',
                'null'       => false,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('id_penjualan');
        $this->forge->addKey('id_pelanggan');
        $this->forge->createTable('tbl_pelanggan_poin_log', true);
    }

    public function down()
    {
        if ($this->db->tableExists('tbl_pelanggan_poin_log')) {
            $this->forge->dropTable('tbl_pelanggan_poin_log', true);
        }
    }
}
