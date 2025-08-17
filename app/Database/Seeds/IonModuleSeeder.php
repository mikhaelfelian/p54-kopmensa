<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Created by: Mikhael Felian Waskito - mikhaelfelian@gmail.com
 * Date: 2025-01-01
 * This file represents the Seeder for tbl_ion_modules table.
 */
class IonModuleSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Master Data Modules
            [
                'parent_id' => 0,
                'name' => 'Master Data',
                'route' => 'Master',
                'icon' => 'fas fa-database',
                'is_menu' => '1',
                'is_active' => '1',
                'sort_order' => 1,
                'default_permissions' => json_encode([
                    'create' => true,
                    'read' => true,
                    'read_all' => true,
                    'update' => true,
                    'update_all' => true,
                    'delete' => true,
                    'delete_all' => true,
                    'export' => true,
                    'import' => true
                ])
            ],
            [
                'parent_id' => 1,
                'name' => 'Item/Barang',
                'route' => 'Master/Item',
                'icon' => 'fas fa-box',
                'is_menu' => '1',
                'is_active' => '1',
                'sort_order' => 1,
                'default_permissions' => json_encode([
                    'create' => true,
                    'read' => true,
                    'read_all' => true,
                    'update' => true,
                    'update_all' => true,
                    'delete' => true,
                    'delete_all' => true,
                    'export' => true,
                    'import' => true
                ])
            ],
            [
                'parent_id' => 1,
                'name' => 'Kategori',
                'route' => 'Master/Kategori',
                'icon' => 'fas fa-tags',
                'is_menu' => '1',
                'is_active' => '1',
                'sort_order' => 2,
                'default_permissions' => json_encode([
                    'create' => true,
                    'read' => true,
                    'read_all' => true,
                    'update' => true,
                    'update_all' => true,
                    'delete' => true,
                    'delete_all' => true
                ])
            ],
            [
                'parent_id' => 1,
                'name' => 'Supplier',
                'route' => 'Master/Supplier',
                'icon' => 'fas fa-truck',
                'is_menu' => '1',
                'is_active' => '1',
                'sort_order' => 3,
                'default_permissions' => json_encode([
                    'create' => true,
                    'read' => true,
                    'read_all' => true,
                    'update' => true,
                    'update_all' => true,
                    'delete' => true,
                    'delete_all' => true
                ])
            ],
            [
                'parent_id' => 1,
                'name' => 'Pelanggan',
                'route' => 'Master/Pelanggan',
                'icon' => 'fas fa-users',
                'is_menu' => '1',
                'is_active' => '1',
                'sort_order' => 4,
                'default_permissions' => json_encode([
                    'create' => true,
                    'read' => true,
                    'read_all' => true,
                    'update' => true,
                    'update_all' => true,
                    'delete' => true,
                    'delete_all' => true
                ])
            ],
            [
                'parent_id' => 1,
                'name' => 'Karyawan',
                'route' => 'Master/Karyawan',
                'icon' => 'fas fa-user-tie',
                'is_menu' => '1',
                'is_active' => '1',
                'sort_order' => 5,
                'default_permissions' => json_encode([
                    'create' => true,
                    'read' => true,
                    'read_all' => true,
                    'update' => true,
                    'update_all' => true,
                    'delete' => true,
                    'delete_all' => true
                ])
            ],
            [
                'parent_id' => 1,
                'name' => 'Gudang',
                'route' => 'Master/Gudang',
                'icon' => 'fas fa-warehouse',
                'is_menu' => '1',
                'is_active' => '1',
                'sort_order' => 6,
                'default_permissions' => json_encode([
                    'create' => true,
                    'read' => true,
                    'read_all' => true,
                    'update' => true,
                    'update_all' => true,
                    'delete' => true,
                    'delete_all' => true
                ])
            ],

            // Transaction Modules
            [
                'parent_id' => 0,
                'name' => 'Transaksi',
                'route' => 'Transaksi',
                'icon' => 'fas fa-exchange-alt',
                'is_menu' => '1',
                'is_active' => '1',
                'sort_order' => 2,
                'default_permissions' => json_encode([
                    'create' => true,
                    'read' => true,
                    'read_all' => true,
                    'update' => true,
                    'update_all' => true,
                    'delete' => true,
                    'delete_all' => true,
                    'approve' => true,
                    'reject' => true
                ])
            ],
            [
                'parent_id' => 8,
                'name' => 'Pembelian',
                'route' => 'Transaksi/Pembelian',
                'icon' => 'fas fa-shopping-cart',
                'is_menu' => '1',
                'is_active' => '1',
                'sort_order' => 1,
                'default_permissions' => json_encode([
                    'create' => true,
                    'read' => true,
                    'read_all' => true,
                    'update' => true,
                    'update_all' => true,
                    'delete' => true,
                    'delete_all' => true,
                    'approve' => true,
                    'reject' => true
                ])
            ],
            [
                'parent_id' => 8,
                'name' => 'Penjualan',
                'route' => 'Transaksi/Penjualan',
                'icon' => 'fas fa-cash-register',
                'is_menu' => '1',
                'is_active' => '1',
                'sort_order' => 2,
                'default_permissions' => json_encode([
                    'create' => true,
                    'read' => true,
                    'read_all' => true,
                    'update' => true,
                    'update_all' => true,
                    'delete' => true,
                    'delete_all' => true,
                    'approve' => true,
                    'reject' => true
                ])
            ],

            // Warehouse Modules
            [
                'parent_id' => 0,
                'name' => 'Gudang',
                'route' => 'Gudang',
                'icon' => 'fas fa-boxes',
                'is_menu' => '1',
                'is_active' => '1',
                'sort_order' => 3,
                'default_permissions' => json_encode([
                    'create' => true,
                    'read' => true,
                    'read_all' => true,
                    'update' => true,
                    'update_all' => true,
                    'delete' => true,
                    'delete_all' => true
                ])
            ],
            [
                'parent_id' => 11,
                'name' => 'Input Stok',
                'route' => 'Gudang/InputStok',
                'icon' => 'fas fa-plus-square',
                'is_menu' => '1',
                'is_active' => '1',
                'sort_order' => 1,
                'default_permissions' => json_encode([
                    'create' => true,
                    'read' => true,
                    'read_all' => true,
                    'update' => true,
                    'update_all' => true,
                    'delete' => true,
                    'delete_all' => true
                ])
            ],
            [
                'parent_id' => 11,
                'name' => 'Inventori',
                'route' => 'Gudang/Inventori',
                'icon' => 'fas fa-clipboard-list',
                'is_menu' => '1',
                'is_active' => '1',
                'sort_order' => 2,
                'default_permissions' => json_encode([
                    'create' => true,
                    'read' => true,
                    'read_all' => true,
                    'update' => true,
                    'update_all' => true,
                    'delete' => true,
                    'delete_all' => true,
                    'export' => true
                ])
            ],

            // Report Modules
            [
                'parent_id' => 0,
                'name' => 'Laporan',
                'route' => 'Laporan',
                'icon' => 'fas fa-chart-bar',
                'is_menu' => '1',
                'is_active' => '1',
                'sort_order' => 4,
                'default_permissions' => json_encode([
                    'read' => true,
                    'read_all' => true,
                    'export' => true
                ])
            ],
            [
                'parent_id' => 14,
                'name' => 'Laporan Outlet',
                'route' => 'Laporan/Outlet',
                'icon' => 'fas fa-store',
                'is_menu' => '1',
                'is_active' => '1',
                'sort_order' => 1,
                'default_permissions' => json_encode([
                    'read' => true,
                    'read_all' => true,
                    'export' => true
                ])
            ],
            [
                'parent_id' => 14,
                'name' => 'Laporan Penjualan',
                'route' => 'Laporan/Penjualan',
                'icon' => 'fas fa-chart-line',
                'is_menu' => '1',
                'is_active' => '1',
                'sort_order' => 2,
                'default_permissions' => json_encode([
                    'read' => true,
                    'read_all' => true,
                    'export' => true
                ])
            ],

            // Settings Modules
            [
                'parent_id' => 0,
                'name' => 'Pengaturan',
                'route' => 'Pengaturan',
                'icon' => 'fas fa-cog',
                'is_menu' => '1',
                'is_active' => '1',
                'sort_order' => 5,
                'default_permissions' => json_encode([
                    'create' => true,
                    'read' => true,
                    'read_all' => true,
                    'update' => true,
                    'update_all' => true,
                    'delete' => true,
                    'delete_all' => true
                ])
            ],
            [
                'parent_id' => 17,
                'name' => 'Modul',
                'route' => 'Pengaturan/Modules',
                'icon' => 'fas fa-puzzle-piece',
                'is_menu' => '1',
                'is_active' => '1',
                'sort_order' => 1,
                'default_permissions' => json_encode([
                    'create' => true,
                    'read' => true,
                    'read_all' => true,
                    'update' => true,
                    'update_all' => true,
                    'delete' => true,
                    'delete_all' => true
                ])
            ],
            [
                'parent_id' => 17,
                'name' => 'Printer',
                'route' => 'Pengaturan/Printer',
                'icon' => 'fas fa-print',
                'is_menu' => '1',
                'is_active' => '1',
                'sort_order' => 2,
                'default_permissions' => json_encode([
                    'create' => true,
                    'read' => true,
                    'read_all' => true,
                    'update' => true,
                    'update_all' => true,
                    'delete' => true,
                    'delete_all' => true
                ])
            ],
            [
                'parent_id' => 17,
                'name' => 'PU Menu',
                'route' => 'Pengaturan/PuMenu',
                'icon' => 'fas fa-list',
                'is_menu' => '1',
                'is_active' => '1',
                'sort_order' => 3,
                'default_permissions' => json_encode([
                    'create' => true,
                    'read' => true,
                    'read_all' => true,
                    'update' => true,
                    'update_all' => true,
                    'delete' => true,
                    'delete_all' => true
                ])
            ],
        ];

        $this->db->table('tbl_ion_modules')->insertBatch($data);
    }
}
