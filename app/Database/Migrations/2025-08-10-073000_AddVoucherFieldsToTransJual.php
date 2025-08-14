<?php

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-08-10
 * Github : github.com/mikhaelfelian
 * Description : Migration for adding voucher fields to tbl_trans_jual table
 * This file represents the Migration.
 */

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Migration_2025_08_10_073000_AddVoucherFieldsToTransJual extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tbl_trans_jual', [
            'voucher_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'metode_bayar',
                'comment'    => 'Voucher code used in transaction',
            ],
            'voucher_discount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => '0.00',
                'after'      => 'voucher_code',
                'comment'    => 'Voucher discount percentage',
            ],
            'voucher_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'voucher_discount',
                'comment'    => 'Reference to voucher table',
            ],
            'voucher_type' => [
                'type'       => 'ENUM',
                'constraint' => ['nominal', 'persen'],
                'null'       => true,
                'after'      => 'voucher_id',
                'comment'    => 'Type of voucher: nominal or percentage',
            ],
            'voucher_discount_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => '0.00',
                'after'      => 'voucher_type',
                'comment'    => 'Actual voucher discount amount applied',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_trans_jual', [
            'voucher_code',
            'voucher_discount',
            'voucher_id',
            'voucher_type',
            'voucher_discount_amount'
        ]);
    }
} 