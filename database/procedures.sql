DELIMITER //

-- ============================================
-- Drop All Existing Procedures
-- ============================================

DROP PROCEDURE IF EXISTS sp_user_get_by_username //
DROP PROCEDURE IF EXISTS sp_user_get_by_id //
DROP PROCEDURE IF EXISTS sp_user_check_username //
DROP PROCEDURE IF EXISTS sp_user_check_email //
DROP PROCEDURE IF EXISTS sp_user_create //

DROP PROCEDURE IF EXISTS sp_restaurant_get_by_id //
DROP PROCEDURE IF EXISTS sp_restaurant_get_first //
DROP PROCEDURE IF EXISTS sp_restaurant_get_all //
DROP PROCEDURE IF EXISTS sp_restaurant_count_active //
DROP PROCEDURE IF EXISTS sp_restaurant_search //

DROP PROCEDURE IF EXISTS sp_order_create //
DROP PROCEDURE IF EXISTS sp_order_get_all //
DROP PROCEDURE IF EXISTS sp_order_count //
DROP PROCEDURE IF EXISTS sp_order_get_by_id //
DROP PROCEDURE IF EXISTS sp_order_get_by_user //
DROP PROCEDURE IF EXISTS sp_order_update //
DROP PROCEDURE IF EXISTS sp_order_delete //

DROP PROCEDURE IF EXISTS sp_customer_get_by_id //
DROP PROCEDURE IF EXISTS sp_customer_create //
DROP PROCEDURE IF EXISTS sp_customer_update //
DROP PROCEDURE IF EXISTS sp_customer_delete //

-- ============================================
-- User Procedures
-- ============================================

CREATE PROCEDURE sp_user_get_by_username(IN p_username VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci)
BEGIN
    SELECT 
        id,
        username,
        email,
        password_hash AS password,
        role,
        '' AS first_name,
        '' AS last_name,
        '' AS image
    FROM users 
    WHERE username = p_username
    LIMIT 1;
END //

CREATE PROCEDURE sp_user_get_by_id(IN p_id INT)
BEGIN
    SELECT 
        id,
        username,
        email,
        password_hash AS password,
        role,
        '' AS first_name,
        '' AS last_name,
        '' AS image
    FROM users 
    WHERE id = p_id
    LIMIT 1;
END //

CREATE PROCEDURE sp_user_check_username(IN p_username VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci)
BEGIN
    SELECT id FROM users WHERE username = p_username LIMIT 1;
END //

CREATE PROCEDURE sp_user_check_email(IN p_email VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci)
BEGIN
    SELECT id FROM users WHERE email = p_email LIMIT 1;
END //

CREATE PROCEDURE sp_user_create(
    IN p_username VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN p_email VARCHAR(100),
    IN p_password_hash VARCHAR(255),
    IN p_role VARCHAR(20),
    IN p_status VARCHAR(20)
)
BEGIN
    INSERT INTO users (username, email, password_hash, role, status)
    VALUES (p_username, p_email, p_password_hash, p_role, p_status);
    SELECT LAST_INSERT_ID() AS user_id;
END //

-- ============================================
-- Restaurant/Product Procedures
-- ============================================

CREATE PROCEDURE sp_restaurant_get_by_id(IN p_id INT)
BEGIN
    SELECT id, name, combo_name, combo_description, combo_price, food_type, is_active
    FROM restaurants
    WHERE id = p_id AND is_active = 1
    LIMIT 1;
END //

CREATE PROCEDURE sp_restaurant_get_first()
BEGIN
    SELECT id, name, combo_name, combo_price, combo_description FROM restaurants LIMIT 1;
END //

CREATE PROCEDURE sp_restaurant_get_all(IN p_limit INT, IN p_offset INT)
BEGIN
    SELECT id, name, combo_name, combo_description, combo_price, food_type, is_active
    FROM restaurants
    WHERE is_active = 1
    LIMIT p_limit OFFSET p_offset;
END //

CREATE PROCEDURE sp_restaurant_count_active()
BEGIN
    SELECT COUNT(*) AS total FROM restaurants WHERE is_active = 1;
END //

CREATE PROCEDURE sp_restaurant_search(IN p_search_term VARCHAR(255))
BEGIN
    SELECT id, name, combo_name, combo_description, combo_price, food_type, is_active
    FROM restaurants
    WHERE is_active = 1 
    AND (name LIKE p_search_term OR combo_name LIKE p_search_term OR food_type LIKE p_search_term OR combo_description LIKE p_search_term);
END //

-- ============================================
-- Order/Cart Procedures
-- ============================================

CREATE PROCEDURE sp_order_create(
    IN p_customer_id INT,
    IN p_restaurant_id INT,
    IN p_combo_name VARCHAR(100),
    IN p_combo_price DECIMAL(10,2),
    IN p_quantity INT,
    IN p_total DECIMAL(10,2),
    IN p_status VARCHAR(20)
)
BEGIN
    INSERT INTO orders (customer_id, restaurant_id, combo_name, combo_price, quantity, total, status)
    VALUES (p_customer_id, p_restaurant_id, p_combo_name, p_combo_price, p_quantity, p_total, p_status);
    SELECT LAST_INSERT_ID() AS order_id;
END //

CREATE PROCEDURE sp_order_get_all(IN p_limit INT, IN p_offset INT)
BEGIN
    SELECT 
        o.id, o.customer_id as userId, o.combo_name as title, o.combo_price as price, 
        o.quantity, o.total, o.status, o.notes, o.created_at,
        r.name as restaurant_name, r.combo_description
    FROM orders o
    JOIN restaurants r ON o.restaurant_id = r.id
    LIMIT p_limit OFFSET p_offset;
END //

CREATE PROCEDURE sp_order_count()
BEGIN
    SELECT COUNT(*) AS total FROM orders;
END //

CREATE PROCEDURE sp_order_get_by_id(IN p_id INT)
BEGIN
    SELECT 
        o.id, o.customer_id as userId, o.combo_name as title, o.combo_price as price, 
        o.quantity, o.total, o.status, o.notes, o.created_at,
        r.name as restaurant_name, r.combo_description
    FROM orders o
    JOIN restaurants r ON o.restaurant_id = r.id
    WHERE o.id = p_id
    LIMIT 1;
END //

CREATE PROCEDURE sp_order_get_by_user(IN p_customer_id INT, IN p_limit INT)
BEGIN
    SELECT 
        o.id, o.customer_id as userId, o.combo_name as title, o.combo_price as price, 
        o.quantity, o.total, o.status, o.notes, o.created_at,
        r.name as restaurant_name, r.combo_description
    FROM orders o
    JOIN restaurants r ON o.restaurant_id = r.id
    WHERE o.customer_id = p_customer_id
    LIMIT p_limit;
END //

CREATE PROCEDURE sp_order_update(IN p_id INT, IN p_quantity INT, IN p_total DECIMAL(10,2))
BEGIN
    UPDATE orders 
    SET quantity = p_quantity, total = p_total, updated_at = NOW()
    WHERE id = p_id;
END //

CREATE PROCEDURE sp_order_delete(IN p_id INT)
BEGIN
    DELETE FROM orders WHERE id = p_id;
END //

-- ============================================
-- Customer/Address/Post Procedures
-- ============================================

CREATE PROCEDURE sp_customer_get_by_id(IN p_id INT)
BEGIN
    SELECT id, first_name, last_name, email, address, city, postal_code, phone_number
    FROM customers
    WHERE id = p_id
    LIMIT 1;
END //

CREATE PROCEDURE sp_customer_create(
    IN p_first_name VARCHAR(50),
    IN p_last_name VARCHAR(50),
    IN p_email VARCHAR(100),
    IN p_address VARCHAR(255),
    IN p_city VARCHAR(100),
    IN p_postal_code VARCHAR(10),
    IN p_phone_number VARCHAR(20)
)
BEGIN
    INSERT INTO customers (first_name, last_name, email, address, city, postal_code, phone_number)
    VALUES (p_first_name, p_last_name, p_email, p_address, p_city, p_postal_code, p_phone_number);
    SELECT LAST_INSERT_ID() AS customer_id;
END //

CREATE PROCEDURE sp_customer_update(
    IN p_id INT,
    IN p_first_name VARCHAR(50),
    IN p_last_name VARCHAR(50),
    IN p_address VARCHAR(255),
    IN p_city VARCHAR(100),
    IN p_postal_code VARCHAR(10)
)
BEGIN
    UPDATE customers 
    SET first_name = p_first_name, last_name = p_last_name, address = p_address, 
        city = p_city, postal_code = p_postal_code, updated_at = NOW()
    WHERE id = p_id;
END //

CREATE PROCEDURE sp_customer_delete(IN p_id INT)
BEGIN
    DELETE FROM customers WHERE id = p_id;
END //

DELIMITER ;