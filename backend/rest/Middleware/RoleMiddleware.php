<?php

declare(strict_types=1);

namespace Rest\Middleware;

final class RoleMiddleware
{
    /**
     * @param string[] $allowedRoles
     */
    public static function handle(array $allowedRoles): void
    {
        $user = \Flight::get('user');

        if ($user === null) {
            \Flight::json(['error' => 'Unauthorized - User not authenticated'], 401);
            \Flight::stop();
            return;
        }

        $userRole = $user['role'] ?? null;

        if ($userRole === null || !in_array($userRole, $allowedRoles, true)) {
            \Flight::json([
                'error' => 'Forbidden - Insufficient permissions',
                'required_roles' => $allowedRoles,
                'user_role' => $userRole
            ], 403);
            \Flight::stop();
        }
    }
}

