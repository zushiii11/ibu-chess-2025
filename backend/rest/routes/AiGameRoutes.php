<?php
/**
 * AI Game Routes
 */

// Create a new AI game
Flight::route('POST /api/ai-games', function() {
    Rest\Middleware\AuthMiddleware::handle();
    
    $userId = Flight::request()->data->user['id'];
    $data = Flight::request()->data->getData();
    
    $difficulty = $data['difficulty'] ?? 'medium';
    $playerColor = $data['player_color'] ?? 'white';
    
    $result = Flight::aiGameService()->createAiGame($userId, $difficulty, $playerColor);
    Flight::json($result, 201);
});

// Make an AI move
Flight::route('POST /api/ai-games/@gameId/move', function($gameId) {
    Rest\Middleware\AuthMiddleware::handle();
    
    $move = Flight::aiGameService()->makeAiMove((int) $gameId);
    Flight::json($move);
});

// Get AI game details
Flight::route('GET /api/ai-games/game/@gameId', function($gameId) {
    Rest\Middleware\AuthMiddleware::handle();
    
    $aiGame = Flight::aiGameService()->getAll(['game_id' => (int) $gameId]);
    
    if (empty($aiGame)) {
        Flight::json(['error' => 'AI game not found'], 404);
        return;
    }
    
    Flight::json($aiGame[0]);
});
?>
