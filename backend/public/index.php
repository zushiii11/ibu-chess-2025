<?php

declare(strict_types=1);

use App\Config\Database;
use App\Dao\GameDao;
use App\Dao\MoveDao;
use App\Dao\ReviewDao;
use App\Dao\TournamentDao;
use App\Dao\TournamentParticipantDao;
use App\Dao\UserDao;
use App\Routes\ApiRouter;
use App\Services\GameService;
use App\Services\MoveService;
use App\Services\ReviewService;
use App\Services\TournamentParticipantService;
use App\Services\TournamentService;
use App\Services\UserService;

require_once __DIR__ . '/../bootstrap.php';

$pdo = Database::connection();

$router = new ApiRouter();
$router->register('users', new UserService(new UserDao($pdo)));
$router->register('tournaments', new TournamentService(new TournamentDao($pdo)));
$router->register('games', new GameService(new GameDao($pdo)));
$router->register('moves', new MoveService(new MoveDao($pdo)));
$router->register('tournament-participants', new TournamentParticipantService(new TournamentParticipantDao($pdo)));
$router->register('reviews', new ReviewService(new ReviewDao($pdo)));

$router->handle(
    $_SERVER['REQUEST_METHOD'] ?? 'GET',
    $_SERVER['REQUEST_URI'] ?? '/',
    $_GET
);
