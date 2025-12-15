<?php

declare(strict_types=1);

namespace Rest\Dao;

use PDO;

final class TournamentParticipantDao extends BaseDao
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'tournament_participants', [
            'tournament_id',
            'user_id',
            'seed',
            'status',
        ]);
    }
}

