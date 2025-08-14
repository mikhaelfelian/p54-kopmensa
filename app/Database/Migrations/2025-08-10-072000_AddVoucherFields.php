<?php

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-18
 * Github : github.com/mikhaelfelian
 * Description : Migration for adding jenis_voucher and nominal fields to tbl_m_voucher table
 * This file represents the Migration.
 */

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVoucherFields extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tbl_m_voucher', [
            'jenis_voucher' => [
                'type'       => 'ENUM',
                'constraint' => ['nominal', 'persen'],
                'null'       => false,
                'default'    => 'nominal',
                'after'      => 'jml',
                'comment'    => 'Voucher type: nominal (fixed amount) or persen (percentage)',
            ],
            'nominal' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => false,
                'default'    => 0.00,
                'after'      => 'jenis_voucher',
                'comment'    => 'Voucher value (amount or percentage)',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_m_voucher', ['jenis_voucher', 'nominal']);
    }
} 