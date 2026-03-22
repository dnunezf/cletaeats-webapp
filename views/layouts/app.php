<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="<?= e(APP_URL) ?>">
    <title><?= e($pageTitle ?? 'Dashboard') ?> - <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= baseUrl('css/variables.css') ?>">
    <link rel="stylesheet" href="<?= baseUrl('css/reset.css') ?>">
    <link rel="stylesheet" href="<?= baseUrl('css/layout.css') ?>">
    <link rel="stylesheet" href="<?= baseUrl('css/components.css') ?>">
    <link rel="stylesheet" href="<?= baseUrl('css/auth.css') ?>">
    <link rel="stylesheet" href="<?= baseUrl('css/customers.css') ?>">
    <link rel="stylesheet" href="<?= baseUrl('css/responsive.css') ?>">
</head>
<body>
    <?php require BASE_PATH . '/views/partials/nav.php'; ?>
    <?php require BASE_PATH . '/views/partials/header.php'; ?>

    <main class="main-content">
        <?php require BASE_PATH . '/views/partials/flash.php'; ?>
        <?= $content ?>
    </main>

    <script src="<?= baseUrl('js/validation.js') ?>"></script>
    <script src="<?= baseUrl('js/app.js') ?>"></script>
    <script src="<?= baseUrl('js/auth.js') ?>"></script>
    <script src="<?= baseUrl('js/customers.js') ?>"></script>
</body>
</html>
