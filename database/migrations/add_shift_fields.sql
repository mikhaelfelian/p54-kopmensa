-- Migration: Add new fields to tbl_m_shift table
-- Created: 2025-01-18
-- Description: Adds nama_shift, catatan_shift, and approved_at columns to shift table

-- Add nama_shift column (shift name)
ALTER TABLE `tbl_m_shift` 
ADD COLUMN `nama_shift` VARCHAR(100) NULL AFTER `shift_code`;

-- Add catatan_shift column (shift notes)
ALTER TABLE `tbl_m_shift` 
ADD COLUMN `catatan_shift` TEXT NULL AFTER `notes`;

-- Add approved_at column (approval timestamp)
ALTER TABLE `tbl_m_shift` 
ADD COLUMN `approved_at` DATETIME NULL AFTER `updated_at`;

-- Add index for faster queries
ALTER TABLE `tbl_m_shift` 
ADD INDEX `idx_user_status` (`user_open_id`, `status`),
ADD INDEX `idx_approved_at` (`approved_at`);

