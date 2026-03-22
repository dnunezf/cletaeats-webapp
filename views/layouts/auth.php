<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="<?= e(APP_URL) ?>">
    <title><?= e($pageTitle ?? 'Login') ?> - <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= baseUrl('css/variables.css') ?>">
    <link rel="stylesheet" href="<?= baseUrl('css/reset.css') ?>">
    <link rel="stylesheet" href="<?= baseUrl('css/components.css') ?>">
    <link rel="stylesheet" href="<?= baseUrl('css/auth.css') ?>">
    <link rel="stylesheet" href="<?= baseUrl('css/responsive.css') ?>">
</head>
<body>
    <?= $content ?>
    <script src="<?= baseUrl('js/validation.js') ?>"></script>
    <script src="<?= baseUrl('js/auth.js') ?>"></script>
</body>
</html>
