-- Run once: mysql -u root -p < setup.sql
CREATE DATABASE IF NOT EXISTS college_db
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE college_db;

CREATE TABLE IF NOT EXISTS students (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  name            VARCHAR(100) NOT NULL,
  email           VARCHAR(150) NOT NULL UNIQUE,
  enrollment_date DATE NOT NULL
) ENGINE=InnoDB;

-- Used by Q12, Q15, Q21
CREATE TABLE IF NOT EXISTS users (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  username      VARCHAR(50)  NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Used by Q19
CREATE TABLE IF NOT EXISTS accounts (
  id      INT AUTO_INCREMENT PRIMARY KEY,
  owner   VARCHAR(100) NOT NULL,
  balance DECIMAL(10,2) NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS transfer_log (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  from_account INT NOT NULL,
  to_account   INT NOT NULL,
  amount       DECIMAL(10,2) NOT NULL,
  created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT IGNORE INTO accounts (id, owner, balance) VALUES (1,'Alice',1000),(2,'Bob',500);
