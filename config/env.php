<?php

/**
 * Loads environment variables from a .env file into $_ENV and putenv().
 */
function loadEnv(string $filePath): void
{
    if (!file_exists($filePath)) {
        throw new RuntimeException(".env file not found at: {$filePath}");
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        $separatorPos = strpos($line, '=');
        if ($separatorPos === false) {
            continue;
        }

        $key = trim(substr($line, 0, $separatorPos));
        $value = trim(substr($line, $separatorPos + 1));

        $value = trim($value, '"\'');

        $_ENV[$key] = $value;
        putenv("{$key}={$value}");
    }
}
