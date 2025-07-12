<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddReceivingFieldsToTransBeli extends Migration
{
    public function up()
    {
        // Add receiving fields to tbl_trans_beli
        $this->forge->addColumn('tbl_trans_beli', [
            'status_terima' => [
                'type'       => 'ENUM',
                'constraint' => ['0','1'],
                'null'       => true,
                'default'    => '0',
                'comment'    => '0=Belum diterima, 1=Sudah diterima',
                'after'      => 'status_hps'
            ],
            'tgl_terima' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Tanggal penerimaan barang',
                'after' => 'status_terima'
            ],
            'catatan_terima' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Catatan saat penerimaan barang',
                'after' => 'tgl_terima'
            ],
            'id_user_terima' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'comment'    => 'ID user yang menerima barang',
                'after'      => 'catatan_terima'
            ]
        ]);

        // Add receiving fields to tbl_trans_beli_det
        $this->forge->addColumn('tbl_trans_beli_det', [
            'id_gudang' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'comment'    => 'ID gudang untuk penerimaan barang',
                'after'      => 'status_item'
            ],
            'status_terima' => [
                'type'       => 'ENUM',
                'constraint' => ['1','2','3'],
                'null'       => true,
                'comment'    => '1=Diterima, 2=Ditolak, 3=Sebagian',
                'after'      => 'id_gudang'
            ],
            'keterangan_terima' => [
                'type'       => 'TEXT',
                'null'       => true,
                'comment'    => 'Keterangan saat penerimaan',
                'after'      => 'status_terima'
            ]
        ]);
    }

    public function down()
    {
        // Remove columns from tbl_trans_beli
        $this->forge->dropColumn('tbl_trans_beli', ['status_terima', 'tgl_terima', 'catatan_terima', 'id_user_terima']);
        
        // Remove columns from tbl_trans_beli_det
        $this->forge->dropColumn('tbl_trans_beli_det', ['id_gudang', 'status_terima', 'keterangan_terima']);
    }
}
