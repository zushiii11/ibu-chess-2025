<?php

declare(strict_types=1);

namespace Rest\Dao;

use PDO;

final class MatchmakingDao extends BaseDao
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'matchmaking_queue', [
            'user_id',
            'time_control',
            'rating_min',
            'rating_max',
            'status',
            'game_id'
        ]);
    }
}
