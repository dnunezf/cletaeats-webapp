-- ============================================================
-- SCHEMA
-- ============================================================

-- Drops
CREATE DATABASE CLETATEST;
USE CLETATEST;

DROP TABLE IF EXISTS complaints;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS invoice_lines;
DROP TABLE IF EXISTS combos;
DROP TABLE IF EXISTS restaurants;
DROP TABLE IF EXISTS customers;
DROP TABLE IF EXISTS drivers;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS locations;

CREATE TABLE locations(
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    address VARCHAR(255) NOT NULL,
    city VARCHAR(255) NOT NULL,
    postal_code VARCHAR(255) NOT NULL
);

CREATE TABLE users(
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('customer','driver','restaurant','admin') NOT NULL DEFAULT 'customer',
    status ENUM('inactive','active') NOT NULL DEFAULT 'inactive',
    document VARCHAR(255) NOT NULL,
    location_id INT UNSIGNED NOT NULL
);
ALTER TABLE users ADD UNIQUE users_document_role_unique(document, role);
ALTER TABLE users ADD UNIQUE users_email_unique(email);

CREATE TABLE customers(
    user_id INT UNSIGNED NOT NULL,
    card_number VARCHAR(255) NOT NULL,
    PRIMARY KEY(user_id)
);

CREATE TABLE restaurants(
    user_id INT UNSIGNED NOT NULL,
    category ENUM('typical','chinese','italian','healthy') NOT NULL DEFAULT 'typical',
    PRIMARY KEY(user_id)
);

CREATE TABLE drivers(
    user_id INT UNSIGNED NOT NULL,
    status ENUM('available','occupied') NOT NULL DEFAULT 'available',
    penalties TINYINT NOT NULL DEFAULT 0,
    card_number VARCHAR(255) NOT NULL,
    km_cost_regular DECIMAL(8,2) NOT NULL,
    km_cost_holidays DECIMAL(8,2) NOT NULL,
    PRIMARY KEY(user_id)
);

CREATE TABLE combos(
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    restaurant_id INT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    description VARCHAR(255) NOT NULL,
    price DECIMAL(8,2) NOT NULL
);

CREATE TABLE orders(
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED NOT NULL,
    driver_id INT UNSIGNED NOT NULL,
    status ENUM('pending','ongoing','delivered','cancelled') NOT NULL DEFAULT 'pending',
    creation_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    delivered_date DATE NULL DEFAULT NULL,
    costumer_card_number VARCHAR(255) NOT NULL,
    driver_card_number VARCHAR(255) NOT NULL
);

CREATE TABLE complaints(
    order_id INT UNSIGNED NOT NULL,
    content VARCHAR(255) NOT NULL,
    rating TINYINT NOT NULL,
    PRIMARY KEY(order_id)
);

CREATE TABLE invoice_lines(
    combo_id INT UNSIGNED NOT NULL,
    order_id INT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    combo_price DECIMAL(8,2) NOT NULL,
    PRIMARY KEY(combo_id, order_id)
);

ALTER TABLE users           ADD CONSTRAINT users_location_id_foreign            FOREIGN KEY(location_id)    REFERENCES locations(id);
ALTER TABLE customers       ADD CONSTRAINT customers_user_id_foreign            FOREIGN KEY(user_id)        REFERENCES users(id);
ALTER TABLE restaurants     ADD CONSTRAINT restaurants_user_id_foreign          FOREIGN KEY(user_id)        REFERENCES users(id);
ALTER TABLE drivers         ADD CONSTRAINT drivers_user_id_foreign              FOREIGN KEY(user_id)        REFERENCES users(id);
ALTER TABLE combos          ADD CONSTRAINT combos_restaurant_id_foreign         FOREIGN KEY(restaurant_id)  REFERENCES restaurants(user_id);
ALTER TABLE orders          ADD CONSTRAINT orders_customer_id_foreign           FOREIGN KEY(customer_id)    REFERENCES customers(user_id);
ALTER TABLE orders          ADD CONSTRAINT orders_driver_id_foreign             FOREIGN KEY(driver_id)      REFERENCES drivers(user_id);
ALTER TABLE invoice_lines   ADD CONSTRAINT invoice_lines_combo_id_foreign       FOREIGN KEY(combo_id)       REFERENCES combos(id);
ALTER TABLE invoice_lines   ADD CONSTRAINT invoice_lines_order_id_foreign       FOREIGN KEY(order_id)       REFERENCES orders(id);
ALTER TABLE complaints      ADD CONSTRAINT complaints_order_id_foreign          FOREIGN KEY(order_id)       REFERENCES orders(id);


