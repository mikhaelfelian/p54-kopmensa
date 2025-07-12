<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Migration_20240101043322_create_tbl_pengaturan extends Migration
{
    public function up()
    {
        $fields = [
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'updated_at' => [
                'type'    => 'TIMESTAMP',
                'null'    => false,
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            ],
            'judul' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'collation'  => 'utf8mb4_general_ci',
            ],
            'judul_app' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'collation'  => 'utf8mb4_general_ci',
            ],
            'alamat' => [
                'type'      => 'TEXT',
                'null'      => true,
                'default'   => null,
                'collation' => 'utf8mb4_general_ci',
            ],
            'deskripsi' => [
                'type'      => 'TEXT',
                'null'      => true,
                'default'   => null,
                'collation' => 'utf8mb4_general_ci',
            ],
            'kota' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'collation'  => 'utf8mb4_general_ci',
            ],
            'url' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
                'collation'  => 'utf8mb4_general_ci',
            ],
            'theme' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => null,
                'collation'  => 'utf8mb4_general_ci',
            ],
            'pagination_limit' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
            ],
            'favicon' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
                'collation'  => 'utf8mb4_general_ci',
            ],
            'logo' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
                'collation'  => 'utf8mb4_general_ci',
            ],
            'logo_header' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
                'collation'  => 'utf8mb4_general_ci',
            ],
            'ppn' => [
                'type'       => 'INT',
                'constraint' => 2,
                'null'       => true,
                'default'    => null,
            ],
        ];

        $this->forge->addField($fields);
        $this->forge->addKey('id', true, true, 'BTREE');
        $this->forge->createTable('tbl_pengaturan', true, [
            'ENGINE' => 'InnoDB',
            'COLLATE' => 'utf8mb4_general_ci',
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_pengaturan', true);
    }
}