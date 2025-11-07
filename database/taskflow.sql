-- TaskFlow Database Schema
-- Run this script in phpMyAdmin or psql (depending on your DB)

CREATE DATABASE IF NOT EXISTS taskflow;
USE taskflow;

-- ==========================
-- 1. Employers Table
-- ==========================
CREATE TABLE employers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    contact_number VARCHAR(20),
    location VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==========================
-- 2. Employees Table
-- ==========================
CREATE TABLE employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    skills TEXT,
    location VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==========================
-- 3. Tasks Table
-- ==========================
CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employer_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(100),
    budget DECIMAL(10,2),
    deadline DATE,
    status ENUM('open', 'closed') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employer_id) REFERENCES employers(id) ON DELETE CASCADE
);

-- ==========================
-- 4. Applications Table
-- ==========================
CREATE TABLE applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT NOT NULL,
    employee_id INT NOT NULL,
    cover_letter TEXT,
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
);

-- ==========================
-- 5. Sample Data (Optional)
-- ==========================

-- Employers
INSERT INTO employers (company_name, email, password, contact_number, location)
VALUES
('Tech Solutions', 'tech@company.com', '$2y$10$Kix8Dk9uqp7KZy9XYT7JYOQx4wXzA4y3bXqx7mkg41ySytquZvh3a', '0712345678', 'Nairobi'),
('Creative Minds', 'creative@company.com', '$2y$10$Kix8Dk9uqp7KZy9XYT7JYOQx4wXzA4y3bXqx7mkg41ySytquZvh3a', '0722334455', 'Mombasa');

-- Employees
INSERT INTO employees (full_name, email, password, skills, location)
VALUES
('John Doe', 'john@example.com', '$2y$10$Kix8Dk9uqp7KZy9XYT7JYOQx4wXzA4y3bXqx7mkg41ySytquZvh3a', 'PHP, JavaScript, HTML', 'Nakuru'),
('Jane Smith', 'jane@example.com', '$2y$10$Kix8Dk9uqp7KZy9XYT7JYOQx4wXzA4y3bXqx7mkg41ySytquZvh3a', 'Graphic Design, CSS, Figma', 'Kisumu');

-- Tasks
INSERT INTO tasks (employer_id, title, description, category, budget, deadline)
VALUES
(1, 'Website Developer Needed', 'Build a company website using PHP and MySQL.', 'IT & Software', 50000.00, '2025-12-15'),
(2, 'Logo Design Project', 'Design a professional logo for a startup.', 'Design & Art', 10000.00, '2025-12-05');

-- Applications
INSERT INTO applications (task_id, employee_id, cover_letter, status)
VALUES
(1, 1, 'I have 3 years of experience in PHP development.', 'pending'),
(2, 2, 'I can deliver a modern logo design in 3 days.', 'pending');
