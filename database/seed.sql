-- ============================================================
-- CletaEats Web App - Initial Admin Seed (new schema)
-- Default credentials: admin / Admin@1234
-- Run AFTER schemaNew.sql and proceduresNew.sql.
-- Idempotent: re-running has no effect if the admin already exists.
-- IMPORTANT: Change the password after first login.
-- ============================================================

-- 1) Insert a placeholder location only if the admin does not exist yet.
INSERT INTO locations (address, city, postal_code)
SELECT 'HQ', 'HQ', '00000'
WHERE NOT EXISTS (SELECT 1 FROM users WHERE username = 'admin');

-- 2) Resolve which location_id the admin should use:
--    - If we just inserted one, use LAST_INSERT_ID().
--    - Else, reuse the existing admin's location_id.
SET @loc_id := IF(
    ROW_COUNT() > 0,
    LAST_INSERT_ID(),
    (SELECT location_id FROM users WHERE username = 'admin' LIMIT 1)
);

-- 3) Admin user — bcrypt cost-12 hash of "Admin@1234".
INSERT INTO users (username, email, password_hash, role, status, document, location_id)
SELECT
    'admin',
    'admin@cletaeats.com',
    '$2y$12$4j5AYNGBClLQ05.p4t4n4eAIuNIJjuDZhxmnXqxrkVW6vCO/kfjlm',
    'admin',
    'active',
    'ADMIN-0001',
    @loc_id
WHERE NOT EXISTS (SELECT 1 FROM users WHERE username = 'admin');
