<?php
/**
 * Matchmaking Routes
 */

// Join matchmaking queue
Flight::route('POST /api/matchmaking/join', function() {
    Rest\Middleware\AuthMiddleware::handle();
    
    $user = Flight::request()->data->user;
    $data = Flight::request()->data->getData();
    
    $timeControl = $data['time_control'] ?? 'blitz';
    $userRating = (int) ($user['rating'] ?? 1500);
    
    $result = Flight::matchmakingService()->joinQueue($user['id'], $timeControl, $userRating);
    Flight::json($result);
});

// Get matchmaking status
Flight::route('GET /api/matchmaking/status', function() {
    Rest\Middleware\AuthMiddleware::handle();
    
    $userId = Flight::request()->data->user['id'];
    $status = Flight::matchmakingService()->getStatus($userId);
    
    if ($status === null) {
        Flight::json(['status' => 'not_in_queue']);
        return;
    }
    
    Flight::json($status);
});

// Cancel matchmaking
Flight::route('POST /api/matchmaking/cancel', function() {
    Rest\Middleware\AuthMiddleware::handle();
    
    $userId = Flight::request()->data->user['id'];
    $result = Flight::matchmakingService()->cancelQueue($userId);
    Flight::json(['success' => $result]);
});
?>
