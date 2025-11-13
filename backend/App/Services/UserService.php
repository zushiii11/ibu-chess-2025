<?php

declare(strict_types=1);

namespace App\Services;

use InvalidArgumentException;

final class UserService extends BaseService
{
    public function create(array $data): array
    {
        $this->validateUserData($data, true);
        
        // Hash password if provided
        if (isset($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT);
            unset($data['password']);
        }
        
        return $this->dao->create($data);
    }

    public function update(int $id, array $data): ?array
    {
        $this->validateUserData($data, false);
        
        // Hash password if provided
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT);
            unset($data['password']);
        }
        
        return $this->dao->update($id, $data);
    }

    private function validateUserData(array $data, bool $isCreate): void
    {
        // Username validation
        if ($isCreate && (!isset($data['username']) || empty(trim($data['username'])))) {
            throw new InvalidArgumentException('Username is required');
        }
        
        if (isset($data['username'])) {
            if (strlen($data['username']) < 3) {
                throw new InvalidArgumentException('Username must be at least 3 characters long');
            }
            if (strlen($data['username']) > 50) {
                throw new InvalidArgumentException('Username must not exceed 50 characters');
            }
            if (!preg_match('/^[a-zA-Z0-9_-]+$/', $data['username'])) {
                throw new InvalidArgumentException('Username can only contain letters, numbers, underscores, and hyphens');
            }
        }

        // Email validation
        if ($isCreate && (!isset($data['email']) || empty(trim($data['email'])))) {
            throw new InvalidArgumentException('Email is required');
        }
        
        if (isset($data['email']) && !empty($data['email'])) {
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException('Invalid email format');
            }
            if (strlen($data['email']) > 120) {
                throw new InvalidArgumentException('Email must not exceed 120 characters');
            }
        }

        // Password validation (only for create or if password is being updated)
        if ($isCreate && (!isset($data['password']) || empty(trim($data['password'])))) {
            throw new InvalidArgumentException('Password is required');
        }
        
        if (isset($data['password']) && !empty($data['password'])) {
            if (strlen($data['password']) < 6) {
                throw new InvalidArgumentException('Password must be at least 6 characters long');
            }
            if (strlen($data['password']) > 255) {
                throw new InvalidArgumentException('Password must not exceed 255 characters');
            }
        }

        // Role validation
        if (isset($data['role']) && !in_array($data['role'], ['player', 'coach', 'arbiter', 'admin'], true)) {
            throw new InvalidArgumentException('Invalid role. Must be one of: player, coach, arbiter, admin');
        }

        // Rating validation
        if (isset($data['rating'])) {
            $rating = (int) $data['rating'];
            if ($rating < 0 || $rating > 3000) {
                throw new InvalidArgumentException('Rating must be between 0 and 3000');
            }
        }
    }

    public function authenticateUser(string $username, string $password): ?array
    {
        $users = $this->dao->findAll(['username' => $username]);
        
        if (empty($users)) {
            return null;
        }
        
        $user = $users[0];
        
        if (password_verify($password, $user['password_hash'])) {
            // Remove password hash from returned data
            unset($user['password_hash']);
            return $user;
        }
        
        return null;
    }
}
