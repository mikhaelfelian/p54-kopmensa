<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBlockedFieldsToPelanggan extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tbl_m_pelanggan', [
            'is_blocked' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'after' => 'status',
                'comment' => '0=not blocked, 1=blocked'
            ],
            'limit_belanja' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
                'after' => 'is_blocked',
                'comment' => 'Shopping limit in currency'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_m_pelanggan', ['is_blocked', 'limit_belanja']);
    }
}
