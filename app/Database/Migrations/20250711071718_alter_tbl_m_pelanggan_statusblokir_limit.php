<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-07-11
 * Github : github.com/mikhaelfelian
 * description : Migration for altering tbl_m_pelanggan to add status_blokir and limit columns (idempotent)
 */
class AlterTblMPelangganStatusblokirLimit extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        $forge = $this->forge;
        $fields = [];
        $columns = array_column($db->getFieldData('tbl_m_pelanggan'), 'name');

        // Add status_blokir if not exists
        if (!in_array('status_blokir', $columns)) {
            $fields['status_blokir'] = [
                'type'       => 'ENUM',
                'constraint' => ['0', '1'],
                'default'    => '0',
                'null'       => true,
                'after'      => 'status_hps',
                'comment'    => '0=tidak diblokir; 1=diblokir',
            ];
        }
        // Add limit if not exists
        if (!in_array('limit', $columns)) {
            $fields['limit'] = [
                'type'       => 'FLOAT',
                'constraint' => '10,2',
                'default'    => 0,
                'null'       => true,
                'after'      => 'status_blokir',
            ];
        }
        if (!empty($fields)) {
            $forge->addColumn('tbl_m_pelanggan', $fields);
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        $forge = $this->forge;
        $columns = array_column($db->getFieldData('tbl_m_pelanggan'), 'name');
        if (in_array('status_blokir', $columns)) {
            $forge->dropColumn('tbl_m_pelanggan', 'status_blokir');
        }
        if (in_array('limit', $columns)) {
            $forge->dropColumn('tbl_m_pelanggan', 'limit');
        }
    }
} 