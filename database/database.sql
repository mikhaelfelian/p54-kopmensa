-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.4.32-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table db_p54_kopmensa.migrations
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_input_stok
DROP TABLE IF EXISTS `tbl_input_stok`;
CREATE TABLE IF NOT EXISTS `tbl_input_stok` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `no_terima` varchar(50) NOT NULL,
  `tgl_terima` datetime NOT NULL,
  `id_supplier` int(11) unsigned NOT NULL,
  `id_gudang` int(11) unsigned NOT NULL,
  `id_penerima` int(11) unsigned NOT NULL,
  `keterangan` text DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1' COMMENT '0=Inactive, 1=Active',
  `status_hps` enum('0','1') NOT NULL DEFAULT '0' COMMENT '0=Not Deleted, 1=Deleted',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `no_terima` (`no_terima`),
  KEY `tgl_terima` (`tgl_terima`),
  KEY `id_supplier` (`id_supplier`),
  KEY `id_gudang` (`id_gudang`),
  KEY `id_penerima` (`id_penerima`),
  CONSTRAINT `tbl_input_stok_id_gudang_foreign` FOREIGN KEY (`id_gudang`) REFERENCES `tbl_m_gudang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tbl_input_stok_id_penerima_foreign` FOREIGN KEY (`id_penerima`) REFERENCES `tbl_m_karyawan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tbl_input_stok_id_supplier_foreign` FOREIGN KEY (`id_supplier`) REFERENCES `tbl_m_supplier` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_input_stok_det
DROP TABLE IF EXISTS `tbl_input_stok_det`;
CREATE TABLE IF NOT EXISTS `tbl_input_stok_det` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_input_stok` int(11) unsigned NOT NULL,
  `id_item` int(11) unsigned NOT NULL,
  `id_satuan` int(11) unsigned NOT NULL,
  `jml` decimal(15,2) NOT NULL DEFAULT 0.00,
  `keterangan` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_input_stok` (`id_input_stok`),
  KEY `id_item` (`id_item`),
  KEY `id_satuan` (`id_satuan`),
  CONSTRAINT `tbl_input_stok_det_id_input_stok_foreign` FOREIGN KEY (`id_input_stok`) REFERENCES `tbl_input_stok` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tbl_input_stok_det_id_item_foreign` FOREIGN KEY (`id_item`) REFERENCES `tbl_m_item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tbl_input_stok_det_id_satuan_foreign` FOREIGN KEY (`id_satuan`) REFERENCES `tbl_m_satuan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_ion_actions
DROP TABLE IF EXISTS `tbl_ion_actions`;
CREATE TABLE IF NOT EXISTS `tbl_ion_actions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL COMMENT 'kode unik aksi, ex: read, create',
  `name` varchar(100) NOT NULL COMMENT 'nama aksi lebih panjang ex: Baca Data',
  `description` varchar(255) DEFAULT NULL COMMENT 'penjelasan singkat',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_ion_groups
DROP TABLE IF EXISTS `tbl_ion_groups`;
CREATE TABLE IF NOT EXISTS `tbl_ion_groups` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_ion_login_attempts
DROP TABLE IF EXISTS `tbl_ion_login_attempts`;
CREATE TABLE IF NOT EXISTS `tbl_ion_login_attempts` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `login` varchar(100) DEFAULT NULL,
  `time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_ion_modules
DROP TABLE IF EXISTS `tbl_ion_modules`;
CREATE TABLE IF NOT EXISTS `tbl_ion_modules` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT 0 COMMENT 'Untuk modul dengan sub-menu',
  `name` varchar(100) NOT NULL COMMENT 'Nama modul (ex: Barang, Kategori)',
  `route` varchar(100) NOT NULL COMMENT 'ex: Master/Item',
  `icon` varchar(100) DEFAULT NULL,
  `is_menu` enum('0','1') NOT NULL DEFAULT '1' COMMENT 'tampil di sidebar',
  `is_active` enum('0','1') NOT NULL DEFAULT '1',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `default_permissions` text DEFAULT NULL COMMENT 'JSON: {"create":true,"read_all":true}',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_ion_permissions
DROP TABLE IF EXISTS `tbl_ion_permissions`;
CREATE TABLE IF NOT EXISTS `tbl_ion_permissions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `module_id` int(11) unsigned NOT NULL COMMENT 'Reference to tbl_ion_modules.id',
  `action_id` int(11) unsigned NOT NULL COMMENT 'Reference to tbl_ion_actions.id',
  `group_id` int(11) unsigned DEFAULT NULL COMMENT 'Reference to groups.id (null for user-specific)',
  `user_id` int(11) unsigned DEFAULT NULL COMMENT 'Reference to users.id (null for group-specific)',
  `is_granted` enum('0','1') NOT NULL DEFAULT '1' COMMENT '1 = granted, 0 = denied',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `module_id_action_id_group_id_user_id` (`module_id`,`action_id`,`group_id`,`user_id`),
  KEY `group_id` (`group_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=441 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_ion_users
DROP TABLE IF EXISTS `tbl_ion_users`;
CREATE TABLE IF NOT EXISTS `tbl_ion_users` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `pin` varchar(6) DEFAULT NULL COMMENT 'PIN for additional authentication',
  `email` varchar(254) NOT NULL,
  `activation_selector` varchar(255) DEFAULT NULL,
  `activation_code` varchar(255) DEFAULT NULL,
  `forgotten_password_selector` varchar(255) DEFAULT NULL,
  `forgotten_password_code` varchar(255) DEFAULT NULL,
  `forgotten_password_time` int(11) unsigned DEFAULT NULL,
  `remember_selector` varchar(255) DEFAULT NULL,
  `remember_code` varchar(255) DEFAULT NULL,
  `created_on` int(11) unsigned NOT NULL,
  `last_login` int(11) unsigned DEFAULT NULL,
  `active` tinyint(1) unsigned DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `profile` varchar(160) DEFAULT NULL,
  `tipe` enum('1','2') NOT NULL DEFAULT '1' COMMENT '1 => karyawan, 2 => anggota',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `activation_selector` (`activation_selector`),
  UNIQUE KEY `forgotten_password_selector` (`forgotten_password_selector`),
  UNIQUE KEY `remember_selector` (`remember_selector`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_ion_users_groups
DROP TABLE IF EXISTS `tbl_ion_users_groups`;
CREATE TABLE IF NOT EXISTS `tbl_ion_users_groups` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL,
  `group_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tbl_ion_users_groups_user_id_foreign` (`user_id`),
  KEY `tbl_ion_users_groups_group_id_foreign` (`group_id`),
  CONSTRAINT `tbl_ion_users_groups_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `tbl_ion_groups` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `tbl_ion_users_groups_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `tbl_ion_users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_m_gudang
DROP TABLE IF EXISTS `tbl_m_gudang`;
CREATE TABLE IF NOT EXISTS `tbl_m_gudang` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int(11) unsigned DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `kode` varchar(160) DEFAULT NULL,
  `nama` varchar(160) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `status` enum('0','1') DEFAULT NULL COMMENT '1 = aktif\\r\\n0 = Non Aktif',
  `status_gd` enum('0','1') DEFAULT '0' COMMENT '1 = Gudang Utama\\r\\n0 = Bukan Gudang Utama',
  `status_otl` enum('0','1') DEFAULT '0' COMMENT '0 = Bukan Outlet\r\n1 = Outlet',
  `status_hps` enum('0','1') DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Table untuk menyimpan nama gudang ./ outlet.\r\nOutlet digabung dengan gudang';

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_m_item
DROP TABLE IF EXISTS `tbl_m_item`;
CREATE TABLE IF NOT EXISTS `tbl_m_item` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL DEFAULT 0,
  `id_kategori` int(11) NOT NULL DEFAULT 0,
  `id_satuan` int(11) NOT NULL DEFAULT 0,
  `id_merk` int(11) NOT NULL DEFAULT 0,
  `id_supplier` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `kode` varchar(64) DEFAULT NULL COMMENT 'Kode Item / SKU',
  `barcode` varchar(64) DEFAULT NULL COMMENT 'Barcode Produk',
  `item` varchar(128) DEFAULT NULL COMMENT 'Nama Produk / Item',
  `deskripsi` text DEFAULT NULL COMMENT 'Deskripsi Produk',
  `jml_min` float DEFAULT 0 COMMENT 'Minimum stok sebelum restock',
  `harga_beli` decimal(12,2) DEFAULT 0.00,
  `harga_jual` decimal(12,2) DEFAULT 0.00,
  `foto` varchar(255) DEFAULT NULL COMMENT 'Gambar produk (opsional)',
  `tipe` enum('1','2','3') DEFAULT '1' COMMENT 'Tipe produk 1=item; 2=jasa; 3=paket;',
  `status` enum('0','1') DEFAULT '1' COMMENT 'Status item aktif / tidak',
  `status_stok` enum('0','1') DEFAULT '1',
  `status_hps` enum('0','1') DEFAULT '0' COMMENT 'Status soft delete',
  `sp` enum('0','1') DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7038 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_m_item_harga
DROP TABLE IF EXISTS `tbl_m_item_harga`;
CREATE TABLE IF NOT EXISTS `tbl_m_item_harga` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_item` int(11) unsigned NOT NULL COMMENT 'Relasi ke tbl_m_item.id',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `nama` varchar(64) NOT NULL COMMENT 'Nama level harga, contoh: ecer, grosir, distributor',
  `jml_min` int(11) NOT NULL DEFAULT 1 COMMENT 'Jumlah minimal beli agar harga ini berlaku',
  `harga` decimal(12,2) NOT NULL COMMENT 'Harga jual untuk level ini',
  `keterangan` varchar(255) DEFAULT NULL COMMENT 'Keterangan tambahan (opsional)',
  PRIMARY KEY (`id`),
  KEY `id_item` (`id_item`),
  CONSTRAINT `tbl_m_item_harga_id_item_foreign` FOREIGN KEY (`id_item`) REFERENCES `tbl_m_item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Table untuk menyimpan harga. Cth : harga utk anggota,dokter,pelanggan, dll';

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_m_item_hist
DROP TABLE IF EXISTS `tbl_m_item_hist`;
CREATE TABLE IF NOT EXISTS `tbl_m_item_hist` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_item` int(11) unsigned DEFAULT NULL,
  `id_satuan` int(11) DEFAULT 0,
  `id_gudang` int(11) unsigned DEFAULT NULL,
  `id_user` int(11) DEFAULT 0,
  `id_pelanggan` int(11) DEFAULT 0,
  `id_supplier` int(11) DEFAULT 0,
  `id_penjualan` int(11) DEFAULT 0,
  `id_pembelian` int(11) DEFAULT 0,
  `id_pembelian_det` int(11) DEFAULT 0,
  `id_so` int(11) DEFAULT 0,
  `id_mutasi` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `tgl_masuk` datetime DEFAULT NULL,
  `no_nota` varchar(100) DEFAULT NULL,
  `kode` varchar(100) DEFAULT NULL,
  `item` text DEFAULT NULL,
  `keterangan` longtext DEFAULT NULL,
  `nominal` decimal(10,2) DEFAULT 0.00,
  `jml` int(11) DEFAULT 0,
  `jml_satuan` int(11) DEFAULT 1,
  `satuan` varchar(50) DEFAULT NULL,
  `status` enum('1','2','3','4','5','6','7','8') DEFAULT NULL COMMENT '1 = Stok Masuk Pembelian, 2 = Stok Masuk, 3 = Stok Masuk Retur Jual, 4 = Stok Keluar Penjualan, 5 = Stok Keluar Retur Beli, 6 = SO, 7 = Stok Keluar, 8 = Mutasi Antar Gd',
  `sp` enum('0','1') DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_item` (`id_item`),
  KEY `id_gudang` (`id_gudang`),
  CONSTRAINT `tbl_m_item_hist_id_gudang_foreign` FOREIGN KEY (`id_gudang`) REFERENCES `tbl_m_gudang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tbl_m_item_hist_id_item_foreign` FOREIGN KEY (`id_item`) REFERENCES `tbl_m_item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Table untuk menyimpan item stok histories';

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_m_item_stok
DROP TABLE IF EXISTS `tbl_m_item_stok`;
CREATE TABLE IF NOT EXISTS `tbl_m_item_stok` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_item` int(11) unsigned NOT NULL,
  `id_gudang` int(11) unsigned DEFAULT 0,
  `id_user` int(11) unsigned DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `jml` float DEFAULT 0,
  `status` enum('0','1','2') DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_item_id_gudang_id_outlet` (`id_item`,`id_gudang`),
  KEY `status` (`status`),
  CONSTRAINT `tbl_m_item_stok_id_item_foreign` FOREIGN KEY (`id_item`) REFERENCES `tbl_m_item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=46764 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_m_item_varian
DROP TABLE IF EXISTS `tbl_m_item_varian`;
CREATE TABLE IF NOT EXISTS `tbl_m_item_varian` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_item` int(11) unsigned NOT NULL,
  `id_item_harga` int(11) unsigned DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `kode` varchar(50) NOT NULL,
  `barcode` varchar(100) DEFAULT NULL,
  `varian` varchar(255) NOT NULL,
  `harga_beli` decimal(18,2) DEFAULT NULL,
  `harga_dasar` decimal(18,2) DEFAULT NULL,
  `harga_jual` decimal(18,2) DEFAULT NULL,
  `foto` varchar(160) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_kode` (`kode`) USING BTREE,
  KEY `idx_id_item` (`id_item`),
  CONSTRAINT `tbl_m_item_varian_id_item_foreign` FOREIGN KEY (`id_item`) REFERENCES `tbl_m_item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_m_karyawan
DROP TABLE IF EXISTS `tbl_m_karyawan`;
CREATE TABLE IF NOT EXISTS `tbl_m_karyawan` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int(4) DEFAULT 0,
  `id_user_group` int(4) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `kode` varchar(10) DEFAULT NULL,
  `nik` varchar(100) DEFAULT NULL,
  `nama_dpn` varchar(100) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `nama_blk` varchar(100) DEFAULT NULL,
  `nama_pgl` varchar(100) DEFAULT NULL,
  `tmp_lahir` varchar(100) DEFAULT NULL,
  `tgl_lahir` date DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `alamat_domisili` text DEFAULT NULL,
  `rt` varchar(3) DEFAULT NULL,
  `rw` varchar(3) DEFAULT NULL,
  `kelurahan` varchar(50) DEFAULT NULL,
  `kecamatan` varchar(50) DEFAULT NULL,
  `kota` varchar(50) DEFAULT NULL,
  `jns_klm` enum('L','P') DEFAULT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `file_foto` varchar(160) DEFAULT NULL,
  `status` int(11) DEFAULT NULL COMMENT '1 = kasir\r\n2 = supervisor / kepala toko\r\n3 = gudang / stocker\r\n4 = admin penjualan\r\n5 = purchasing\r\n6 = owner / manajer',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_m_kategori
DROP TABLE IF EXISTS `tbl_m_kategori`;
CREATE TABLE IF NOT EXISTS `tbl_m_kategori` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `kode` varchar(100) DEFAULT NULL,
  `kategori` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_m_merk
DROP TABLE IF EXISTS `tbl_m_merk`;
CREATE TABLE IF NOT EXISTS `tbl_m_merk` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `kode` varchar(160) DEFAULT NULL,
  `merk` varchar(160) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `status` enum('0','1') DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_m_outlet
DROP TABLE IF EXISTS `tbl_m_outlet`;
CREATE TABLE IF NOT EXISTS `tbl_m_outlet` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `kode` varchar(64) DEFAULT NULL,
  `nama` varchar(128) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `status_hps` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_m_pelanggan
DROP TABLE IF EXISTS `tbl_m_pelanggan`;
CREATE TABLE IF NOT EXISTS `tbl_m_pelanggan` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  `kode` varchar(160) DEFAULT NULL,
  `nama` varchar(360) DEFAULT NULL,
  `no_telp` varchar(50) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `kota` text DEFAULT NULL,
  `provinsi` text DEFAULT NULL,
  `tipe` enum('0','1','2','3') DEFAULT '0' COMMENT '0=none; 1=anggota koperasi; 2=umum; 3=swasta',
  `status` enum('0','1') DEFAULT '0',
  `status_hps` enum('0','1') DEFAULT '0' COMMENT '0=none; 1=terhapus;',
  `status_limit` enum('0','1') DEFAULT '0',
  `status_blokir` enum('0','1') DEFAULT '0' COMMENT '0=tidak diblokir; 1=diblokir',
  `limit` float(10,2) DEFAULT 0.00,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=503 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_m_pelanggan_grup
DROP TABLE IF EXISTS `tbl_m_pelanggan_grup`;
CREATE TABLE IF NOT EXISTS `tbl_m_pelanggan_grup` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `grup` varchar(100) NOT NULL COMMENT 'Nama grup pelanggan, misal: Umum, Anggota, Reseller',
  `deskripsi` varchar(255) DEFAULT NULL COMMENT 'Keterangan tambahan grup pelanggan',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=aktif, 0=nonaktif',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Table untuk menyimpan grup pelanggan';

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_m_pelanggan_grup_member
DROP TABLE IF EXISTS `tbl_m_pelanggan_grup_member`;
CREATE TABLE IF NOT EXISTS `tbl_m_pelanggan_grup_member` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_grup` int(11) unsigned NOT NULL,
  `id_pelanggan` int(11) unsigned NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_grup_id_pelanggan` (`id_grup`,`id_pelanggan`),
  KEY `tbl_m_pelanggan_grup_member_id_pelanggan_foreign` (`id_pelanggan`),
  CONSTRAINT `tbl_m_pelanggan_grup_member_id_grup_foreign` FOREIGN KEY (`id_grup`) REFERENCES `tbl_m_pelanggan_grup` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tbl_m_pelanggan_grup_member_id_pelanggan_foreign` FOREIGN KEY (`id_pelanggan`) REFERENCES `tbl_m_pelanggan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_m_platform
DROP TABLE IF EXISTS `tbl_m_platform`;
CREATE TABLE IF NOT EXISTS `tbl_m_platform` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_outlet` int(11) unsigned NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `kode` varchar(160) DEFAULT NULL,
  `platform` varchar(160) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `persen` decimal(10,1) DEFAULT NULL,
  `status` enum('0','1') DEFAULT '1',
  `status_sys` enum('0','1') DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Platform Pembayaran';

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_m_printer
DROP TABLE IF EXISTS `tbl_m_printer`;
CREATE TABLE IF NOT EXISTS `tbl_m_printer` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nama_printer` varchar(100) NOT NULL,
  `tipe_printer` enum('network','usb','file','windows') NOT NULL DEFAULT 'network',
  `ip_address` varchar(45) DEFAULT NULL,
  `port` int(11) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `driver` enum('pos58','epson','star','citizen','generic') NOT NULL DEFAULT 'pos58',
  `width_paper` int(11) NOT NULL DEFAULT 58,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `keterangan` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tipe_printer` (`tipe_printer`),
  KEY `status` (`status`),
  KEY `is_default` (`is_default`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_m_satuan
DROP TABLE IF EXISTS `tbl_m_satuan`;
CREATE TABLE IF NOT EXISTS `tbl_m_satuan` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `satuanKecil` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `satuanBesar` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `jml` int(11) NOT NULL,
  `status` enum('1','0') CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_m_supplier
DROP TABLE IF EXISTS `tbl_m_supplier`;
CREATE TABLE IF NOT EXISTS `tbl_m_supplier` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `kode` varchar(50) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `npwp` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `rt` varchar(50) DEFAULT NULL,
  `rw` varchar(50) DEFAULT NULL,
  `kecamatan` varchar(50) DEFAULT NULL,
  `kelurahan` varchar(50) DEFAULT NULL,
  `kota` varchar(100) DEFAULT NULL,
  `no_tlp` varchar(20) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `tipe` enum('0','1','2') DEFAULT '0' COMMENT '1= Instansi; 2=Personal',
  `status` enum('0','1') DEFAULT '0',
  `status_hps` enum('0','1') DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_m_varian
DROP TABLE IF EXISTS `tbl_m_varian`;
CREATE TABLE IF NOT EXISTS `tbl_m_varian` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `kode` varchar(50) NOT NULL COMMENT 'Kode unik varian',
  `nama` varchar(100) NOT NULL COMMENT 'Nama varian, contoh: Warna Merah, Ukuran XL',
  `keterangan` text DEFAULT NULL COMMENT 'Penjelasan detail varian jika perlu',
  `status` enum('1','0') NOT NULL DEFAULT '1' COMMENT '1=Aktif, 0=Nonaktif',
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode` (`kode`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Table untuk menyimpan data varian produk seperti warna, ukuran, atau atribut lainnya';

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_m_voucher
DROP TABLE IF EXISTS `tbl_m_voucher`;
CREATE TABLE IF NOT EXISTS `tbl_m_voucher` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key for voucher table',
  `id_user` int(11) unsigned DEFAULT NULL COMMENT 'User ID who created the voucher',
  `created_at` datetime NOT NULL COMMENT 'Record creation timestamp',
  `updated_at` datetime NOT NULL COMMENT 'Record update timestamp',
  `kode` varchar(50) NOT NULL COMMENT 'Unique voucher code',
  `jml` int(11) NOT NULL COMMENT 'Total voucher amount/quantity',
  `jenis_voucher` enum('nominal','persen') NOT NULL DEFAULT 'nominal' COMMENT 'Voucher type: nominal (fixed amount) or persen (percentage)',
  `nominal` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Voucher value (amount or percentage)',
  `jml_keluar` int(11) NOT NULL DEFAULT 0 COMMENT 'Amount/quantity used',
  `jml_max` int(11) NOT NULL COMMENT 'Maximum usage limit',
  `tgl_masuk` date NOT NULL COMMENT 'Voucher start date',
  `tgl_keluar` date NOT NULL COMMENT 'Voucher expiry date',
  `status` enum('0','1') NOT NULL DEFAULT '1' COMMENT '1=Active, 0=Inactive',
  `keterangan` text DEFAULT NULL COMMENT 'Voucher description or notes',
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode` (`kode`),
  KEY `id_user` (`id_user`),
  KEY `status` (`status`),
  KEY `tgl_masuk` (`tgl_masuk`),
  KEY `tgl_keluar` (`tgl_keluar`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Table untuk menyimpan data master voucher/kupon diskon yang dapat digunakan dalam transaksi penjualan';

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_pengaturan
DROP TABLE IF EXISTS `tbl_pengaturan`;
CREATE TABLE IF NOT EXISTS `tbl_pengaturan` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `judul` varchar(100) DEFAULT NULL,
  `judul_app` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `kota` varchar(100) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `theme` varchar(50) DEFAULT NULL,
  `pagination_limit` int(11) DEFAULT NULL,
  `favicon` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `logo_header` varchar(255) DEFAULT NULL,
  `ppn` int(2) DEFAULT NULL,
  `limit` decimal(10,2) DEFAULT NULL COMMENT 'Limit pengaturan',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_pengaturan_api
DROP TABLE IF EXISTS `tbl_pengaturan_api`;
CREATE TABLE IF NOT EXISTS `tbl_pengaturan_api` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `id_pengaturan` int(11) unsigned DEFAULT NULL,
  `nama` varchar(100) NOT NULL,
  `pub_key` text DEFAULT NULL,
  `priv_key` text DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `id_pengaturan` (`id_pengaturan`),
  CONSTRAINT `FK_pengaturan_api` FOREIGN KEY (`id_pengaturan`) REFERENCES `tbl_pengaturan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Table untuk menyimpan pengaturan API';

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_pengaturan_theme
DROP TABLE IF EXISTS `tbl_pengaturan_theme`;
CREATE TABLE IF NOT EXISTS `tbl_pengaturan_theme` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_pengaturan` int(11) unsigned DEFAULT NULL,
  `nama` varchar(50) DEFAULT NULL,
  `path` varchar(160) DEFAULT NULL,
  `status` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `id_pengaturan` (`id_pengaturan`),
  CONSTRAINT `FK_pengaturan_theme` FOREIGN KEY (`id_pengaturan`) REFERENCES `tbl_pengaturan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_sessions
DROP TABLE IF EXISTS `tbl_sessions`;
CREATE TABLE IF NOT EXISTS `tbl_sessions` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL DEFAULT 0,
  `data` blob NOT NULL,
  PRIMARY KEY (`id`),
  KEY `timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Table untuk menyimpan session data';

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_trans_beli
DROP TABLE IF EXISTS `tbl_trans_beli`;
CREATE TABLE IF NOT EXISTS `tbl_trans_beli` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_penerima` int(11) DEFAULT 0,
  `id_supplier` int(11) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_po` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `tgl_bayar` date DEFAULT NULL,
  `tgl_masuk` date DEFAULT NULL,
  `tgl_keluar` date DEFAULT NULL,
  `no_nota` varchar(160) DEFAULT NULL,
  `no_po` varchar(160) DEFAULT NULL,
  `supplier` varchar(160) DEFAULT NULL,
  `jml_total` decimal(32,2) DEFAULT 0.00,
  `disk1` decimal(32,2) DEFAULT 0.00,
  `disk2` decimal(32,2) DEFAULT 0.00,
  `disk3` decimal(32,2) DEFAULT 0.00,
  `jml_potongan` decimal(32,2) DEFAULT 0.00,
  `jml_retur` decimal(32,2) DEFAULT 0.00,
  `jml_diskon` decimal(32,2) DEFAULT 0.00,
  `jml_biaya` decimal(32,2) DEFAULT 0.00,
  `jml_ongkir` decimal(32,2) DEFAULT 0.00,
  `jml_subtotal` decimal(32,2) DEFAULT 0.00,
  `jml_dpp` decimal(32,2) DEFAULT 0.00,
  `ppn` int(11) DEFAULT 0,
  `jml_ppn` decimal(32,2) DEFAULT 0.00,
  `jml_gtotal` decimal(32,2) DEFAULT 0.00,
  `jml_bayar` decimal(32,2) DEFAULT 0.00,
  `jml_kembali` decimal(32,2) DEFAULT 0.00,
  `jml_kurang` decimal(32,2) DEFAULT 0.00,
  `status_bayar` int(11) DEFAULT NULL,
  `status_nota` int(11) DEFAULT 0,
  `status_ppn` enum('0','1','2') DEFAULT NULL,
  `status_retur` enum('0','1') DEFAULT NULL,
  `status_penerimaan` enum('0','1','2','3') DEFAULT '0',
  `metode_bayar` varchar(50) DEFAULT NULL,
  `status_hps` enum('0','1') DEFAULT '0',
  `status_terima` enum('0','1') DEFAULT '0' COMMENT '0=Belum diterima, 1=Sudah diterima',
  `tgl_terima` datetime DEFAULT NULL COMMENT 'Tanggal penerimaan barang',
  `catatan_terima` text DEFAULT NULL COMMENT 'Catatan saat penerimaan barang',
  `id_user_terima` int(11) DEFAULT NULL COMMENT 'ID user yang menerima barang',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_trans_beli_det
DROP TABLE IF EXISTS `tbl_trans_beli_det`;
CREATE TABLE IF NOT EXISTS `tbl_trans_beli_det` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT 0,
  `id_pembelian` int(11) unsigned NOT NULL,
  `id_item` int(11) DEFAULT NULL,
  `id_satuan` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `tgl_masuk` date DEFAULT NULL,
  `tgl_terima` date DEFAULT NULL,
  `tgl_ed` date DEFAULT NULL,
  `kode` varchar(50) DEFAULT NULL,
  `kode_batch` varchar(50) DEFAULT NULL,
  `item` varchar(160) DEFAULT NULL,
  `jml` decimal(10,2) DEFAULT NULL,
  `jml_satuan` int(11) DEFAULT NULL,
  `jml_diterima` int(11) DEFAULT 0,
  `jml_retur` int(11) DEFAULT 0,
  `satuan` varchar(160) DEFAULT NULL,
  `harga` decimal(32,2) DEFAULT NULL,
  `disk1` decimal(10,2) DEFAULT NULL,
  `disk2` decimal(10,2) DEFAULT NULL,
  `disk3` decimal(10,2) DEFAULT NULL,
  `diskon` decimal(32,2) DEFAULT NULL,
  `potongan` decimal(32,2) DEFAULT NULL,
  `subtotal` decimal(32,2) DEFAULT NULL,
  `satuan_retur` varchar(160) DEFAULT NULL,
  `keterangan` varchar(160) DEFAULT NULL,
  `status_item` int(11) DEFAULT NULL,
  `id_gudang` int(11) DEFAULT NULL COMMENT 'ID gudang untuk penerimaan barang',
  `status_terima` enum('1','2','3') DEFAULT NULL COMMENT '1=Diterima, 2=Ditolak, 3=Sebagian',
  `keterangan_terima` text DEFAULT NULL COMMENT 'Keterangan saat penerimaan',
  PRIMARY KEY (`id`),
  KEY `tbl_trans_beli_det_id_pembelian_foreign` (`id_pembelian`),
  CONSTRAINT `tbl_trans_beli_det_id_pembelian_foreign` FOREIGN KEY (`id_pembelian`) REFERENCES `tbl_trans_beli` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_trans_beli_po
DROP TABLE IF EXISTS `tbl_trans_beli_po`;
CREATE TABLE IF NOT EXISTS `tbl_trans_beli_po` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_penerima` int(11) DEFAULT 0,
  `id_supplier` int(11) unsigned DEFAULT NULL,
  `id_user` int(11) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL COMMENT 'Kalau terisi sudah terhapus',
  `tgl_masuk` date DEFAULT NULL,
  `tgl_keluar` date DEFAULT NULL,
  `no_nota` varchar(50) DEFAULT NULL COMMENT 'No Faktur pembelian',
  `supplier` varchar(160) DEFAULT NULL COMMENT 'Nama Supplier',
  `keterangan` text DEFAULT NULL,
  `pengiriman` text DEFAULT NULL,
  `status` int(11) DEFAULT 0,
  `status_hps` enum('0','1') DEFAULT '0' COMMENT '0 = Belum dihapus\\r\\n1 = Dihapus',
  PRIMARY KEY (`id`),
  KEY `tbl_trans_beli_po_id_supplier_foreign` (`id_supplier`),
  CONSTRAINT `tbl_trans_beli_po_id_supplier_foreign` FOREIGN KEY (`id_supplier`) REFERENCES `tbl_m_supplier` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Table untuk menyimpan PO, berelasi ke tbl_trans_beli_po_det';

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_trans_beli_po_det
DROP TABLE IF EXISTS `tbl_trans_beli_po_det`;
CREATE TABLE IF NOT EXISTS `tbl_trans_beli_po_det` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int(11) unsigned NOT NULL DEFAULT 0,
  `id_pembelian` int(11) unsigned NOT NULL,
  `id_item` int(11) unsigned NOT NULL,
  `id_satuan` int(11) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `tgl_masuk` date NOT NULL,
  `kode` varchar(50) DEFAULT NULL,
  `item` varchar(160) DEFAULT NULL,
  `jml` int(11) DEFAULT NULL,
  `jml_satuan` int(11) DEFAULT NULL,
  `satuan` varchar(160) DEFAULT NULL,
  `keterangan` varchar(160) DEFAULT NULL,
  `keterangan_itm` text DEFAULT NULL,
  `status` enum('0','1') DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tbl_trans_beli_po_det_id_pembelian_foreign` (`id_pembelian`),
  KEY `tbl_trans_beli_po_det_id_item_foreign` (`id_item`),
  CONSTRAINT `tbl_trans_beli_po_det_id_item_foreign` FOREIGN KEY (`id_item`) REFERENCES `tbl_m_item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tbl_trans_beli_po_det_id_pembelian_foreign` FOREIGN KEY (`id_pembelian`) REFERENCES `tbl_trans_beli_po` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Table untuk menyimpan PO Detail, berelasi ke tbl_trans_beli_po dan tbl_m_item';

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_trans_jual
DROP TABLE IF EXISTS `tbl_trans_jual`;
CREATE TABLE IF NOT EXISTS `tbl_trans_jual` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `id_sales` int(11) DEFAULT NULL,
  `id_pelanggan` int(11) DEFAULT NULL,
  `id_gudang` int(11) DEFAULT 0,
  `no_nota` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `tgl_bayar` date DEFAULT '0000-00-00',
  `tgl_masuk` date DEFAULT '0000-00-00',
  `tgl_keluar` date DEFAULT '0000-00-00',
  `jml_total` decimal(32,2) DEFAULT 0.00,
  `jml_biaya` decimal(32,2) DEFAULT 0.00,
  `jml_ongkir` decimal(32,2) DEFAULT 0.00,
  `jml_retur` decimal(32,2) DEFAULT 0.00,
  `diskon` decimal(32,2) DEFAULT 0.00,
  `jml_diskon` decimal(32,2) DEFAULT 0.00,
  `jml_subtotal` decimal(32,2) DEFAULT 0.00,
  `ppn` int(11) DEFAULT 0,
  `jml_ppn` decimal(32,2) DEFAULT 0.00,
  `jml_gtotal` decimal(32,2) DEFAULT 0.00,
  `jml_bayar` decimal(32,2) DEFAULT 0.00,
  `jml_kembali` decimal(32,2) DEFAULT 0.00,
  `jml_kurang` decimal(32,2) DEFAULT 0.00,
  `disk1` decimal(32,2) DEFAULT 0.00,
  `jml_disk1` decimal(32,2) DEFAULT 0.00,
  `disk2` decimal(32,2) DEFAULT 0.00,
  `jml_disk2` decimal(32,2) DEFAULT 0.00,
  `disk3` decimal(32,2) DEFAULT 0.00,
  `jml_disk3` decimal(32,2) DEFAULT 0.00,
  `metode_bayar` int(11) DEFAULT NULL,
  `voucher_code` varchar(100) DEFAULT NULL COMMENT 'Voucher code used in transaction',
  `voucher_discount` decimal(10,2) DEFAULT 0.00 COMMENT 'Voucher discount percentage',
  `voucher_id` int(11) DEFAULT NULL COMMENT 'Reference to voucher table',
  `voucher_type` enum('nominal','persen') DEFAULT NULL COMMENT 'Type of voucher: nominal or percentage',
  `voucher_discount_amount` decimal(10,2) DEFAULT 0.00 COMMENT 'Actual voucher discount amount applied',
  `status` enum('0','1','2','3','4') DEFAULT '0' COMMENT '1=pos',
  `qr_scanned` enum('0','1') DEFAULT '0',
  `qr_scan_time` datetime DEFAULT NULL,
  `status_nota` int(11) DEFAULT NULL COMMENT '1=anamnesa, 2=pemeriksaan, 3=tindakan, 4=obat, 5=dokter, 6=pembayaran, 7=finish',
  `status_ppn` enum('0','1') DEFAULT '0',
  `status_bayar` enum('0','1','2') DEFAULT '0',
  `status_retur` enum('0','1','2') DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `no_nota` (`no_nota`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_trans_jual_det
DROP TABLE IF EXISTS `tbl_trans_jual_det`;
CREATE TABLE IF NOT EXISTS `tbl_trans_jual_det` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_penjualan` int(11) unsigned DEFAULT NULL,
  `id_item` int(11) DEFAULT NULL,
  `id_satuan` int(11) DEFAULT NULL,
  `id_kategori` int(11) DEFAULT NULL,
  `id_merk` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `no_nota` varchar(50) DEFAULT NULL,
  `kode` varchar(50) DEFAULT NULL,
  `produk` varchar(256) DEFAULT NULL,
  `satuan` varchar(50) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `harga` decimal(32,2) DEFAULT NULL,
  `harga_beli` decimal(32,2) DEFAULT NULL,
  `jml` int(6) DEFAULT NULL,
  `jml_satuan` int(6) DEFAULT NULL,
  `disk1` decimal(32,2) DEFAULT NULL,
  `disk2` decimal(32,2) DEFAULT NULL,
  `disk3` decimal(32,2) DEFAULT NULL,
  `diskon` decimal(32,2) DEFAULT NULL,
  `potongan` decimal(32,2) DEFAULT NULL,
  `subtotal` decimal(32,2) DEFAULT NULL,
  `status` int(2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tbl_trans_jual_det_id_penjualan_foreign` (`id_penjualan`),
  CONSTRAINT `tbl_trans_jual_det_id_penjualan_foreign` FOREIGN KEY (`id_penjualan`) REFERENCES `tbl_trans_jual` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Table untuk menyimpan detail transaksi penjualan/sales transaction detail';

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_trans_jual_plat
DROP TABLE IF EXISTS `tbl_trans_jual_plat`;
CREATE TABLE IF NOT EXISTS `tbl_trans_jual_plat` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_penjualan` int(11) unsigned NOT NULL DEFAULT 0,
  `id_platform` int(11) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `modified_at` datetime NOT NULL,
  `no_nota` varchar(50) NOT NULL,
  `platform` varchar(160) NOT NULL,
  `keterangan` varchar(160) DEFAULT NULL,
  `nominal` decimal(32,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tbl_trans_jual_plat_id_penjualan_foreign` (`id_penjualan`),
  KEY `tbl_trans_jual_plat_id_platform_foreign` (`id_platform`),
  CONSTRAINT `tbl_trans_jual_plat_id_penjualan_foreign` FOREIGN KEY (`id_penjualan`) REFERENCES `tbl_trans_jual` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tbl_trans_jual_plat_id_platform_foreign` FOREIGN KEY (`id_platform`) REFERENCES `tbl_m_platform` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Table untuk menyimpan data platform pembayaran transaksi penjualan';

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_trans_jual_scan_log
DROP TABLE IF EXISTS `tbl_trans_jual_scan_log`;
CREATE TABLE IF NOT EXISTS `tbl_trans_jual_scan_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `transaction_id` int(11) unsigned NOT NULL,
  `scan_data` text NOT NULL,
  `scan_time` datetime NOT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_trans_mutasi
DROP TABLE IF EXISTS `tbl_trans_mutasi`;
CREATE TABLE IF NOT EXISTS `tbl_trans_mutasi` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_user_terima` int(11) DEFAULT NULL,
  `id_gd_asal` int(11) DEFAULT NULL,
  `id_gd_tujuan` int(11) DEFAULT NULL,
  `id_outlet` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `tgl_masuk` date DEFAULT '0000-00-00',
  `tgl_keluar` date DEFAULT '0000-00-00',
  `no_nota` varchar(50) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `status_nota` enum('0','1','2','3','4') DEFAULT '0',
  `status_terima` enum('0','1','2') DEFAULT '0' COMMENT '1 = Terima, 2 = Tolak',
  `tipe` enum('0','1','2','3','4') DEFAULT '0' COMMENT '1 = Pindah Gudang, 2 = Stok Masuk, 3 = Stok Keluar',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tabel untuk transfer mutasi masuk / keluar';

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_trans_mutasi_det
DROP TABLE IF EXISTS `tbl_trans_mutasi_det`;
CREATE TABLE IF NOT EXISTS `tbl_trans_mutasi_det` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_mutasi` int(11) unsigned NOT NULL DEFAULT 0,
  `id_satuan` int(11) DEFAULT 0,
  `id_item` int(11) DEFAULT 0,
  `id_user` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `no_nota` varchar(50) DEFAULT NULL,
  `kode` varchar(50) DEFAULT NULL,
  `item` varchar(256) DEFAULT NULL,
  `satuan` varchar(50) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `jml` int(6) DEFAULT 0,
  `jml_diterima` int(6) DEFAULT 0,
  `jml_satuan` int(6) DEFAULT NULL,
  `status_brg` enum('0','1') DEFAULT '0',
  `status_terima` enum('0','1') DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_mutasi` (`id_mutasi`),
  CONSTRAINT `tbl_trans_mutasi_det_id_mutasi_foreign` FOREIGN KEY (`id_mutasi`) REFERENCES `tbl_trans_mutasi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tabel untuk transfer mutasi detail masuk / keluar';

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_trans_retur_beli
DROP TABLE IF EXISTS `tbl_trans_retur_beli`;
CREATE TABLE IF NOT EXISTS `tbl_trans_retur_beli` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_beli` int(11) unsigned NOT NULL COMMENT 'Referensi ke tbl_trans_beli.id',
  `id_supplier` int(11) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_user_terima` int(11) DEFAULT NULL COMMENT 'User yang memproses retur',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `tgl_retur` date DEFAULT NULL COMMENT 'Tanggal retur barang',
  `no_nota_retur` varchar(160) DEFAULT NULL,
  `no_nota_asal` varchar(160) DEFAULT NULL COMMENT 'No nota dari pembelian',
  `alasan_retur` text DEFAULT NULL,
  `jml_retur` decimal(32,2) DEFAULT 0.00,
  `jml_potongan` decimal(32,2) DEFAULT 0.00,
  `jml_subtotal` decimal(32,2) DEFAULT 0.00,
  `jml_ppn` decimal(32,2) DEFAULT 0.00,
  `jml_total` decimal(32,2) DEFAULT 0.00,
  `status_ppn` enum('0','1','2') DEFAULT NULL,
  `status_retur` enum('0','1') DEFAULT '0' COMMENT '0=Draft, 1=Selesai',
  `catatan` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Table untuk menyimpan data transaksi retur pembelian';

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_trans_retur_beli_det
DROP TABLE IF EXISTS `tbl_trans_retur_beli_det`;
CREATE TABLE IF NOT EXISTS `tbl_trans_retur_beli_det` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_retur` int(11) unsigned NOT NULL COMMENT 'Referensi ke tbl_trans_retur_beli.id',
  `id_beli_det` int(11) unsigned DEFAULT NULL COMMENT 'Referensi ke tbl_trans_beli_det.id (jika ada)',
  `id_user` int(11) DEFAULT 0,
  `id_item` int(11) DEFAULT NULL,
  `id_satuan` int(11) DEFAULT NULL,
  `id_gudang` int(11) DEFAULT NULL COMMENT 'Gudang asal barang diretur',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `tgl_masuk` date DEFAULT NULL COMMENT 'Tanggal barang masuk ke gudang',
  `tgl_keluar` date DEFAULT NULL COMMENT 'Tanggal barang keluar karena retur',
  `kode` varchar(50) DEFAULT NULL,
  `kode_batch` varchar(50) DEFAULT NULL,
  `item` varchar(160) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `satuan` varchar(160) DEFAULT NULL,
  `jml` decimal(10,2) DEFAULT NULL COMMENT 'Jumlah item yang diretur',
  `harga` decimal(32,2) DEFAULT NULL,
  `disk1` decimal(10,2) DEFAULT NULL,
  `disk2` decimal(10,2) DEFAULT NULL,
  `disk3` decimal(10,2) DEFAULT NULL,
  `diskon` decimal(32,2) DEFAULT NULL,
  `potongan` decimal(32,2) DEFAULT NULL,
  `subtotal` decimal(32,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_retur` (`id_retur`),
  KEY `id_beli_det` (`id_beli_det`),
  CONSTRAINT `fk_retur_beli_det_beli_det` FOREIGN KEY (`id_beli_det`) REFERENCES `tbl_trans_beli_det` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  CONSTRAINT `fk_retur_beli_det_retur` FOREIGN KEY (`id_retur`) REFERENCES `tbl_trans_retur_beli` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Table untuk menyimpan detail retur pembelian barang';

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_trans_retur_jual
DROP TABLE IF EXISTS `tbl_trans_retur_jual`;
CREATE TABLE IF NOT EXISTS `tbl_trans_retur_jual` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_penjualan` int(11) unsigned NOT NULL COMMENT 'Referensi ke tbl_trans_jual.id',
  `id_user` int(11) DEFAULT NULL,
  `id_pelanggan` int(11) DEFAULT NULL,
  `id_sales` int(11) DEFAULT NULL,
  `id_gudang` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `no_nota` varchar(50) NOT NULL,
  `no_retur` varchar(50) NOT NULL,
  `tgl_masuk` date NOT NULL,
  `keterangan` text DEFAULT NULL,
  `status` enum('0','1','2') DEFAULT '0' COMMENT '0=Draft, 1=Diproses, 2=Selesai',
  `status_retur` enum('1','2') DEFAULT '1' COMMENT '1 = refund\r\n2 = retur barang',
  `status_terima` enum('0','1','2') DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_no_retur` (`no_retur`),
  KEY `fk_retur_penjualan` (`id_penjualan`),
  CONSTRAINT `fk_retur_penjualan` FOREIGN KEY (`id_penjualan`) REFERENCES `tbl_trans_jual` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Table untuk menyimpan data retur penjualan';

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_trans_retur_jual_det
DROP TABLE IF EXISTS `tbl_trans_retur_jual_det`;
CREATE TABLE IF NOT EXISTS `tbl_trans_retur_jual_det` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_retur_jual` int(11) unsigned NOT NULL COMMENT 'Referensi ke tbl_trans_retur_jual.id',
  `id_item` int(11) DEFAULT NULL,
  `id_satuan` int(11) DEFAULT NULL,
  `id_gudang` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `kode` varchar(100) DEFAULT NULL,
  `item` varchar(255) DEFAULT NULL,
  `satuan` varchar(100) DEFAULT NULL,
  `harga` decimal(32,2) DEFAULT 0.00,
  `jml` int(6) DEFAULT 0,
  `subtotal` decimal(32,2) DEFAULT 0.00,
  `keterangan` text DEFAULT NULL,
  `status_item` enum('1','2') DEFAULT '1' COMMENT '1=Valid, 2=Ditolak',
  `status_terima` enum('0','1','2') DEFAULT '0' COMMENT '0=Belum, 1=Diterima, 2=Ditolak',
  PRIMARY KEY (`id`),
  KEY `fk_retur_jual_det_retur` (`id_retur_jual`),
  CONSTRAINT `fk_retur_jual_det_retur` FOREIGN KEY (`id_retur_jual`) REFERENCES `tbl_trans_retur_jual` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Table untuk menyimpan detail item retur penjualan';

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_util_so
DROP TABLE IF EXISTS `tbl_util_so`;
CREATE TABLE IF NOT EXISTS `tbl_util_so` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_gudang` int(11) DEFAULT 0,
  `id_outlet` int(11) DEFAULT 0,
  `id_user` int(11) DEFAULT NULL,
  `tgl_masuk` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `reset` enum('0','1') DEFAULT '0',
  `tipe` enum('1','2') DEFAULT '1' COMMENT '1 = Gudang\n2 = Toko',
  `status` enum('0','1','2','3') DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tabel untuk stock opname';

-- Data exporting was unselected.

-- Dumping structure for table db_p54_kopmensa.tbl_util_so_det
DROP TABLE IF EXISTS `tbl_util_so_det`;
CREATE TABLE IF NOT EXISTS `tbl_util_so_det` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_so` int(11) unsigned NOT NULL,
  `id_item` int(11) DEFAULT NULL,
  `id_satuan` int(11) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `tgl_masuk` date DEFAULT NULL,
  `kode` varchar(100) DEFAULT NULL,
  `item` varchar(100) DEFAULT NULL,
  `satuan` varchar(100) DEFAULT NULL,
  `keterangan` longtext DEFAULT NULL,
  `jml` decimal(10,2) DEFAULT NULL,
  `jml_sys` decimal(10,2) DEFAULT NULL,
  `jml_so` decimal(10,2) DEFAULT NULL,
  `jml_sls` decimal(10,2) DEFAULT NULL,
  `jml_satuan` int(11) DEFAULT NULL,
  `sp` varchar(100) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_so` (`id_so`),
  CONSTRAINT `tbl_util_so_det_id_so_foreign` FOREIGN KEY (`id_so`) REFERENCES `tbl_util_so` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tabel untuk stock opname detail';

-- Data exporting was unselected.

-- Dumping structure for view db_p54_kopmensa.v_item_stok
DROP VIEW IF EXISTS `v_item_stok`;
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `v_item_stok` (
	`id_item` INT(11) UNSIGNED NOT NULL,
	`id_gudang` INT(11) UNSIGNED NOT NULL,
	`gudang` VARCHAR(1) NULL COLLATE 'utf8mb4_general_ci',
	`kode` VARCHAR(1) NULL COMMENT 'Kode Item / SKU' COLLATE 'utf8mb4_general_ci',
	`item` VARCHAR(1) NULL COMMENT 'Nama Produk / Item' COLLATE 'utf8mb4_general_ci',
	`so` INT(11) NULL,
	`stok_masuk` DECIMAL(58,2) NULL,
	`stok_keluar` DECIMAL(55,0) NULL,
	`sisa` DECIMAL(60,2) NULL
) ENGINE=MyISAM;

-- Dumping structure for view db_p54_kopmensa.v_trans_jual_cutoff
DROP VIEW IF EXISTS `v_trans_jual_cutoff`;
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `v_trans_jual_cutoff` (
	`id_user` INT(11) NULL,
	`trx_date` DATE NULL,
	`created_at` DATETIME NULL,
	`id_penjualan` INT(11) UNSIGNED NOT NULL,
	`no_nota` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_general_ci',
	`id_gudang` INT(11) NULL,
	`status` ENUM('0','1','2','3','4') NULL COMMENT '1=pos' COLLATE 'utf8mb4_general_ci',
	`status_bayar` ENUM('0','1','2') NULL COLLATE 'utf8mb4_general_ci',
	`status_retur` ENUM('0','1','2') NULL COLLATE 'utf8mb4_general_ci',
	`jml_total` DECIMAL(32,2) NULL,
	`jml_subtotal` DECIMAL(32,2) NULL,
	`jml_diskon_header` DECIMAL(32,2) NULL,
	`jml_ppn` DECIMAL(32,2) NULL,
	`jml_gtotal` DECIMAL(32,2) NULL,
	`jml_bayar` DECIMAL(32,2) NULL,
	`jml_kembali` DECIMAL(32,2) NULL,
	`jml_kurang` DECIMAL(32,2) NULL,
	`jml_biaya` DECIMAL(32,2) NULL,
	`jml_ongkir` DECIMAL(32,2) NULL,
	`jml_retur` DECIMAL(32,2) NULL,
	`metode_bayar` INT(11) NULL,
	`voucher_code` VARCHAR(1) NULL COMMENT 'Voucher code used in transaction' COLLATE 'utf8mb4_general_ci',
	`voucher_type` ENUM('nominal','persen') NULL COMMENT 'Type of voucher: nominal or percentage' COLLATE 'utf8mb4_general_ci',
	`voucher_discount` DECIMAL(10,2) NULL,
	`voucher_discount_amount` DECIMAL(10,2) NULL,
	`id_detail` INT(11) UNSIGNED NOT NULL,
	`id_item` INT(11) NULL,
	`kode` VARCHAR(1) NULL COLLATE 'utf8mb4_general_ci',
	`produk` VARCHAR(1) NULL COLLATE 'utf8mb4_general_ci',
	`satuan` VARCHAR(1) NULL COLLATE 'utf8mb4_general_ci',
	`qty` INT(11) NULL,
	`qty_satuan` INT(11) NULL,
	`harga_satuan` DECIMAL(32,2) NULL,
	`harga_beli` DECIMAL(32,2) NULL,
	`disk1_line` DECIMAL(32,2) NULL,
	`disk2_line` DECIMAL(32,2) NULL,
	`disk3_line` DECIMAL(32,2) NULL,
	`diskon_line` DECIMAL(32,2) NULL,
	`potongan_line` DECIMAL(32,2) NULL,
	`subtotal_line` DECIMAL(32,2) NULL,
	`line_gross` DECIMAL(42,2) NULL
) ENGINE=MyISAM;

-- Dumping structure for view db_p54_kopmensa.v_trans_jual_cutoff_header
DROP VIEW IF EXISTS `v_trans_jual_cutoff_header`;
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `v_trans_jual_cutoff_header` (
	`id_user` INT(11) NULL,
	`trx_date` DATE NULL,
	`created_at` DATETIME NULL,
	`id_penjualan` INT(11) UNSIGNED NOT NULL,
	`no_nota` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_general_ci',
	`id_gudang` INT(11) NULL,
	`jml_total` DECIMAL(32,2) NULL,
	`jml_subtotal` DECIMAL(32,2) NULL,
	`jml_diskon_header` DECIMAL(32,2) NULL,
	`jml_ppn` DECIMAL(32,2) NULL,
	`jml_gtotal` DECIMAL(32,2) NULL,
	`jml_kembali` DECIMAL(32,2) NULL,
	`jml_kurang` DECIMAL(32,2) NULL,
	`jml_biaya` DECIMAL(32,2) NULL,
	`jml_ongkir` DECIMAL(32,2) NULL,
	`jml_retur` DECIMAL(32,2) NULL,
	`item_qty` DECIMAL(32,0) NULL
) ENGINE=MyISAM;

-- Dumping structure for view db_p54_kopmensa.v_trans_jual_cutoff_payment_detail
DROP VIEW IF EXISTS `v_trans_jual_cutoff_payment_detail`;
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `v_trans_jual_cutoff_payment_detail` (
	`id_user` INT(11) NULL,
	`trx_date` DATE NULL,
	`id_penjualan` INT(11) UNSIGNED NOT NULL,
	`no_nota` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_general_ci',
	`created_at` DATETIME NULL,
	`sumber` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_general_ci',
	`id_platform_row` DECIMAL(11,0) NULL,
	`pay_channel` VARCHAR(1) NULL COLLATE 'utf8mb4_general_ci',
	`amount` DECIMAL(32,2) NULL,
	`keterangan` VARCHAR(1) NULL COLLATE 'utf8mb4_general_ci'
) ENGINE=MyISAM;

-- Dumping structure for view db_p54_kopmensa.v_trans_jual_cutoff_payment_summary
DROP VIEW IF EXISTS `v_trans_jual_cutoff_payment_summary`;
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `v_trans_jual_cutoff_payment_summary` (
	`id_user` INT(11) NULL,
	`trx_date` DATE NULL,
	`pay_channel` VARCHAR(1) NULL COLLATE 'utf8mb4_general_ci',
	`total_amount` DECIMAL(54,2) NULL,
	`trx_count` BIGINT(21) NOT NULL
) ENGINE=MyISAM;

-- Dumping structure for view db_p54_kopmensa.v_trans_jual_cutoff_summary
DROP VIEW IF EXISTS `v_trans_jual_cutoff_summary`;
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `v_trans_jual_cutoff_summary` (
	`id_user` INT(11) NULL,
	`trx_date` DATE NULL,
	`trx_count` BIGINT(21) NOT NULL,
	`total_gross` DECIMAL(54,2) NULL,
	`total_subtotal` DECIMAL(54,2) NULL,
	`total_discount` DECIMAL(54,2) NULL,
	`total_tax` DECIMAL(54,2) NULL,
	`total_net` DECIMAL(54,2) NULL,
	`total_paid` DECIMAL(65,2) NULL,
	`total_change` DECIMAL(54,2) NULL,
	`total_due` DECIMAL(54,2) NULL,
	`total_biaya` DECIMAL(54,2) NULL,
	`total_ongkir` DECIMAL(54,2) NULL,
	`total_retur` DECIMAL(54,2) NULL,
	`item_qty` DECIMAL(54,0) NULL
) ENGINE=MyISAM;

-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `v_item_stok`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `v_item_stok` AS WITH
/* ============================ *
   v_item_stok (LIVE, all items x gudang)
   - stok_masuk : beli + retur jual + manual/mutasi masuk (all-time)
   - stok_keluar: jual + retur beli + manual/mutasi keluar (all-time)
   - so         : pergerakan SETELAH SO (after-SO net, bisa +/-)
   - sisa       : stok real = baseline SO + so  (bisa minus)
 * ============================ */
-- pasangan item×gudang dari master stok
dim AS (
  SELECT DISTINCT s.id_item, s.id_gudang
  FROM tbl_m_item_stok s
  -- WHERE s.status = '1'   -- aktif saja? silakan buka jika perlu
),

/* ===== SO terakhir per item×gudang (qty murni) ===== */
so_one AS (
  SELECT id_item, id_gudang, jml AS so_qty, created_at AS so_ts
  FROM (
    SELECT h.*,
           ROW_NUMBER() OVER (
             PARTITION BY h.id_item, h.id_gudang
             ORDER BY h.created_at DESC, h.id DESC
           ) AS rn
    FROM tbl_m_item_hist h
    WHERE h.status='6'
  ) t
  WHERE rn=1
),

/* ===== PEMBELIAN (anti dobel nomor nota) ===== */
beli_hdr_can AS (
  SELECT MIN(id) AS id, no_nota
  FROM tbl_trans_beli
  GROUP BY no_nota
),
beli_per_nota AS (
  SELECT bd.id_item, bd.id_gudang, b.no_nota,
         MIN(COALESCE(bd.created_at, b.created_at)) AS ts,
         SUM(COALESCE(NULLIF(bd.jml_diterima,0), bd.jml, 0)) AS qty
  FROM tbl_trans_beli_det bd
  JOIN beli_hdr_can bc ON bc.id = bd.id_pembelian
  JOIN tbl_trans_beli b ON b.id = bc.id
  GROUP BY bd.id_item, bd.id_gudang, b.no_nota
),

/* ===== PENJUALAN (anti dobel nomor nota) ===== */
jual_hdr_can AS (
  SELECT MIN(id) AS id, no_nota
  FROM tbl_trans_jual
  GROUP BY no_nota
),
jual_per_nota AS (
  SELECT jd.id_item, j.id_gudang, j.no_nota,
         MIN(COALESCE(j.created_at, jd.created_at)) AS ts,
         SUM(CASE WHEN COALESCE(jd.status,1)=1 THEN jd.jml ELSE 0 END) AS qty
  FROM tbl_trans_jual_det jd
  JOIN jual_hdr_can jc ON jc.id = jd.id_penjualan
  JOIN tbl_trans_jual j ON j.id = jc.id
  GROUP BY jd.id_item, j.id_gudang, j.no_nota
),

/* ===== MANUAL MASUK dari HISTORI (status=2) ===== */
hist2 AS (
  SELECT h.id_item, h.id_gudang, h.created_at AS ts, h.jml AS qty
  FROM tbl_m_item_hist h
  WHERE h.status='2'
),

/* ===== MUTASI =====
   - keluar (asal) dari histori status=7 (gudang = h.id_gudang)
   - masuk (tujuan) dari HISTORI status=7 JOIN header mutasi utk ambil id_gd_tujuan
*/
mutasi_out AS (
  SELECT h.id_item,
         h.id_gudang       AS id_gudang,
         h.created_at      AS ts,
         h.jml             AS qty
  FROM tbl_m_item_hist h
  WHERE h.status='7'
),
mutasi_in AS (
  SELECT h.id_item,
         m.id_gd_tujuan    AS id_gudang,
         h.created_at      AS ts,
         h.jml             AS qty
  FROM tbl_m_item_hist h
  JOIN tbl_trans_mutasi m
       ON m.id = h.id_mutasi      -- <<== ganti ke kolom FK sebenarnya jika berbeda
  WHERE h.status='7'
),

/* ===== REKAP ALL-TIME ===== */
masuk_all AS (  -- pembelian + manual + mutasi masuk
  SELECT d.id_item, d.id_gudang,
         COALESCE(b.qty,0) + COALESCE(h2.qty,0) + COALESCE(mi.qty,0) AS qty
  FROM dim d
  LEFT JOIN (SELECT id_item,id_gudang,SUM(qty) qty FROM beli_per_nota GROUP BY 1,2) b
         ON b.id_item=d.id_item AND b.id_gudang=d.id_gudang
  LEFT JOIN (SELECT id_item,id_gudang,SUM(qty) qty FROM hist2 GROUP BY 1,2) h2
         ON h2.id_item=d.id_item AND h2.id_gudang=d.id_gudang
  LEFT JOIN (SELECT id_item,id_gudang,SUM(qty) qty FROM mutasi_in GROUP BY 1,2) mi
         ON mi.id_item=d.id_item AND mi.id_gudang=d.id_gudang
),
keluar_all AS ( -- penjualan + mutasi keluar
  SELECT d.id_item, d.id_gudang,
         COALESCE(j.qty,0) + COALESCE(mo.qty,0) AS qty
  FROM dim d
  LEFT JOIN (SELECT id_item,id_gudang,SUM(qty) qty FROM jual_per_nota GROUP BY 1,2) j
         ON j.id_item=d.id_item AND j.id_gudang=d.id_gudang
  LEFT JOIN (SELECT id_item,id_gudang,SUM(qty) qty FROM mutasi_out GROUP BY 1,2) mo
         ON mo.id_item=d.id_item AND mo.id_gudang=d.id_gudang
),

/* ===== PERGERAKAN SETELAH SO (dipakai untuk 'sisa') ===== */
masuk_after AS (
  SELECT d.id_item, d.id_gudang,
         /* pembelian setelah SO */
         COALESCE((
           SELECT SUM(CASE WHEN so.so_ts IS NULL OR bp.ts > so.so_ts THEN bp.qty ELSE 0 END)
           FROM beli_per_nota bp
           LEFT JOIN so_one so ON so.id_item=d.id_item AND so.id_gudang=d.id_gudang
           WHERE bp.id_item=d.id_item AND bp.id_gudang=d.id_gudang
         ),0)
       + /* manual(2) setelah SO */
         COALESCE((
           SELECT SUM(CASE WHEN so.so_ts IS NULL OR h.ts > so.so_ts THEN h.qty ELSE 0 END)
           FROM hist2 h
           LEFT JOIN so_one so ON so.id_item=d.id_item AND so.id_gudang=d.id_gudang
           WHERE h.id_item=d.id_item AND h.id_gudang=d.id_gudang
         ),0)
       + /* mutasi MASUK (tujuan) setelah SO */
         COALESCE((
           SELECT SUM(CASE WHEN so.so_ts IS NULL OR mi.ts > so.so_ts THEN mi.qty ELSE 0 END)
           FROM mutasi_in mi
           LEFT JOIN so_one so ON so.id_item=d.id_item AND so.id_gudang=d.id_gudang
           WHERE mi.id_item=d.id_item AND mi.id_gudang=d.id_gudang
         ),0) AS qty
  FROM dim d
),
keluar_after AS (
  SELECT d.id_item, d.id_gudang,
         /* penjualan setelah SO */
         COALESCE((
           SELECT SUM(CASE WHEN so.so_ts IS NULL OR jp.ts > so.so_ts THEN jp.qty ELSE 0 END)
           FROM jual_per_nota jp
           LEFT JOIN so_one so ON so.id_item=d.id_item AND so.id_gudang=d.id_gudang
           WHERE jp.id_item=d.id_item AND jp.id_gudang=d.id_gudang
         ),0)
       + /* mutasi KELUAR (asal) setelah SO */
         COALESCE((
           SELECT SUM(CASE WHEN so.so_ts IS NULL OR mo.ts > so.so_ts THEN mo.qty ELSE 0 END)
           FROM mutasi_out mo
           LEFT JOIN so_one so ON so.id_item=d.id_item AND so.id_gudang=d.id_gudang
           WHERE mo.id_item=d.id_item AND mo.id_gudang=d.id_gudang
         ),0) AS qty
  FROM dim d
)

SELECT
  i.id   AS id_item,
  g.id   AS id_gudang,
  g.nama AS gudang,
  i.kode,
  i.item,

  /* SO murni */
  COALESCE(so.so_qty,0)        AS so,

  /* rekap all-time untuk info */
  COALESCE(ma.qty,0)           AS stok_masuk,   -- beli + manual(2) + mutasi masuk
  COALESCE(ka.qty,0)           AS stok_keluar,  -- jual + mutasi keluar

  /* stok real-time */
  CASE
    WHEN so.so_ts IS NULL
      THEN COALESCE(ma.qty,0) - COALESCE(ka.qty,0)
    ELSE COALESCE(so.so_qty,0)
       +  COALESCE(mf.qty,0)   -- masuk setelah SO
       -  COALESCE(kf.qty,0)   -- keluar setelah SO
  END AS sisa

FROM dim d
JOIN tbl_m_item   i ON i.id = d.id_item
JOIN tbl_m_gudang g ON g.id = d.id_gudang
LEFT JOIN so_one      so ON so.id_item=d.id_item AND so.id_gudang=d.id_gudang
LEFT JOIN masuk_all   ma ON ma.id_item=d.id_item AND ma.id_gudang=d.id_gudang
LEFT JOIN keluar_all  ka ON ka.id_item=d.id_item AND ka.id_gudang=d.id_gudang
LEFT JOIN masuk_after mf ON mf.id_item=d.id_item AND mf.id_gudang=d.id_gudang
LEFT JOIN keluar_after kf ON kf.id_item=d.id_item AND kf.id_gudang=d.id_gudang
ORDER BY i.item, g.nama ;

-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `v_trans_jual_cutoff`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `v_trans_jual_cutoff` AS SELECT
  j.id_user,
  DATE(j.created_at)                       AS trx_date,
  j.created_at,
  j.id                                      AS id_penjualan,
  j.no_nota,
  j.id_gudang,

  j.status,
  j.status_bayar,
  j.status_retur,

  COALESCE(j.jml_total,0)                  AS jml_total,
  COALESCE(j.jml_subtotal,0)               AS jml_subtotal,
  COALESCE(j.jml_diskon,0)                 AS jml_diskon_header,
  COALESCE(j.jml_ppn,0)                    AS jml_ppn,
  COALESCE(j.jml_gtotal,0)                 AS jml_gtotal,
  COALESCE(j.jml_bayar,0)                  AS jml_bayar,
  COALESCE(j.jml_kembali,0)                AS jml_kembali,
  COALESCE(j.jml_kurang,0)                 AS jml_kurang,
  COALESCE(j.jml_biaya,0)                  AS jml_biaya,
  COALESCE(j.jml_ongkir,0)                 AS jml_ongkir,
  COALESCE(j.jml_retur,0)                  AS jml_retur,
  j.metode_bayar,
  j.voucher_code,
  j.voucher_type,
  COALESCE(j.voucher_discount,0)           AS voucher_discount,
  COALESCE(j.voucher_discount_amount,0)    AS voucher_discount_amount,

  jd.id                                     AS id_detail,
  jd.id_item,
  jd.kode,
  jd.produk,
  jd.satuan,
  COALESCE(jd.jml,0)                        AS qty,
  COALESCE(jd.jml_satuan,0)                 AS qty_satuan,
  COALESCE(jd.harga,0)                      AS harga_satuan,
  COALESCE(jd.harga_beli,0)                 AS harga_beli,
  COALESCE(jd.disk1,0)                      AS disk1_line,
  COALESCE(jd.disk2,0)                      AS disk2_line,
  COALESCE(jd.disk3,0)                      AS disk3_line,
  COALESCE(jd.diskon,0)                     AS diskon_line,
  COALESCE(jd.potongan,0)                   AS potongan_line,
  COALESCE(jd.subtotal,0)                   AS subtotal_line,
  (COALESCE(jd.jml,0) * COALESCE(jd.harga,0)) AS line_gross
FROM tbl_trans_jual j
JOIN tbl_trans_jual_det jd ON jd.id_penjualan = j.id
WHERE j.deleted_at IS NULL
  AND j.status_bayar = '1'
  AND j.created_at IS NOT NULL
  AND TIME(j.created_at) BETWEEN '00:00:01' AND '23:59:59' ;

-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `v_trans_jual_cutoff_header`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `v_trans_jual_cutoff_header` AS SELECT
  j.id_user,
  DATE(j.created_at)            AS trx_date,
  j.created_at,
  j.id                          AS id_penjualan,
  j.no_nota,
  j.id_gudang,

  COALESCE(j.jml_total,0)       AS jml_total,
  COALESCE(j.jml_subtotal,0)    AS jml_subtotal,
  COALESCE(j.jml_diskon,0)      AS jml_diskon_header,
  COALESCE(j.jml_ppn,0)         AS jml_ppn,
  COALESCE(j.jml_gtotal,0)      AS jml_gtotal,
  COALESCE(j.jml_kembali,0)     AS jml_kembali,
  COALESCE(j.jml_kurang,0)      AS jml_kurang,
  COALESCE(j.jml_biaya,0)       AS jml_biaya,
  COALESCE(j.jml_ongkir,0)      AS jml_ongkir,
  COALESCE(j.jml_retur,0)       AS jml_retur,
  COALESCE(d.item_qty,0)        AS item_qty
FROM tbl_trans_jual j
LEFT JOIN (
  SELECT id_penjualan, SUM(COALESCE(jml,0)) AS item_qty
  FROM tbl_trans_jual_det
  GROUP BY id_penjualan
) d ON d.id_penjualan = j.id
WHERE j.deleted_at IS NULL
  AND j.status_bayar = '1'
  AND j.created_at IS NOT NULL
  AND TIME(j.created_at) BETWEEN '00:00:01' AND '23:59:59' ;

-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `v_trans_jual_cutoff_payment_detail`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `v_trans_jual_cutoff_payment_detail` AS SELECT
  j.id_user,
  DATE(j.created_at)        AS trx_date,
  j.id                      AS id_penjualan,
  j.no_nota,
  j.created_at,
  'PLATFORM'                AS sumber,
  p.id                      AS id_platform_row,
  p.platform                AS pay_channel,
  COALESCE(p.nominal,0)     AS amount,
  p.keterangan              AS keterangan
FROM tbl_trans_jual j
JOIN tbl_trans_jual_plat p ON p.id_penjualan = j.id
WHERE j.deleted_at IS NULL
  AND j.status_bayar = '1'
  AND j.created_at IS NOT NULL
  AND TIME(j.created_at) BETWEEN '00:00:01' AND '23:59:59'

UNION ALL
SELECT
  j.id_user,
  DATE(j.created_at)        AS trx_date,
  j.id                      AS id_penjualan,
  j.no_nota,
  j.created_at,
  'HEADER'                  AS sumber,
  NULL                      AS id_platform_row,
  CONCAT('METODE_', COALESCE(j.metode_bayar,0)) AS pay_channel,
  COALESCE(j.jml_gtotal,0)  AS amount,
  NULL                      AS keterangan
FROM tbl_trans_jual j
LEFT JOIN tbl_trans_jual_plat p ON p.id_penjualan = j.id
WHERE p.id IS NULL
  AND j.deleted_at IS NULL
  AND j.status_bayar = '1'
  AND j.created_at IS NOT NULL
  AND TIME(j.created_at) BETWEEN '00:00:01' AND '23:59:59' ;

-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `v_trans_jual_cutoff_payment_summary`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `v_trans_jual_cutoff_payment_summary` AS SELECT
  id_user,
  trx_date,
  pay_channel,
  SUM(amount) AS total_amount,
  COUNT(DISTINCT id_penjualan) AS trx_count
FROM v_trans_jual_cutoff_payment_detail
GROUP BY id_user, trx_date, pay_channel ;

-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `v_trans_jual_cutoff_summary`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `v_trans_jual_cutoff_summary` AS SELECT
  h.id_user,
  h.trx_date,
  COUNT(*)                                     AS trx_count,         -- jumlah nota
  SUM(h.jml_total)                             AS total_gross,
  SUM(h.jml_subtotal)                          AS total_subtotal,
  SUM(h.jml_diskon_header)                     AS total_discount,
  SUM(h.jml_ppn)                               AS total_tax,
  SUM(h.jml_gtotal)                            AS total_net,
  COALESCE(SUM(pp.paid_amount),0)              AS total_paid,        -- ← dari platform / fallback
  SUM(h.jml_kembali)                           AS total_change,
  SUM(h.jml_kurang)                            AS total_due,
  SUM(h.jml_biaya)                             AS total_biaya,
  SUM(h.jml_ongkir)                            AS total_ongkir,
  SUM(h.jml_retur)                             AS total_retur,       -- 0 jika header 0
  SUM(h.item_qty)                              AS item_qty
FROM v_trans_jual_cutoff_header h
LEFT JOIN (
  SELECT id_penjualan, SUM(amount) AS paid_amount
  FROM v_trans_jual_cutoff_payment_detail
  GROUP BY id_penjualan
) pp ON pp.id_penjualan = h.id_penjualan
GROUP BY h.id_user, h.trx_date ;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
