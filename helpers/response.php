<?php

/**
 * Response helper functions: redirect, flash messages, output escaping, view rendering.
 */

function redirect(string $path): void
{
    header('Location: ' . baseUrl($path));
    exit;
}

function baseUrl(string $path = ''): string
{
    return rtrim(APP_URL, '/') . '/' . ltrim($path, '/');
}

function setFlash(string $key, mixed $value): void
{
    $_SESSION['flash'][$key] = $value;
}

function getFlash(string $key, mixed $default = null): mixed
{
    $value = $_SESSION['flash'][$key] ?? $default;
    unset($_SESSION['flash'][$key]);
    return $value;
}

function setOldInput(array $data): void
{
    $_SESSION['flash']['old'] = $data;
}

function old(string $key, string $default = ''): string
{
    $value = $_SESSION['flash']['old'][$key] ?? $default;
    return $value;
}

function clearOldInput(): void
{
    unset($_SESSION['flash']['old']);
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function jsonResponse(array $data, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function isAjax(): bool
{
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

function view(string $viewPath, array $data = [], string $layout = 'app'): void
{
    extract($data);

    ob_start();
    require BASE_PATH . '/views/' . $viewPath . '.php';
    $content = ob_get_clean();

    require BASE_PATH . '/views/layouts/' . $layout . '.php';
}
