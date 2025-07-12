<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLimitToTblPengaturan extends Migration
{
    public function up()
    {
        $fields = [
            'limit' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => null,
                'after'      => 'ppn'
            ]
        ];

        $this->forge->addColumn('tbl_pengaturan', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_pengaturan', 'limit');
    }
} 