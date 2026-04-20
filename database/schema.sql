-- ============================================
-- CletaEats Web App - Database Schema
-- Run this script against the local MySQL database (cletaeats).
-- Compatible with MySQL 5.5+
-- ============================================

-- NOTE: The old mobile app used a different 'users' table schema.
-- This script drops it and recreates with the web app schema.
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(50)  NOT NULL UNIQUE,
    email         VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role          ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    is_active     TINYINT(1) NOT NULL DEFAULT 1,
    status        ENUM('pending', 'active') NOT NULL DEFAULT 'active',
    created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME DEFAULT NULL,
    INDEX idx_users_username (username),
    INDEX idx_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS customers (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name   VARCHAR(50)  NOT NULL,
    last_name    VARCHAR(50)  NOT NULL,
    email        VARCHAR(100) NOT NULL UNIQUE,
    phone_number VARCHAR(20)  DEFAULT NULL,
    address      VARCHAR(255) DEFAULT NULL,
    city         VARCHAR(100) DEFAULT NULL,
    postal_code  VARCHAR(10)  DEFAULT NULL,
    is_active    TINYINT(1) NOT NULL DEFAULT 1,
    created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME DEFAULT NULL,
    INDEX idx_customers_email (email),
    INDEX idx_customers_name (first_name, last_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS restaurants (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name              VARCHAR(100)  NOT NULL,
    legal_id          VARCHAR(30)   NOT NULL UNIQUE,
    address           VARCHAR(255)  NOT NULL,
    food_type         VARCHAR(50)   NOT NULL,
    combo_name        VARCHAR(100)  NOT NULL,
    combo_description VARCHAR(255)  DEFAULT NULL,
    combo_price       DECIMAL(10,2) NOT NULL,
    is_active         TINYINT(1)    NOT NULL DEFAULT 1,
    created_at        TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        DATETIME      DEFAULT NULL,
    INDEX idx_restaurants_legal_id (legal_id),
    INDEX idx_restaurants_name (name),
    INDEX idx_restaurants_food_type (food_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
