-- ============================================================
-- DROPS
-- ============================================================

DROP TRIGGER IF EXISTS trg_bef_ins_order;
DROP TRIGGER IF EXISTS trg_bef_ins_invoice_line;

DROP PROCEDURE IF EXISTS sp_create_location ;
DROP PROCEDURE IF EXISTS sp_read_locations ;
DROP PROCEDURE IF EXISTS sp_read_location ;
DROP PROCEDURE IF EXISTS sp_update_location ;
DROP PROCEDURE IF EXISTS sp_delete_location ;

DROP PROCEDURE IF EXISTS sp_create_user ;
DROP PROCEDURE IF EXISTS sp_read_users ;
DROP PROCEDURE IF EXISTS sp_read_user ;
DROP PROCEDURE IF EXISTS sp_update_user ;
DROP PROCEDURE IF EXISTS sp_delete_user ;

DROP PROCEDURE IF EXISTS sp_create_customer ;
DROP PROCEDURE IF EXISTS sp_read_customers ;
DROP PROCEDURE IF EXISTS sp_read_customer ;
DROP PROCEDURE IF EXISTS sp_update_customer ;
DROP PROCEDURE IF EXISTS sp_delete_customer ;

DROP PROCEDURE IF EXISTS sp_create_restaurant ;
DROP PROCEDURE IF EXISTS sp_read_restaurants ;
DROP PROCEDURE IF EXISTS sp_read_restaurant ;
DROP PROCEDURE IF EXISTS sp_update_restaurant ;
DROP PROCEDURE IF EXISTS sp_delete_restaurant ;

DROP PROCEDURE IF EXISTS sp_create_driver ;
DROP PROCEDURE IF EXISTS sp_read_drivers ;
DROP PROCEDURE IF EXISTS sp_read_driver ;
DROP PROCEDURE IF EXISTS sp_update_driver ;
DROP PROCEDURE IF EXISTS sp_delete_driver ;

DROP PROCEDURE IF EXISTS sp_create_combo ;
DROP PROCEDURE IF EXISTS sp_read_combos ;
DROP PROCEDURE IF EXISTS sp_read_combo ;
DROP PROCEDURE IF EXISTS sp_update_combo ;
DROP PROCEDURE IF EXISTS sp_delete_combo ;

DROP PROCEDURE IF EXISTS sp_create_order ;
DROP PROCEDURE IF EXISTS sp_read_orders ;
DROP PROCEDURE IF EXISTS sp_read_order ;
DROP PROCEDURE IF EXISTS sp_update_order ;
DROP PROCEDURE IF EXISTS sp_delete_order ;

DROP PROCEDURE IF EXISTS sp_create_invoice_line ;
DROP PROCEDURE IF EXISTS sp_read_invoice_lines ;
DROP PROCEDURE IF EXISTS sp_read_invoice_line ;
DROP PROCEDURE IF EXISTS sp_update_invoice_line ;
DROP PROCEDURE IF EXISTS sp_delete_invoice_line ;

DROP PROCEDURE IF EXISTS sp_create_complaint ;
DROP PROCEDURE IF EXISTS sp_read_complaints ;
DROP PROCEDURE IF EXISTS sp_read_complaint ;
DROP PROCEDURE IF EXISTS sp_update_complaint ;
DROP PROCEDURE IF EXISTS sp_delete_complaint ;

DELIMITER //

-- ============================================================
-- TRIGGERS
-- ============================================================

CREATE TRIGGER trg_bef_ins_order
BEFORE INSERT ON orders
FOR EACH ROW
BEGIN
    SET NEW.costumer_card_number = (
        SELECT card_number
        FROM customers
        WHERE user_id = NEW.customer_id
    );
    SET NEW.driver_card_number = (
        SELECT card_number
        FROM drivers
        WHERE user_id = NEW.driver_id
    );
END //

CREATE TRIGGER trg_bef_ins_invoice_line
BEFORE INSERT ON invoice_lines
FOR EACH ROW
BEGIN
    SET NEW.combo_price = (
        SELECT price
        FROM combos
        WHERE id = NEW.combo_id
    );
END //


-- ============================================================
-- LOCATIONS
-- ============================================================

CREATE PROCEDURE sp_create_location(
    IN p_address     VARCHAR(255),
    IN p_city        VARCHAR(255),
    IN p_postal_code VARCHAR(255)
)
BEGIN
    INSERT INTO locations (address, city, postal_code)
    VALUES (p_address, p_city, p_postal_code);
    SELECT LAST_INSERT_ID() AS id;
END //

CREATE PROCEDURE sp_read_locations()
BEGIN
    SELECT * FROM locations;
END //

CREATE PROCEDURE sp_read_location(IN p_id INT UNSIGNED)
BEGIN
    SELECT * FROM locations WHERE id = p_id;
END //

CREATE PROCEDURE sp_update_location(
    IN p_id          INT UNSIGNED,
    IN p_address     VARCHAR(255),
    IN p_city        VARCHAR(255),
    IN p_postal_code VARCHAR(255)
)
BEGIN
    UPDATE locations
    SET address     = p_address,
        city        = p_city,
        postal_code = p_postal_code
    WHERE id = p_id;
    SELECT ROW_COUNT() AS rows_affected;
END //

CREATE PROCEDURE sp_delete_location(IN p_id INT UNSIGNED)
BEGIN
    DELETE FROM locations WHERE id = p_id;
    SELECT ROW_COUNT() AS rows_affected;
END //


-- ============================================================
-- USERS
-- ============================================================

CREATE PROCEDURE sp_create_user(
    IN p_username      VARCHAR(255),
    IN p_email         VARCHAR(255),
    IN p_password_hash VARCHAR(255),
    IN p_role          ENUM('customer','driver','restaurant','admin'),
    IN p_document      VARCHAR(255),
    IN p_location_id   INT UNSIGNED
)
BEGIN
    INSERT INTO users (username, email, password_hash, role, document, location_id)
    VALUES (p_username, p_email, p_password_hash, p_role, p_document, p_location_id);
    SELECT LAST_INSERT_ID() AS id;
END //

CREATE PROCEDURE sp_read_users()
BEGIN
    SELECT * FROM users;
END //

CREATE PROCEDURE sp_read_user(IN p_id INT UNSIGNED)
BEGIN
    SELECT * FROM users WHERE id = p_id;
END //

CREATE PROCEDURE sp_update_user(
    IN p_id            INT UNSIGNED,
    IN p_username      VARCHAR(255),
    IN p_email         VARCHAR(255),
    IN p_password_hash VARCHAR(255),
    IN p_role          ENUM('customer','driver','restaurant','admin'),
    IN p_status        ENUM('inactive','active'),
    IN p_document      VARCHAR(255),
    IN p_location_id   INT UNSIGNED
)
BEGIN
    UPDATE users
    SET username      = p_username,
        email         = p_email,
        password_hash = p_password_hash,
        role          = p_role,
        status        = p_status,
        document      = p_document,
        location_id   = p_location_id
    WHERE id = p_id;
    SELECT ROW_COUNT() AS rows_affected;
END //

CREATE PROCEDURE sp_delete_user(IN p_id INT UNSIGNED)
BEGIN
    DELETE FROM users WHERE id = p_id;
    SELECT ROW_COUNT() AS rows_affected;
END //


-- ============================================================
-- CUSTOMERS
-- ============================================================

CREATE PROCEDURE sp_create_customer(
    IN p_user_id     INT UNSIGNED,
    IN p_card_number VARCHAR(255)
)
BEGIN
    INSERT INTO customers (user_id, card_number)
    VALUES (p_user_id, p_card_number);
    SELECT p_user_id AS user_id;
END //

CREATE PROCEDURE sp_read_customers()
BEGIN
    SELECT * FROM customers;
END //

CREATE PROCEDURE sp_read_customer(IN p_user_id INT UNSIGNED)
BEGIN
    SELECT * FROM customers WHERE user_id = p_user_id;
END //

CREATE PROCEDURE sp_update_customer(
    IN p_user_id     INT UNSIGNED,
    IN p_card_number VARCHAR(255)
)
BEGIN
    UPDATE customers
    SET card_number = p_card_number
    WHERE user_id = p_user_id;
    SELECT ROW_COUNT() AS rows_affected;
END //

CREATE PROCEDURE sp_delete_customer(IN p_user_id INT UNSIGNED)
BEGIN
    DELETE FROM customers WHERE user_id = p_user_id;
    SELECT ROW_COUNT() AS rows_affected;
END //


-- ============================================================
-- RESTAURANTS
-- ============================================================

CREATE PROCEDURE sp_create_restaurant(
    IN p_user_id  INT UNSIGNED,
    IN p_category ENUM('typical','chinese','italian','healthy')
)
BEGIN
    INSERT INTO restaurants (user_id, category)
    VALUES (p_user_id, p_category);
    SELECT p_user_id AS user_id;
END //

CREATE PROCEDURE sp_read_restaurants()
BEGIN
    SELECT * FROM restaurants;
END //

CREATE PROCEDURE sp_read_restaurant(IN p_user_id INT UNSIGNED)
BEGIN
    SELECT * FROM restaurants WHERE user_id = p_user_id;
END //

CREATE PROCEDURE sp_update_restaurant(
    IN p_user_id  INT UNSIGNED,
    IN p_category ENUM('typical','chinese','italian','healthy')
)
BEGIN
    UPDATE restaurants
    SET category = p_category
    WHERE user_id = p_user_id;
    SELECT ROW_COUNT() AS rows_affected;
END //

CREATE PROCEDURE sp_delete_restaurant(IN p_user_id INT UNSIGNED)
BEGIN
    DELETE FROM restaurants WHERE user_id = p_user_id;
    SELECT ROW_COUNT() AS rows_affected;
END //


-- ============================================================
-- DRIVERS
-- ============================================================

CREATE PROCEDURE sp_create_driver(
    IN p_user_id           INT UNSIGNED,
    IN p_card_number       VARCHAR(255),
    IN p_km_cost_regular   DECIMAL(8,2),
    IN p_km_cost_holidays  DECIMAL(8,2)
)
BEGIN
    INSERT INTO drivers (user_id, card_number, km_cost_regular, km_cost_holidays)
    VALUES (p_user_id, p_card_number, p_km_cost_regular, p_km_cost_holidays);
    SELECT p_user_id AS user_id;
END //

CREATE PROCEDURE sp_read_drivers()
BEGIN
    SELECT * FROM drivers;
END //

CREATE PROCEDURE sp_read_driver(IN p_user_id INT UNSIGNED)
BEGIN
    SELECT * FROM drivers WHERE user_id = p_user_id;
END //

CREATE PROCEDURE sp_update_driver(
    IN p_user_id           INT UNSIGNED,
    IN p_status            ENUM('available','occupied'),
    IN p_penalties         TINYINT,
    IN p_card_number       VARCHAR(255),
    IN p_km_cost_regular   DECIMAL(8,2),
    IN p_km_cost_holidays  DECIMAL(8,2)
)
BEGIN
    UPDATE drivers
    SET status            = p_status,
        penalties         = p_penalties,
        card_number       = p_card_number,
        km_cost_regular   = p_km_cost_regular,
        km_cost_holidays  = p_km_cost_holidays
    WHERE user_id = p_user_id;
    SELECT ROW_COUNT() AS rows_affected;
END //

CREATE PROCEDURE sp_delete_driver(IN p_user_id INT UNSIGNED)
BEGIN
    DELETE FROM drivers WHERE user_id = p_user_id;
    SELECT ROW_COUNT() AS rows_affected;
END //


-- ============================================================
-- COMBOS
-- ============================================================

CREATE PROCEDURE sp_create_combo(
    IN p_restaurant_id INT UNSIGNED,
    IN p_name          VARCHAR(255),
    IN p_description   VARCHAR(255),
    IN p_price         DECIMAL(8,2)
)
BEGIN
    INSERT INTO combos (restaurant_id, name, description, price)
    VALUES (p_restaurant_id, p_name, p_description, p_price);
    SELECT LAST_INSERT_ID() AS id;
END //

CREATE PROCEDURE sp_read_combos()
BEGIN
    SELECT * FROM combos;
END //

CREATE PROCEDURE sp_read_combo(IN p_id INT UNSIGNED)
BEGIN
    SELECT * FROM combos WHERE id = p_id;
END //

CREATE PROCEDURE sp_update_combo(
    IN p_id          INT UNSIGNED,
    IN p_name        VARCHAR(255),
    IN p_description VARCHAR(255),
    IN p_price       DECIMAL(8,2)
)
BEGIN
    UPDATE combos
    SET name        = p_name,
        description = p_description,
        price       = p_price
    WHERE id = p_id;
    SELECT ROW_COUNT() AS rows_affected;
END //

CREATE PROCEDURE sp_delete_combo(IN p_id INT UNSIGNED)
BEGIN
    DELETE FROM combos WHERE id = p_id;
    SELECT ROW_COUNT() AS rows_affected;
END //


-- ============================================================
-- ORDERS
-- ============================================================

CREATE PROCEDURE sp_create_order(
    IN p_customer_id INT UNSIGNED,
    IN p_driver_id   INT UNSIGNED
)
BEGIN
    INSERT INTO orders (customer_id, driver_id, costumer_card_number, driver_card_number)
    VALUES (p_customer_id, p_driver_id, '', '');
    SELECT LAST_INSERT_ID() AS id;
END //

CREATE PROCEDURE sp_read_orders()
BEGIN
    SELECT * FROM orders;
END //

CREATE PROCEDURE sp_read_order(IN p_id INT UNSIGNED)
BEGIN
    SELECT * FROM orders WHERE id = p_id;
END //

CREATE PROCEDURE sp_update_order(
    IN p_id             INT UNSIGNED,
    IN p_status         ENUM('pending','ongoing','delivered','cancelled'),
    IN p_delivered_date DATE
)
BEGIN
    UPDATE orders
    SET status         = p_status,
        delivered_date = p_delivered_date
    WHERE id = p_id;
    SELECT ROW_COUNT() AS rows_affected;
END //

CREATE PROCEDURE sp_delete_order(IN p_id INT UNSIGNED)
BEGIN
    DELETE FROM orders WHERE id = p_id;
    SELECT ROW_COUNT() AS rows_affected;
END //


-- ============================================================
-- INVOICE LINES
-- ============================================================

CREATE PROCEDURE sp_create_invoice_line(
    IN p_combo_id INT UNSIGNED,
    IN p_order_id INT UNSIGNED,
    IN p_quantity INT
)
BEGIN
    INSERT INTO invoice_lines (combo_id, order_id, quantity, combo_price)
    VALUES (p_combo_id, p_order_id, p_quantity, 0.00);
    SELECT p_combo_id AS combo_id, p_order_id AS order_id;
END //

CREATE PROCEDURE sp_read_invoice_lines()
BEGIN
    SELECT * FROM invoice_lines;
END //

CREATE PROCEDURE sp_read_invoice_line(
    IN p_combo_id INT UNSIGNED,
    IN p_order_id INT UNSIGNED
)
BEGIN
    SELECT * FROM invoice_lines
    WHERE combo_id = p_combo_id AND order_id = p_order_id;
END //

CREATE PROCEDURE sp_update_invoice_line(
    IN p_combo_id INT UNSIGNED,
    IN p_order_id INT UNSIGNED,
    IN p_quantity INT
)
BEGIN
    UPDATE invoice_lines
    SET quantity = p_quantity
    WHERE combo_id = p_combo_id AND order_id = p_order_id;
    SELECT ROW_COUNT() AS rows_affected;
END //

CREATE PROCEDURE sp_delete_invoice_line(
    IN p_combo_id INT UNSIGNED,
    IN p_order_id INT UNSIGNED
)
BEGIN
    DELETE FROM invoice_lines
    WHERE combo_id = p_combo_id AND order_id = p_order_id;
    SELECT ROW_COUNT() AS rows_affected;
END //


-- ============================================================
-- COMPLAINTS
-- ============================================================

CREATE PROCEDURE sp_create_complaint(
    IN p_order_id INT UNSIGNED,
    IN p_content  VARCHAR(255),
    IN p_rating   TINYINT
)
BEGIN
    INSERT INTO complaints (order_id, content, rating)
    VALUES (p_order_id, p_content, p_rating);
    SELECT p_order_id AS order_id;
END //

CREATE PROCEDURE sp_read_complaints()
BEGIN
    SELECT * FROM complaints;
END //

CREATE PROCEDURE sp_read_complaint(IN p_order_id INT UNSIGNED)
BEGIN
    SELECT * FROM complaints WHERE order_id = p_order_id;
END //

CREATE PROCEDURE sp_update_complaint(
    IN p_order_id INT UNSIGNED,
    IN p_content  VARCHAR(255),
    IN p_rating   TINYINT
)
BEGIN
    UPDATE complaints
    SET content = p_content,
        rating  = p_rating
    WHERE order_id = p_order_id;
    SELECT ROW_COUNT() AS rows_affected;
END //

CREATE PROCEDURE sp_delete_complaint(IN p_order_id INT UNSIGNED)
BEGIN
    DELETE FROM complaints WHERE order_id = p_order_id;
    SELECT ROW_COUNT() AS rows_affected;
END //

-- Special

CREATE FUNCTION fn_count_restaurant_orders(p_restaurant_id INT UNSIGNED)
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE v_total INT;

    SELECT COUNT(DISTINCT o.id) INTO v_total
    FROM restaurants AS r
    INNER JOIN combos AS c ON r.user_id = c.restaurant_id
    INNER JOIN invoice_lines AS il ON c.id = il.combo_id
    INNER JOIN orders AS o ON il.order_id = o.id
    WHERE r.user_id = p_restaurant_id;

    RETURN v_total;
END //

DELIMITER ;