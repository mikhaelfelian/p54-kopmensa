<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Migration_20250130000001_add_logo_invoice_to_tbl_pengaturan extends Migration
{
    public function up()
    {
        $fields = [
            'logo_invoice' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
                'collation'  => 'utf8mb4_general_ci',
                'after'      => 'logo_header',
            ],
        ];

        $this->forge->addColumn('tbl_pengaturan', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_pengaturan', 'logo_invoice');
    }
}

