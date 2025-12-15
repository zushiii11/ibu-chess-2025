<?php

declare(strict_types=1);

namespace Rest\Dao;

use PDO;

final class AiGameDao extends BaseDao
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'ai_games', [
            'game_id',
            'difficulty',
            'ai_engine'
        ]);
    }
}
