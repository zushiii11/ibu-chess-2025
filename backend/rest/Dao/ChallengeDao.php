<?php

declare(strict_types=1);

namespace Rest\Dao;

use PDO;

final class ChallengeDao extends BaseDao
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'challenges', [
            'challenger_id',
            'challenged_id',
            'time_control',
            'challenger_color',
            'message',
            'status',
            'game_id',
            'expires_at'
        ]);
    }
}
