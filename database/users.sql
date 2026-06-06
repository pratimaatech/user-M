-- =====================================================
-- User Management System - Database
-- =====================================================

CREATE DATABASE IF NOT EXISTS user_management;
USE user_management;

-- Admin login table
DROP TABLE IF EXISTS admin;
CREATE TABLE admin (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert admin: username=admin, password=admin123
-- Hash generated with: password_hash('admin123', PASSWORD_BCRYPT)
INSERT INTO admin (username, password) VALUES (
    'admin',
    '$2y$10$e0NRusJuXMGsNy3dMWJ5JuRtw3VtA5Q9VYMkN8mK2YWLqoV5FcvKG'
);

-- Users table
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(100) NOT NULL UNIQUE,
    phone      VARCHAR(15)  NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample Indian users
INSERT INTO users (name, email, phone) VALUES
('Rahul Sharma',  'rahul@example.com',  '9876543210'),
('Priya Verma',   'priya@example.com',  '9823456781'),
('Amit Kumar',    'amit@example.com',   '9712345678'),
('Neha Singh',    'neha@example.com',   '9645123456'),
('Suresh Yadav',  'suresh@example.com', '9534561230'),
('Anjali Gupta',  'anjali@example.com', '9423456789'),
('Vikram Patel',  'vikram@example.com', '9312345670'),
('Pooja Meena',   'pooja@example.com',  '9201234567');
