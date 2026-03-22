<?php

/**
 * CSRF token generation and validation.
 */

function generateCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . generateCsrfToken() . '">';
}

function validateCsrfToken(?string $token): bool
{
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function csrfCheck(): void
{
    if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        setFlash('error', 'Invalid security token. Please try again.');
        header('Location: ' . $_SERVER['HTTP_REFERER'] ?? baseUrl('login'));
        exit;
    }
}
