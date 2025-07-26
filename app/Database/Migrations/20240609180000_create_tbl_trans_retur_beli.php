<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2024-06-09
 * Github : github.com/mikhaelfelian
 * description : Migration for creating tbl_trans_jual_plat
 * This file represents the Migration.
 * Table untuk <note_for_using_table>
 */

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblTransReturBeli extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_beli' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'comment'    => 'Referensi ke tbl_trans_beli.id',
            ],
            'id_supplier' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
            ],
            'id_user' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
            ],
            'id_user_terima' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
                'comment'    => 'User yang memproses retur',
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
            ],
            'deleted_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
            ],
            'tgl_retur' => [
                'type'    => 'DATE',
                'null'    => true,
                'default' => null,
                'comment' => 'Tanggal retur barang',
            ],
            'no_nota_retur' => [
                'type'       => 'VARCHAR',
                'constraint' => 160,
                'null'       => true,
                'default'    => null,
                'collate'    => 'utf8mb4_general_ci',
            ],
            'no_nota_asal' => [
                'type'       => 'VARCHAR',
                'constraint' => 160,
                'null'       => true,
                'default'    => null,
                'collate'    => 'utf8mb4_general_ci',
                'comment'    => 'No nota dari pembelian',
            ],
            'alasan_retur' => [
                'type'       => 'TEXT',
                'null'       => true,
                'default'    => null,
                'collate'    => 'utf8mb4_general_ci',
            ],
            'jml_retur' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => '0.00',
            ],
            'jml_potongan' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => '0.00',
            ],
            'jml_subtotal' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => '0.00',
            ],
            'jml_ppn' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => '0.00',
            ],
            'jml_total' => [
                'type'       => 'DECIMAL',
                'constraint' => '32,2',
                'null'       => true,
                'default'    => '0.00',
            ],
            'status_ppn' => [
                'type'       => 'ENUM',
                'constraint' => ['0','1','2'],
                'null'       => true,
                'default'    => null,
                'collate'    => 'utf8mb4_general_ci',
            ],
            'status_retur' => [
                'type'       => 'ENUM',
                'constraint' => ['0','1'],
                'null'       => true,
                'default'    => '0',
                'collate'    => 'utf8mb4_general_ci',
                'comment'    => '0=Draft, 1=Selesai',
            ],
            'catatan' => [
                'type'       => 'TEXT',
                'null'       => true,
                'default'    => null,
                'collate'    => 'utf8mb4_general_ci',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_trans_retur_beli', true, [
            'comment' => 'Table untuk menyimpan data transaksi retur pembelian'
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_trans_retur_beli', true);
    }
} 