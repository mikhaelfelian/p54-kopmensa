<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Create Refund Requests Table
 * 
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-18
 * Description: Migration for creating refund requests table
 */
class CreateTblRefundRequests extends Migration
{
    public function up()
    {
        // Drop table if exists
        $this->forge->dropTable('tbl_refund_requests', true);

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_transaction' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'comment'    => 'Relasi ke tbl_trans_jual.id',
            ],
            'id_user' => [
                'type'       => 'MEDIUMINT',
                'constraint' => '8',
                'unsigned'   => true,
                'null'       => false,
                'comment'    => 'Relasi ke tbl_ion_users.id (cashier yang membuat request)',
            ],
            'id_pelanggan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'comment'    => 'Relasi ke tbl_m_pelanggan.id',
            ],
            'no_nota' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
                'comment'    => 'Nomor nota transaksi',
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'null'       => false,
                'comment'    => 'Jumlah refund yang diminta',
            ],
            'reason' => [
                'type'       => 'TEXT',
                'null'       => false,
                'comment'    => 'Alasan refund (wajib diisi)',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'default'    => 'pending',
                'null'       => false,
                'comment'    => 'Status permintaan refund',
            ],
            'approved_by' => [
                'type'       => 'MEDIUMINT',
                'constraint' => '8',
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'Relasi ke tbl_ion_users.id (superadmin yang approve/reject)',
            ],
            'approved_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Waktu approval/rejection',
            ],
            'rejection_reason' => [
                'type'       => 'TEXT',
                'null'       => true,
                'comment'    => 'Alasan penolakan jika status rejected',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('id_transaction');
        $this->forge->addKey('id_user');
        $this->forge->addKey('id_pelanggan');
        $this->forge->addKey('status');
        
        // Add foreign key constraints with correct table names
        $this->forge->addForeignKey('id_transaction', 'tbl_trans_jual', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_user', 'tbl_ion_users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_pelanggan', 'tbl_m_pelanggan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('approved_by', 'tbl_ion_users', 'id', 'SET NULL', 'SET NULL');
        
        $this->forge->createTable('tbl_refund_requests', true, [
            'comment' => 'Table untuk menyimpan permintaan refund dari kasir'
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('tbl_refund_requests');
    }
}
