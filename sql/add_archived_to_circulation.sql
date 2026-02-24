-- Migration: Add archived column to circulation table
-- Created: February 24, 2026
-- Purpose: Enable soft delete for circulation records

-- Add archived column to circulation table
ALTER TABLE `circulation` 
ADD COLUMN `archived` TINYINT(1) NOT NULL DEFAULT 0 AFTER `fine_amount`,
ADD INDEX `archived` (`archived`);

-- Update comment
ALTER TABLE `circulation` COMMENT = 'Book borrowing records with soft delete support';
