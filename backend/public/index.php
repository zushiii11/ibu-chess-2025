<?php

declare(strict_types=1);

// Start output buffering to catch any errors
ob_start();

use App\Config\Database;
use Rest\Dao\GameDao;
use Rest\Dao\MoveDao;
use Rest\Dao\ReviewDao;
use Rest\Dao\TournamentDao;
use Rest\Dao\TournamentParticipantDao;
use Rest\Dao\UserDao;
use Rest\Dao\ChallengeDao;
use Rest\Dao\MatchmakingDao;
use Rest\Dao\AiGameDao;
use Rest\Services\AuthService;
use Rest\Services\GameService;
use Rest\Services\MoveService;
use Rest\Services\ReviewService;
use Rest\Services\TournamentParticipantService;
use Rest\Services\TournamentService;
use Rest\Services\UserService;
use Rest\Services\ChallengeService;
use Rest\Services\MatchmakingService;
use Rest\Services\AiGameService;

require_once __DIR__ . '/../bootstrap.php';

// Try multiple vendor paths (check root first, then backend)
$vendorPaths = [
    __DIR__ . '/../../vendor/autoload.php',  // Root vendor (preferred)
    __DIR__ . '/../vendor/autoload.php'      // Backend vendor (fallback)
];

$vendorLoaded = false;
$vendorPath = null;
foreach ($vendorPaths as $path) {
    if (file_exists($path)) {
        require_once $path;
        $vendorLoaded = true;
        $vendorPath = $path;
        break;
    }
}

if (!$vendorLoaded) {
    // Clear any output
    ob_clean();
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Composer dependencies not found. Run: composer install']);
    exit;
}

// Check if Firebase JWT is available (try to load it)
try {
    if (!class_exists('Firebase\JWT\JWT')) {
        // Try to require it directly
        $firebasePaths = [
            __DIR__ . '/../../vendor/firebase/php-jwt/src/JWT.php',
            __DIR__ . '/../vendor/firebase/php-jwt/src/JWT.php'
        ];
        
        $firebaseLoaded = false;
        foreach ($firebasePaths as $firebasePath) {
            if (file_exists($firebasePath)) {
                require_once $firebasePath;
                require_once dirname($firebasePath) . '/Key.php';
                $firebaseLoaded = true;
                break;
            }
        }
        
        if (!$firebaseLoaded) {
            throw new Exception('Firebase JWT not found');
        }
    }
} catch (Exception $e) {
    ob_clean();
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'Firebase JWT library not installed. Run: composer require firebase/php-jwt',
        'details' => 'The JWT library is required for authentication. Please install it using composer.'
    ]);
    exit;
}

// Clear output buffer (in case of any warnings)
ob_clean();

// Initialize database connection
$pdo = Database::connection();

// Initialize DAOs
$userDao = new UserDao($pdo);
$tournamentDao = new TournamentDao($pdo);
$gameDao = new GameDao($pdo);
$moveDao = new MoveDao($pdo);
$participantDao = new TournamentParticipantDao($pdo);
$reviewDao = new ReviewDao($pdo);
$challengeDao = new ChallengeDao($pdo);
$matchmakingDao = new MatchmakingDao($pdo);
$aiGameDao = new AiGameDao($pdo);

// Initialize services
$userService = new UserService($userDao);
$authService = new AuthService($userService);
$tournamentService = new TournamentService($tournamentDao);
$gameService = new GameService($gameDao);
$moveService = new MoveService($moveDao);
$participantService = new TournamentParticipantService($participantDao);
$reviewService = new ReviewService($reviewDao);
$challengeService = new ChallengeService($challengeDao);
$matchmakingService = new MatchmakingService($matchmakingDao);
$aiGameService = new AiGameService($aiGameDao);

// Register services with Flight
Flight::register('userService', UserService::class, [$userDao]);
Flight::register('authService', AuthService::class, [$userService]);
Flight::register('tournamentService', TournamentService::class, [$tournamentDao]);
Flight::register('gameService', GameService::class, [$gameDao]);
Flight::register('moveService', MoveService::class, [$moveDao]);
Flight::register('participantService', TournamentParticipantService::class, [$participantDao]);
Flight::register('reviewService', ReviewService::class, [$reviewDao]);
Flight::register('challengeService', ChallengeService::class, [$challengeDao]);
Flight::register('matchmakingService', MatchmakingService::class, [$matchmakingDao]);
Flight::register('aiGameService', AiGameService::class, [$aiGameDao]);

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
    // Clear any output
    ob_clean();
    
    $statusCode = 500;
    $message = $error->getMessage();
    
    if ($error instanceof InvalidArgumentException) {
        $statusCode = 400;
    } elseif ($error instanceof RuntimeException) {
        $statusCode = 404;
    }
    
    // Log error for debugging
    error_log('FlightPHP Error: ' . $message);
    error_log('Stack trace: ' . $error->getTraceAsString());
    
    // Ensure we output JSON even on errors
    header('Content-Type: application/json');
    Flight::json(['error' => $message], $statusCode);
});

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', '0'); // Don't display errors (we handle them)
ini_set('log_errors', '1');

// Catch fatal errors
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        ob_clean();
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode([
            'error' => 'Fatal error: ' . $error['message'],
            'file' => $error['file'],
            'line' => $error['line']
        ]);
        exit;
    }
});

// Root endpoint
Flight::route('GET /', function () {
    Flight::json([
        'message' => 'IBU Chess API - Milestone 4',
        'version' => '4.0.0',
        'documentation' => '/v1/docs/',
        'authentication' => [
            'POST /api/auth/register' => 'Register new user',
            'POST /api/auth/login' => 'Login user',
            'GET /api/auth/me' => 'Get current user (requires authentication)'
        ],
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
require __DIR__ . '/../rest/routes/AuthRoutes.php';
require __DIR__ . '/../rest/routes/UserRoutes.php';
require __DIR__ . '/../rest/routes/TournamentRoutes.php';
require __DIR__ . '/../rest/routes/GameRoutes.php';
require __DIR__ . '/../rest/routes/MoveRoutes.php';
require __DIR__ . '/../rest/routes/TournamentParticipantRoutes.php';
require __DIR__ . '/../rest/routes/ReviewRoutes.php';
require __DIR__ . '/../rest/routes/ChallengeRoutes.php';
require __DIR__ . '/../rest/routes/MatchmakingRoutes.php';
require __DIR__ . '/../rest/routes/AiGameRoutes.php';

// Start Flight
Flight::start();
