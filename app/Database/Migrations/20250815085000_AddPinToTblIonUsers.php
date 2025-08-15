<?php

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-08-06
 * Github: github.com/mikhaelfelian
 * description: Migration to add PIN field to tbl_ion_users table
 * This file represents the Migration.
 */

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPinToTblIonUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tbl_ion_users', [
            'pin' => [
                'type'       => 'VARCHAR',
                'constraint' => '6',
                'null'       => true,
                'default'    => null,
                'comment'    => 'PIN for additional authentication'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tbl_ion_users', 'pin');
    }
} 