<?php

declare(strict_types=1);

namespace Rest\Services;

use App\Config\Jwt;
use Firebase\JWT\JWT as FirebaseJWT;
use InvalidArgumentException;

final class AuthService
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function register(array $data): array
    {
        // Create user
        $user = $this->userService->create($data);
        
        // Remove password hash from response
        unset($user['password_hash']);
        
        // Generate JWT token
        $token = $this->generateToken($user);
        
        return [
            'user' => $user,
            'token' => $token
        ];
    }

    public function login(string $username, string $password): array
    {
        if (empty($username) || empty($password)) {
            throw new InvalidArgumentException('Username and password are required');
        }

        $user = $this->userService->authenticateUser($username, $password);
        
        if ($user === null) {
            throw new InvalidArgumentException('Invalid username or password');
        }
        
        // Generate JWT token
        $token = $this->generateToken($user);
        
        return [
            'user' => $user,
            'token' => $token
        ];
    }

    public function generateToken(array $user): string
    {
        $payload = [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role'],
            'iat' => time(),
            'exp' => time() + Jwt::getExpiration()
        ];
        
        return FirebaseJWT::encode($payload, Jwt::getSecret(), 'HS256');
    }
}

