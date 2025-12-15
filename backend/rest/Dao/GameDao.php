<?php

declare(strict_types=1);

namespace Rest\Dao;

use PDO;

final class GameDao extends BaseDao
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'games', [
            'tournament_id',
            'white_player_id',
            'black_player_id',
            'result',
            'pgn',
            'played_at',
        ]);
    }
}

