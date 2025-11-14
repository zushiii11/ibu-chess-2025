<?php

declare(strict_types=1);

use App\Config\Database;
use App\Dao\GameDao;
use App\Dao\MoveDao;
use App\Dao\ReviewDao;
use App\Dao\TournamentDao;
use App\Dao\TournamentParticipantDao;
use App\Dao\UserDao;
use App\Services\GameService;
use App\Services\MoveService;
use App\Services\ReviewService;
use App\Services\TournamentParticipantService;
use App\Services\TournamentService;
use App\Services\UserService;

require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../../vendor/autoload.php';

// Initialize database connection
$pdo = Database::connection();

// Initialize DAOs
$userDao = new UserDao($pdo);
$tournamentDao = new TournamentDao($pdo);
$gameDao = new GameDao($pdo);
$moveDao = new MoveDao($pdo);
$participantDao = new TournamentParticipantDao($pdo);
$reviewDao = new ReviewDao($pdo);

// Initialize services
$userService = new UserService($userDao);
$tournamentService = new TournamentService($tournamentDao);
$gameService = new GameService($gameDao);
$moveService = new MoveService($moveDao);
$participantService = new TournamentParticipantService($participantDao);
$reviewService = new ReviewService($reviewDao);

// Register services with Flight
Flight::register('userService', UserService::class, [$userDao]);
Flight::register('tournamentService', TournamentService::class, [$tournamentDao]);
Flight::register('gameService', GameService::class, [$gameDao]);
Flight::register('moveService', MoveService::class, [$moveDao]);
Flight::register('participantService', TournamentParticipantService::class, [$participantDao]);
Flight::register('reviewService', ReviewService::class, [$reviewDao]);

// Configure Flight
Flight::set('flight.log_errors', true);

// CORS middleware
Flight::before('start', function () {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
});

// Error handler
Flight::map('error', function (Throwable $error) {
    $statusCode = 500;
    $message = $error->getMessage();
    
    if ($error instanceof InvalidArgumentException) {
        $statusCode = 400;
    } elseif ($error instanceof RuntimeException) {
        $statusCode = 404;
    }
    
    Flight::json(['error' => $message], $statusCode);
});

// Root endpoint
Flight::route('GET /', function () {
    Flight::json([
        'message' => 'IBU Chess API - Milestone 3',
        'version' => '3.0.0',
        'documentation' => '/v1/docs/',
        'resources' => [
            '/api/users',
            '/api/tournaments',
            '/api/games',
            '/api/moves',
            '/api/tournament-participants',
            '/api/reviews'
        ]
    ]);
});

// Load route files
require __DIR__ . '/../rest/routes/UserRoutes.php';
require __DIR__ . '/../rest/routes/TournamentRoutes.php';
require __DIR__ . '/../rest/routes/GameRoutes.php';
require __DIR__ . '/../rest/routes/MoveRoutes.php';
require __DIR__ . '/../rest/routes/TournamentParticipantRoutes.php';
require __DIR__ . '/../rest/routes/ReviewRoutes.php';

// Start Flight
Flight::start();
