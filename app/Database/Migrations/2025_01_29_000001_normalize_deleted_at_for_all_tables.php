<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class NormalizeDeletedAtForAllTables extends Migration
{
    public function up()
    {
        $masterTables = [
            'tbl_m_item',
            'tbl_m_supplier',
            'tbl_m_pelanggan',
            'tbl_m_pelanggan_grup',
            'tbl_m_karyawan',
            'tbl_m_varian',
            'tbl_m_kategori',
            'tbl_m_merk',
            'tbl_m_outlet',
            'tbl_m_gudang',
            'tbl_m_satuan',
            'tbl_m_platform',
            'tbl_m_voucher',
        ];

        foreach ($masterTables as $table) {
            if ($this->db->tableExists($table)) {
                if (! $this->db->fieldExists('deleted_at', $table)) {
                    $this->forge->addColumn($table, [
                        'deleted_at' => [
                            'type'    => 'DATETIME',
                            'null'    => true,
                            'default' => null,
                            'after'   => 'updated_at',
                        ],
                    ]);
                    
                    log_message('info', "Added deleted_at column to {$table}");
                } else {
                    log_message('info', "Column deleted_at already exists in {$table}");
                }
            } else {
                log_message('warning', "Table {$table} does not exist - skipping");
            }
        }
    }

    public function down()
    {
        $masterTables = [
            'tbl_m_item',
            'tbl_m_supplier',
            'tbl_m_pelanggan',
            'tbl_m_pelanggan_grup',
            'tbl_m_karyawan',
            'tbl_m_varian',
            'tbl_m_kategori',
            'tbl_m_merk',
            'tbl_m_outlet',
            'tbl_m_gudang',
            'tbl_m_satuan',
            'tbl_m_platform',
            'tbl_m_voucher',
        ];

        foreach ($masterTables as $table) {
            if ($this->db->tableExists($table)) {
                if ($this->db->fieldExists('deleted_at', $table)) {
                    $this->forge->dropColumn($table, 'deleted_at');
                    log_message('info', "Removed deleted_at column from {$table}");
                } else {
                    log_message('info', "Column deleted_at does not exist in {$table}");
                }
            }
        }
    }
}

