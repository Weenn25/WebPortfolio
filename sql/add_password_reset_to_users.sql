-- Migration: Add password reset fields to users table
-- Created: February 24, 2026
-- Purpose: Enable password reset functionality

-- Check and add password_reset_token column if it doesn't exist
ALTER TABLE `users` 
ADD COLUMN `password_reset_token` VARCHAR(100) NULL UNIQUE;

-- Add password_reset_expires column if it doesn't exist
ALTER TABLE `users` 
ADD COLUMN `password_reset_expires` DATETIME NULL;

-- Update comment
ALTER TABLE `users` COMMENT = 'User authentication with password reset support';
