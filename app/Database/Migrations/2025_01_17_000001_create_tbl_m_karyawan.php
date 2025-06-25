<?php
/**
 * Created by:
 * Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * 2025-01-17
 * 
 * Migration for tbl_m_karyawan
 */

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTblMKaryawan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 4,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_user' => [
                'type'       => 'INT',
                'constraint' => 4,
                'null'       => true,
                'default'    => 0,
            ],
            'id_user_group' => [
                'type'       => 'INT',
                'constraint' => 4,
                'null'       => true,
                'default'    => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'kode' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            'nik' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'nama_dpn' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'nama_blk' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'nama_pgl' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'tmp_lahir' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'tgl_lahir' => [
                'type'    => 'DATE',
                'null'    => true,
            ],
            'alamat' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'alamat_domisili' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'rt' => [
                'type'       => 'VARCHAR',
                'constraint' => 3,
                'null'       => true,
            ],
            'rw' => [
                'type'       => 'VARCHAR',
                'constraint' => 3,
                'null'       => true,
            ],
            'kelurahan' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'kecamatan' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'kota' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'jns_klm' => [
                'type'       => 'ENUM',
                'constraint' => ['L', 'P'],
                'null'       => true,
            ],
            'jabatan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'no_hp' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'file_foto' => [
                'type'       => 'VARCHAR',
                'constraint' => 160,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'comment'    => '1 = kasir\r\n2 = supervisor / kepala toko\r\n3 = gudang / stocker\r\n4 = admin penjualan\r\n5 = purchasing\r\n6 = owner / manajer',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('tbl_m_karyawan');
    }

    public function down()
    {
        $this->forge->dropTable('tbl_m_karyawan');
    }
}