<?php

declare(strict_types=1);

namespace App\Config;

final class Jwt
{
    private static ?string $secret = null;

    public static function getSecret(): string
    {
        if (self::$secret === null) {
            self::$secret = getenv('JWT_SECRET') ?: 'ibu-chess-secret-key-change-in-production-2025';
        }

        return self::$secret;
    }

    public static function getExpiration(): int
    {
        return (int) (getenv('JWT_EXPIRATION') ?: 86400); // 24 hours default
    }
}
