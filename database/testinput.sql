-- ============================================================
-- LOCATIONS
-- ============================================================
CALL sp_create_location('123 Main St', 'San Jose', '10101');
CALL sp_read_locations();
CALL sp_read_location(1);
CALL sp_update_location(1, '456 Elm St', 'Cartago', '30201');
CALL sp_delete_location(1);


-- ============================================================
-- USERS
-- ============================================================
CALL sp_create_user('john_doe', 'john@email.com', 'hashed_pw_123', 'customer', 'DOC001', 1);
CALL sp_create_user('rest_owner', 'rest@email.com', 'hashed_pw_456', 'restaurant', 'DOC002', 1);
CALL sp_create_user('driver_mike', 'mike@email.com', 'hashed_pw_789', 'driver', 'DOC003', 1);
CALL sp_read_users();
CALL sp_read_user(1);
CALL sp_update_user(1, 'john_updated', 'john_new@email.com', 'hashed_pw_new', 'customer', 'active', 'DOC001', 1);
CALL sp_delete_user(1);


-- ============================================================
-- CUSTOMERS
-- ============================================================
CALL sp_create_customer(1, '4111111111111111');
CALL sp_read_customers();
CALL sp_read_customer(1);
CALL sp_update_customer(1, '4222222222222222');
CALL sp_delete_customer(1);


-- ============================================================
-- RESTAURANTS
-- ============================================================
CALL sp_create_restaurant(2, 'italian');
CALL sp_read_restaurants();
CALL sp_read_restaurant(2);
CALL sp_update_restaurant(2, 'healthy');
CALL sp_delete_restaurant(2);


-- ============================================================
-- DRIVERS
-- ============================================================
CALL sp_create_driver(3, '5333333333333333', 1.50, 2.00);
CALL sp_read_drivers();
CALL sp_read_driver(3);
CALL sp_update_driver(3, 'available', 0, '5444444444444444', 1.75, 2.25);
CALL sp_delete_driver(3);


-- ============================================================
-- COMBOS
-- ============================================================
CALL sp_create_combo(2, 'Pasta Combo', 'Pasta with salad and drink', 8.99);
CALL sp_read_combos();
CALL sp_read_combo(1);
CALL sp_update_combo(1, 'Pasta Deluxe', 'Pasta with salad, bread and drink', 10.99);
CALL sp_delete_combo(1);


-- ============================================================
-- ORDERS
-- ============================================================
CALL sp_create_order(1, 3);
CALL sp_read_orders();
CALL sp_read_order(1);
CALL sp_update_order(1, 'ongoing', NULL);
CALL sp_update_order(1, 'delivered', '2026-05-14');
CALL sp_delete_order(1);


-- ============================================================
-- INVOICE LINES
-- ============================================================
CALL sp_create_invoice_line(1, 1, 2);
CALL sp_read_invoice_lines();
CALL sp_read_invoice_line(1, 1);
CALL sp_update_invoice_line(1, 1, 3);
CALL sp_delete_invoice_line(1, 1);


-- ============================================================
-- COMPLAINTS
-- ============================================================
CALL sp_create_complaint(1, 'Order arrived late and cold.', 2);
CALL sp_read_complaints();
CALL sp_read_complaint(1);
CALL sp_update_complaint(1, 'Order arrived late but food was fine.', 3);
CALL sp_delete_complaint(1);