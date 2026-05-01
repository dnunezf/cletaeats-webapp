<?php
/** @var string $content  Rendered view output, captured via ob_get_clean() in view() helper */
$asset = static function (string $path): string {
    $file = BASE_PATH . '/public/' . ltrim($path, '/');
    $ver  = is_file($file) ? (string) filemtime($file) : '0';
    return baseUrl($path) . '?v=' . $ver;
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="<?= e(APP_URL) ?>">
    <title><?= e($pageTitle ?? 'Dashboard') ?> - <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= $asset('css/variables.css') ?>">
    <link rel="stylesheet" href="<?= $asset('css/reset.css') ?>">
    <link rel="stylesheet" href="<?= $asset('css/layout.css') ?>">
    <link rel="stylesheet" href="<?= $asset('css/components.css') ?>">
    <link rel="stylesheet" href="<?= $asset('css/auth.css') ?>">
    <link rel="stylesheet" href="<?= $asset('css/customers.css') ?>">
    <link rel="stylesheet" href="<?= $asset('css/restaurants.css') ?>">
    <link rel="stylesheet" href="<?= $asset('css/drivers.css') ?>">
    <link rel="stylesheet" href="<?= $asset('css/users.css') ?>">
    <link rel="stylesheet" href="<?= $asset('css/orders.css') ?>">
    <link rel="stylesheet" href="<?= $asset('css/billing.css') ?>">
    <link rel="stylesheet" href="<?= $asset('css/reports.css') ?>">
    <link rel="stylesheet" href="<?= $asset('css/responsive.css') ?>">
</head>
<body>
    <?php require BASE_PATH . '/views/partials/nav.php'; ?>
    <?php require BASE_PATH . '/views/partials/header.php'; ?>

    <main class="main-content">
        <?php require BASE_PATH . '/views/partials/flash.php'; ?>
        <?= $content ?>
    </main>

    <script src="<?= $asset('js/validation.js') ?>"></script>
    <script src="<?= $asset('js/app.js') ?>"></script>
    <script src="<?= $asset('js/auth.js') ?>"></script>
    <script src="<?= $asset('js/customers.js') ?>"></script>
    <script src="<?= $asset('js/restaurants.js') ?>"></script>
    <script src="<?= $asset('js/drivers.js') ?>"></script>
    <script src="<?= $asset('js/users.js') ?>"></script>
    <script src="<?= $asset('js/orders.js') ?>"></script>
</body>
</html>
