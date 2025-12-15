<?php

declare(strict_types=1);

namespace Rest\Dao;

use PDO;

final class UserDao extends BaseDao
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'users', [
            'username',
            'email',
            'password_hash',
            'role',
            'rating',
            'bio',
        ]);
    }
}

