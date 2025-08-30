<?php
/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2024-06-13
 * Github: github.com/mikhaelfelian
 * description: Migration for Petty Cash Category Master (tbl_m_petty_category)
 * This file represents the Migration.
 */

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Migration_create_tbl_m_petty_category extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
                'after'   => 'id',
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
                'after'   => 'created_at',
            ],
            'kode'       => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'nama'       => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
                'collate'    => 'utf8mb4_general_ci',
                'after'      => 'kode',
            ],
            'deskripsi'  => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'status'     => [
                'type'       => 'ENUM',
                'constraint' => ['0', '1'],
                'default'    => '1',
                'null'       => false,
                'collate'    => 'utf8mb4_general_ci',
            ],
        ]);

        $this->forge->addKey('id', true);
        // Remove the old unique index on 'name' (if exists) and add new unique index on 'nama'
        // CodeIgniter's Forge does not support dropping indexes by name in createTable, so we only add the correct unique keys
        $this->forge->addUniqueKey(['nama'], 'name');
        $this->forge->addUniqueKey(['kode']);
        $this->forge->createTable('tbl_m_petty_category', true, ['ENGINE' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_m_petty_category', true);
    }
}
