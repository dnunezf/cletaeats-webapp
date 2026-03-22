<?php

/**
 * Redirects authenticated users away from guest-only pages (login).
 */
class GuestMiddleware
{
    public function handle(): void
    {
        if (!empty($_SESSION['user_id'])) {
            redirect('dashboard');
        }
    }
}
