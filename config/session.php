<?php

/**
 * Configures and starts the PHP session with secure settings.
 */
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.use_only_cookies', 1);

if (($_ENV['APP_ENV'] ?? 'development') === 'production') {
    ini_set('session.cookie_secure', 1);
}

session_start();
