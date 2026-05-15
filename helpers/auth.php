<?php

/**
 * Role / session helper functions.
 *
 * Lightweight wrappers around $_SESSION used by views, controllers, and
 * middleware. Centralizing the role check here keeps callers consistent
 * (no more inline `($_SESSION['role'] ?? '') === 'admin'` scattered around).
 */

function currentUserId(): ?int
{
    return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
}

function currentRole(): ?string
{
    return $_SESSION['role'] ?? null;
}

function userIs(string $role): bool
{
    return currentRole() === $role;
}

function userIsAnyOf(array $roles): bool
{
    return in_array(currentRole(), $roles, true);
}

function userIsAdmin(): bool      { return userIs('admin'); }
function userIsCustomer(): bool   { return userIs('customer'); }
function userIsDriver(): bool     { return userIs('driver'); }
function userIsRestaurant(): bool { return userIs('restaurant'); }
