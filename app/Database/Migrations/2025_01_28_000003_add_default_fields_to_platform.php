<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDefaultFieldsToPlatform extends Migration
{
    public function up()
    {
        $fields = [
            'is_default' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => '0',
                'null'       => false,
                'comment'    => 'Is default payment method for this outlet'
            ],
            'status_hps' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => '0',
                'null'       => false,
                'comment'    => 'Archive status: 0=active, 1=archived'
            ],
            'deleted_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
                'comment' => 'Soft delete timestamp'
            ]
        ];

        $this->forge->addColumn('tbl_m_platform', $fields);

        // Add index for is_default
        $this->forge->addKey('is_default');
        
        // Add index for status_hps
        $this->forge->addKey('status_hps');
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_m_platform', ['is_default', 'status_hps', 'deleted_at']);
    }
}

