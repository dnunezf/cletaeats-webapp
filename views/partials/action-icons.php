<?php

/**
 * Reusable SVG markup for CRUD action icons.
 *
 * Renders 16x16 SVG paths using currentColor so they inherit from the parent
 * .btn-icon button (outline/ghost/danger variants pick up the right hue).
 *
 * Usage in a view:
 *     <?php $action = 'edit'; require ...; ?>
 * Or via the helper:
 *     <?= actionIcon('edit') ?>
 */
if (!function_exists('actionIcon')) {
    function actionIcon(string $kind): string
    {
        $paths = [
            'view'   => 'M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z',
            'edit'   => 'M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34a.9959.9959 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z',
            'delete' => 'M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z',
            'combos' => 'M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z',
            'add'    => 'M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z',
            'approve'=> 'M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z',
        ];
        $d = $paths[$kind] ?? '';
        return '<svg class="action-icon" viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true"><path d="' . $d . '"/></svg>';
    }
}
