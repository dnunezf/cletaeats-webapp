<?php

/**
 * Ensures the authenticated user's role is in the allowlist passed to the
 * constructor. Used via the `role:<csv>` route middleware token (parsed in
 * public/index.php). Shows 403 if the role doesn't match.
 *
 * AdminMiddleware is preserved as a back-compat alias for role:admin.
 */
class RoleMiddleware
{
    /** @var string[] */
    private array $allowedRoles;

    public function __construct(array $allowedRoles)
    {
        $this->allowedRoles = array_values(array_filter(array_map('trim', $allowedRoles)));
    }

    public function handle(): void
    {
        $role = $_SESSION['role'] ?? '';
        if (!in_array($role, $this->allowedRoles, true)) {
            http_response_code(403);
            require BASE_PATH . '/views/errors/403.php';
            exit;
        }
    }
}
