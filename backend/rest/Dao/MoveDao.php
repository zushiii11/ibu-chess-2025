<?php

declare(strict_types=1);

namespace Rest\Dao;

use PDO;

final class MoveDao extends BaseDao
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'moves', [
            'game_id',
            'move_number',
            'notation',
            'comment',
            'time_spent_seconds',
        ]);
    }
}

