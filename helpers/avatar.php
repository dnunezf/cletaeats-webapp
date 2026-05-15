<?php

/**
 * Profile-photo helpers.
 *
 * Avatars are stored on disk at public/uploads/avatars/user_{id}.{ext} with one of
 * the allowed extensions (jpg, jpeg, png, webp, gif). No DB column is needed —
 * presence on disk is the single source of truth, which keeps the schema
 * unchanged. Returns the public URL if a file exists, or null.
 */

const AVATAR_DIR        = '/public/uploads/avatars';
const AVATAR_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

/** Filesystem path to the avatar file for $userId, or null when none exists. */
function avatarPath(int $userId): ?string
{
    if ($userId <= 0) {
        return null;
    }
    foreach (AVATAR_EXTENSIONS as $ext) {
        $candidate = BASE_PATH . AVATAR_DIR . '/user_' . $userId . '.' . $ext;
        if (is_file($candidate)) {
            return $candidate;
        }
    }
    return null;
}

/** Public URL for $userId's avatar, or null when none exists. Cache-busted by mtime. */
function avatarUrl(int $userId): ?string
{
    $path = avatarPath($userId);
    if (!$path) {
        return null;
    }
    $filename = basename($path);
    $bust = @filemtime($path) ?: 0;
    return baseUrl('uploads/avatars/' . $filename . '?v=' . $bust);
}
