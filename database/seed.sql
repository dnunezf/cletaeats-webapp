-- ============================================
-- CletaEats Web App - Initial Admin Seed
-- Default credentials: admin / Admin@1234
-- IMPORTANT: Change the password after first login.
-- ============================================

INSERT INTO users (username, email, password_hash, role, is_active)
VALUES (
    'admin',
    'admin@cletaeats.com',
    '$2y$12$TSMWOutJyI4FQaY1qwOMDOMAJClWw0aDjamok5.MfjkEWpi/ulu5S',
    'admin',
    1
)
ON DUPLICATE KEY UPDATE username = username;
