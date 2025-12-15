<?php

declare(strict_types=1);

namespace Rest\Middleware;

use App\Config\Jwt;
use Firebase\JWT\JWT as FirebaseJWT;
use Firebase\JWT\Key;
use Exception;

final class AuthMiddleware
{
    public static function handle(): void
    {
        // Get Authorization header from various sources
        $authHeader = '';
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        }
        
        if (empty($authHeader)) {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
        }

        if (empty($authHeader) || !str_starts_with($authHeader, 'Bearer ')) {
            \Flight::json(['error' => 'Unauthorized - Missing or invalid token'], 401);
            \Flight::stop();
            return;
        }

        $token = substr($authHeader, 7); // Remove "Bearer " prefix

        try {
            $decoded = FirebaseJWT::decode($token, new Key(Jwt::getSecret(), 'HS256'));
            
            // Store user info in Flight for use in routes
            \Flight::set('user', (array) $decoded);
        } catch (Exception $e) {
            \Flight::json(['error' => 'Unauthorized - Invalid token: ' . $e->getMessage()], 401);
            \Flight::stop();
        }
    }
}

