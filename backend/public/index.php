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

// Initialize services
$userService = new UserService(new UserDao($pdo));
$tournamentService = new TournamentService(new TournamentDao($pdo));
$gameService = new GameService(new GameDao($pdo));
$moveService = new MoveService(new MoveDao($pdo));
$participantService = new TournamentParticipantService(new TournamentParticipantDao($pdo));
$reviewService = new ReviewService(new ReviewDao($pdo));

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

// JSON response helper
function jsonResponse($data, int $statusCode = 200): void
{
    Flight::json($data, $statusCode);
}

// Error handler
Flight::map('error', function (Throwable $error) {
    $statusCode = 500;
    $message = $error->getMessage();
    
    if ($error instanceof InvalidArgumentException) {
        $statusCode = 400;
    } elseif ($error instanceof RuntimeException) {
        $statusCode = 404;
    }
    
    jsonResponse(['error' => $message], $statusCode);
});

// Root endpoint
Flight::route('GET /', function () {
    jsonResponse([
        'message' => 'IBU Chess API - Milestone 3',
        'version' => '3.0.0',
        'documentation' => '/api-docs',
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

// API documentation endpoint
Flight::route('GET /api-docs', function () {
    $swaggerPath = __DIR__ . '/../docs/swagger.yaml';
    if (file_exists($swaggerPath)) {
        Flight::redirect('/swagger-ui/');
    } else {
        jsonResponse(['message' => 'API documentation is being generated']);
    }
});

// ====================
// USERS ENDPOINTS
// ====================
Flight::route('GET /api/users', function () use ($userService) {
    $filters = Flight::request()->query->getData();
    jsonResponse($userService->getAll($filters));
});

Flight::route('GET /api/users/@id', function ($id) use ($userService) {
    $user = $userService->getById((int) $id);
    if ($user === null) {
        jsonResponse(['error' => 'User not found'], 404);
        return;
    }
    jsonResponse($user);
});

Flight::route('POST /api/users', function () use ($userService) {
    $data = Flight::request()->data->getData();
    $user = $userService->create($data);
    jsonResponse($user, 201);
});

Flight::route('PUT /api/users/@id', function ($id) use ($userService) {
    $data = Flight::request()->data->getData();
    $user = $userService->update((int) $id, $data);
    if ($user === null) {
        jsonResponse(['error' => 'User not found'], 404);
        return;
    }
    jsonResponse($user);
});

Flight::route('PATCH /api/users/@id', function ($id) use ($userService) {
    $data = Flight::request()->data->getData();
    $user = $userService->update((int) $id, $data);
    if ($user === null) {
        jsonResponse(['error' => 'User not found'], 404);
        return;
    }
    jsonResponse($user);
});

Flight::route('DELETE /api/users/@id', function ($id) use ($userService) {
    $deleted = $userService->delete((int) $id);
    if (!$deleted) {
        jsonResponse(['error' => 'User not found'], 404);
        return;
    }
    http_response_code(204);
});

// ====================
// TOURNAMENTS ENDPOINTS
// ====================
Flight::route('GET /api/tournaments', function () use ($tournamentService) {
    $filters = Flight::request()->query->getData();
    jsonResponse($tournamentService->getAll($filters));
});

Flight::route('GET /api/tournaments/@id', function ($id) use ($tournamentService) {
    $tournament = $tournamentService->getById((int) $id);
    if ($tournament === null) {
        jsonResponse(['error' => 'Tournament not found'], 404);
        return;
    }
    jsonResponse($tournament);
});

Flight::route('GET /api/tournaments/@id/participants', function ($id) use ($tournamentService) {
    $tournament = $tournamentService->getTournamentWithParticipants((int) $id);
    if ($tournament === null) {
        jsonResponse(['error' => 'Tournament not found'], 404);
        return;
    }
    jsonResponse($tournament);
});

Flight::route('POST /api/tournaments', function () use ($tournamentService) {
    $data = Flight::request()->data->getData();
    $tournament = $tournamentService->create($data);
    jsonResponse($tournament, 201);
});

Flight::route('PUT /api/tournaments/@id', function ($id) use ($tournamentService) {
    $data = Flight::request()->data->getData();
    $tournament = $tournamentService->update((int) $id, $data);
    if ($tournament === null) {
        jsonResponse(['error' => 'Tournament not found'], 404);
        return;
    }
    jsonResponse($tournament);
});

Flight::route('PATCH /api/tournaments/@id', function ($id) use ($tournamentService) {
    $data = Flight::request()->data->getData();
    $tournament = $tournamentService->update((int) $id, $data);
    if ($tournament === null) {
        jsonResponse(['error' => 'Tournament not found'], 404);
        return;
    }
    jsonResponse($tournament);
});

Flight::route('DELETE /api/tournaments/@id', function ($id) use ($tournamentService) {
    $deleted = $tournamentService->delete((int) $id);
    if (!$deleted) {
        jsonResponse(['error' => 'Tournament not found'], 404);
        return;
    }
    http_response_code(204);
});

// ====================
// GAMES ENDPOINTS
// ====================
Flight::route('GET /api/games', function () use ($gameService) {
    $filters = Flight::request()->query->getData();
    jsonResponse($gameService->getAll($filters));
});

Flight::route('GET /api/games/@id', function ($id) use ($gameService) {
    $game = $gameService->getById((int) $id);
    if ($game === null) {
        jsonResponse(['error' => 'Game not found'], 404);
        return;
    }
    jsonResponse($game);
});

Flight::route('GET /api/games/@id/details', function ($id) use ($gameService) {
    $game = $gameService->getGameWithMoves((int) $id);
    if ($game === null) {
        jsonResponse(['error' => 'Game not found'], 404);
        return;
    }
    jsonResponse($game);
});

Flight::route('POST /api/games', function () use ($gameService) {
    $data = Flight::request()->data->getData();
    $game = $gameService->create($data);
    jsonResponse($game, 201);
});

Flight::route('PUT /api/games/@id', function ($id) use ($gameService) {
    $data = Flight::request()->data->getData();
    $game = $gameService->update((int) $id, $data);
    if ($game === null) {
        jsonResponse(['error' => 'Game not found'], 404);
        return;
    }
    jsonResponse($game);
});

Flight::route('PATCH /api/games/@id', function ($id) use ($gameService) {
    $data = Flight::request()->data->getData();
    $game = $gameService->update((int) $id, $data);
    if ($game === null) {
        jsonResponse(['error' => 'Game not found'], 404);
        return;
    }
    jsonResponse($game);
});

Flight::route('DELETE /api/games/@id', function ($id) use ($gameService) {
    $deleted = $gameService->delete((int) $id);
    if (!$deleted) {
        jsonResponse(['error' => 'Game not found'], 404);
        return;
    }
    http_response_code(204);
});

// ====================
// MOVES ENDPOINTS
// ====================
Flight::route('GET /api/moves', function () use ($moveService) {
    $filters = Flight::request()->query->getData();
    jsonResponse($moveService->getAll($filters));
});

Flight::route('GET /api/moves/@id', function ($id) use ($moveService) {
    $move = $moveService->getById((int) $id);
    if ($move === null) {
        jsonResponse(['error' => 'Move not found'], 404);
        return;
    }
    jsonResponse($move);
});

Flight::route('POST /api/moves', function () use ($moveService) {
    $data = Flight::request()->data->getData();
    $move = $moveService->create($data);
    jsonResponse($move, 201);
});

Flight::route('PUT /api/moves/@id', function ($id) use ($moveService) {
    $data = Flight::request()->data->getData();
    $move = $moveService->update((int) $id, $data);
    if ($move === null) {
        jsonResponse(['error' => 'Move not found'], 404);
        return;
    }
    jsonResponse($move);
});

Flight::route('PATCH /api/moves/@id', function ($id) use ($moveService) {
    $data = Flight::request()->data->getData();
    $move = $moveService->update((int) $id, $data);
    if ($move === null) {
        jsonResponse(['error' => 'Move not found'], 404);
        return;
    }
    jsonResponse($move);
});

Flight::route('DELETE /api/moves/@id', function ($id) use ($moveService) {
    $deleted = $moveService->delete((int) $id);
    if (!$deleted) {
        jsonResponse(['error' => 'Move not found'], 404);
        return;
    }
    http_response_code(204);
});

// ====================
// TOURNAMENT PARTICIPANTS ENDPOINTS
// ====================
Flight::route('GET /api/tournament-participants', function () use ($participantService) {
    $filters = Flight::request()->query->getData();
    jsonResponse($participantService->getAll($filters));
});

Flight::route('GET /api/tournament-participants/@id', function ($id) use ($participantService) {
    $participant = $participantService->getById((int) $id);
    if ($participant === null) {
        jsonResponse(['error' => 'Participant not found'], 404);
        return;
    }
    jsonResponse($participant);
});

Flight::route('POST /api/tournament-participants', function () use ($participantService) {
    $data = Flight::request()->data->getData();
    $participant = $participantService->create($data);
    jsonResponse($participant, 201);
});

Flight::route('PUT /api/tournament-participants/@id', function ($id) use ($participantService) {
    $data = Flight::request()->data->getData();
    $participant = $participantService->update((int) $id, $data);
    if ($participant === null) {
        jsonResponse(['error' => 'Participant not found'], 404);
        return;
    }
    jsonResponse($participant);
});

Flight::route('PATCH /api/tournament-participants/@id', function ($id) use ($participantService) {
    $data = Flight::request()->data->getData();
    $participant = $participantService->update((int) $id, $data);
    if ($participant === null) {
        jsonResponse(['error' => 'Participant not found'], 404);
        return;
    }
    jsonResponse($participant);
});

Flight::route('DELETE /api/tournament-participants/@id', function ($id) use ($participantService) {
    $deleted = $participantService->delete((int) $id);
    if (!$deleted) {
        jsonResponse(['error' => 'Participant not found'], 404);
        return;
    }
    http_response_code(204);
});

// ====================
// REVIEWS ENDPOINTS
// ====================
Flight::route('GET /api/reviews', function () use ($reviewService) {
    $filters = Flight::request()->query->getData();
    jsonResponse($reviewService->getAll($filters));
});

Flight::route('GET /api/reviews/@id', function ($id) use ($reviewService) {
    $review = $reviewService->getById((int) $id);
    if ($review === null) {
        jsonResponse(['error' => 'Review not found'], 404);
        return;
    }
    jsonResponse($review);
});

Flight::route('POST /api/reviews', function () use ($reviewService) {
    $data = Flight::request()->data->getData();
    $review = $reviewService->create($data);
    jsonResponse($review, 201);
});

Flight::route('PUT /api/reviews/@id', function ($id) use ($reviewService) {
    $data = Flight::request()->data->getData();
    $review = $reviewService->update((int) $id, $data);
    if ($review === null) {
        jsonResponse(['error' => 'Review not found'], 404);
        return;
    }
    jsonResponse($review);
});

Flight::route('PATCH /api/reviews/@id', function ($id) use ($reviewService) {
    $data = Flight::request()->data->getData();
    $review = $reviewService->update((int) $id, $data);
    if ($review === null) {
        jsonResponse(['error' => 'Review not found'], 404);
        return;
    }
    jsonResponse($review);
});

Flight::route('DELETE /api/reviews/@id', function ($id) use ($reviewService) {
    $deleted = $reviewService->delete((int) $id);
    if (!$deleted) {
        jsonResponse(['error' => 'Review not found'], 404);
        return;
    }
    http_response_code(204);
});

// Start Flight
Flight::start();
