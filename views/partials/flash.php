<?php
$flashSuccess = getFlash('success');
$flashError   = getFlash('error');
$flashErrors  = getFlash('errors');
?>

<?php if ($flashSuccess): ?>
    <div class="alert alert-success" role="alert">
        <span><?= e($flashSuccess) ?></span>
        <button type="button" class="alert-close" aria-label="Close">&times;</button>
    </div>
<?php endif; ?>

<?php if ($flashError): ?>
    <div class="alert alert-error" role="alert">
        <span><?= e($flashError) ?></span>
        <button type="button" class="alert-close" aria-label="Close">&times;</button>
    </div>
<?php endif; ?>

<?php if ($flashErrors && is_array($flashErrors)): ?>
    <div class="alert alert-error" role="alert">
        <div>
            <?php foreach ($flashErrors as $field => $message): ?>
                <div><?= e($message) ?></div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="alert-close" aria-label="Close">&times;</button>
    </div>
<?php endif; ?>
