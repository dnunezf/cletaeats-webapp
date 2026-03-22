<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <link rel="stylesheet" href="<?= rtrim($_ENV['APP_URL'] ?? '', '/') ?>/css/variables.css">
    <link rel="stylesheet" href="<?= rtrim($_ENV['APP_URL'] ?? '', '/') ?>/css/reset.css">
    <link rel="stylesheet" href="<?= rtrim($_ENV['APP_URL'] ?? '', '/') ?>/css/components.css">
    <link rel="stylesheet" href="<?= rtrim($_ENV['APP_URL'] ?? '', '/') ?>/css/auth.css">
    <style>
        .error-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--color-bg);
            padding: var(--space-lg);
        }
        .error-card {
            text-align: center;
            max-width: 480px;
        }
        .error-code {
            font-size: 6rem;
            font-weight: 800;
            color: var(--color-primary);
            line-height: 1;
            margin-bottom: var(--space-md);
        }
        .error-title {
            font-size: var(--font-size-2xl);
            margin-bottom: var(--space-md);
        }
        .error-text {
            color: var(--color-text-secondary);
            margin-bottom: var(--space-xl);
        }
    </style>
</head>
<body>
    <div class="error-wrapper">
        <div class="error-card">
            <div class="error-code">404</div>
            <h1 class="error-title">Page Not Found</h1>
            <p class="error-text">The page you are looking for does not exist or has been moved.</p>
            <a href="<?= rtrim($_ENV['APP_URL'] ?? '', '/') ?>/dashboard" class="btn btn-primary btn-lg">
                Go to Dashboard
            </a>
        </div>
    </div>
</body>
</html>
