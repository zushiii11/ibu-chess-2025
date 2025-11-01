<?php

declare(strict_types=1);

namespace App\Dao;

use PDO;

final class TournamentDao extends BaseDao
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'tournaments', [
            'name',
            'location',
            'start_date',
            'end_date',
            'description',
            'prize_pool',
        ]);
    }
}
