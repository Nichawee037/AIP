CREATE DATABASE IF NOT EXISTS school_repair
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE school_repair;

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  fullname VARCHAR(120) NOT NULL,
  role ENUM('admin','teacher','technician') NOT NULL DEFAULT 'teacher',
  email VARCHAR(120) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS repair_requests (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  request_no VARCHAR(20) NOT NULL UNIQUE,
  user_id INT UNSIGNED NOT NULL,
  building VARCHAR(100) NOT NULL,
  room VARCHAR(50) NOT NULL,
  problem_type ENUM('electrical','furniture','computer','aircon','other') NOT NULL DEFAULT 'other',
  description TEXT NOT NULL,
  urgency ENUM('low','medium','high') NOT NULL DEFAULT 'medium',
  status ENUM('pending','accepted','in_progress','completed') NOT NULL DEFAULT 'pending',
  assigned_to INT UNSIGNED NULL,
  due_date DATE NULL,
  completion_date DATE NULL,
  cost DECIMAL(10,2) NULL,
  result TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_repair_user FOREIGN KEY (user_id) REFERENCES users(id),
  CONSTRAINT fk_repair_technician FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_repair_status (status),
  INDEX idx_repair_created (created_at)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS repair_images (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  repair_id INT UNSIGNED NOT NULL,
  image_type ENUM('before','after') NOT NULL,
  image_path VARCHAR(255) NOT NULL,
  uploaded_by INT UNSIGNED NOT NULL,
  uploaded_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_image_repair FOREIGN KEY (repair_id) REFERENCES repair_requests(id) ON DELETE CASCADE,
  CONSTRAINT fk_image_user FOREIGN KEY (uploaded_by) REFERENCES users(id),
  INDEX idx_image_repair (repair_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS comments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  repair_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  comment TEXT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_comment_repair FOREIGN KEY (repair_id) REFERENCES repair_requests(id) ON DELETE CASCADE,
  CONSTRAINT fk_comment_user FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;
