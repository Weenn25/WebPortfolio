-- Library Management System Database Schema
-- Created: February 18, 2026

-- Users Table (for login and authentication)
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL UNIQUE,
  `email` varchar(100) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','librarian','member') DEFAULT 'member',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Books Table
CREATE TABLE IF NOT EXISTS `books` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `isbn` varchar(20) UNIQUE,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `publisher` varchar(255),
  `publication_year` int(4),
  `description` text,
  `total_quantity` int(11) NOT NULL DEFAULT 1,
  `available_quantity` int(11) NOT NULL DEFAULT 1,
  `archived` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `title` (`title`),
  KEY `author` (`author`),
  KEY `archived` (`archived`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Members Table
CREATE TABLE IF NOT EXISTS `members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(100),
  `phone` varchar(20),
  `address` text,
  `membership_date` date NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Circulation Table (Book Borrowing Records)
CREATE TABLE IF NOT EXISTS `circulation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `book_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `borrow_date` date NOT NULL,
  `due_date` date NOT NULL,
  `return_date` date,
  `status` enum('borrowed','returned','overdue') DEFAULT 'borrowed',
  `fine_amount` decimal(10, 2) DEFAULT 0,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `book_id` (`book_id`),
  KEY `member_id` (`member_id`),
  KEY `status` (`status`),
  FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data
INSERT INTO `users` (`first_name`, `last_name`, `username`, `email`, `password`, `role`, `is_active`) VALUES
('Admin', 'User', 'admin', 'admin@library.local', '$2y$10$OXE3VYE/rB8hZdweBKU04O1tpVMVKWVkqPeVNKczJIpqMf5vz1Fia', 'admin', 1),
('Librarian', 'Staff', 'librarian', 'librarian@library.local', '$2y$10$OXE3VYE/rB8hZdweBKU04O1tpVMVKWVkqPeVNKczJIpqMf5vz1Fia', 'librarian', 1);
-- Default password for both accounts: password123

INSERT INTO `books` (`isbn`, `title`, `author`, `publisher`, `publication_year`, `total_quantity`, `available_quantity`) VALUES
('978-0-06-112008-4', 'To Kill a Mockingbird', 'Harper Lee', 'J. B. Lippincott', 1960, 5, 5),
('978-0-448-11459-5', 'The Great Gatsby', 'F. Scott Fitzgerald', 'Scribner', 1925, 3, 3),
('978-0451524935', '1984', 'George Orwell', 'Signet Classic', 1949, 4, 4),
('978-0451526342', 'Pride and Prejudice', 'Jane Austen', 'Dover Publications', 1813, 2, 2);

INSERT INTO `members` (`first_name`, `last_name`, `email`, `phone`, `address`, `membership_date`) VALUES
('John', 'Doe', 'john@example.com', '555-0101', '123 Main St', '2024-01-15'),
('Jane', 'Smith', 'jane@example.com', '555-0102', '456 Oak Ave', '2024-01-20');
