<?php

/**
 * Ensures the user is authenticated. Redirects to login if not.
 */
class AuthMiddleware
{
    public function handle(): void
    {
        if (empty($_SESSION['user_id'])) {
            setFlash('error', 'Please log in to continue.');
            redirect('login');
        }
    }
}
