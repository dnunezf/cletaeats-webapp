<?php

/**
 * Ensures the authenticated user has admin role. Shows 403 if not.
 */
class AdminMiddleware
{
    public function handle(): void
    {
        if (($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(403);
            require BASE_PATH . '/views/errors/403.php';
            exit;
        }
    }
}
