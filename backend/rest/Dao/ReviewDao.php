<?php

declare(strict_types=1);

namespace Rest\Dao;

use PDO;

final class ReviewDao extends BaseDao
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'reviews', [
            'user_id',
            'game_id',
            'rating',
            'comment',
        ]);
    }
}

