<?php
/**
 * Challenge Routes
 */

// Get all challenges for current user
Flight::route('GET /api/challenges', function() {
    Rest\Middleware\AuthMiddleware::handle();
    
    $userId = Flight::request()->data->user['id'];
    $challenges = Flight::challengeService()->getChallengesForUser($userId);
    Flight::json($challenges);
});

// Create a new challenge
Flight::route('POST /api/challenges', function() {
    Rest\Middleware\AuthMiddleware::handle();
    
    $userId = Flight::request()->data->user['id'];
    $data = Flight::request()->data->getData();
    
    // Get challenged user by username if provided
    if (isset($data['challenged_username'])) {
        $stmt = Flight::challengeService()->dao->db->prepare("SELECT id FROM users WHERE username = :username");
        $stmt->execute(['username' => $data['challenged_username']]);
        $user = $stmt->fetch();
        
        if (!$user) {
            Flight::json(['error' => 'User not found'], 404);
            return;
        }
        
        $data['challenged_id'] = $user['id'];
    }
    
    $data['challenger_id'] = $userId;
    
    $challenge = Flight::challengeService()->create($data);
    Flight::json($challenge, 201);
});

// Accept a challenge
Flight::route('POST /api/challenges/@id/accept', function($id) {
    Rest\Middleware\AuthMiddleware::handle();
    
    $userId = Flight::request()->data->user['id'];
    $result = Flight::challengeService()->acceptChallenge((int) $id, $userId);
    Flight::json($result);
});

// Decline a challenge
Flight::route('POST /api/challenges/@id/decline', function($id) {
    Rest\Middleware\AuthMiddleware::handle();
    
    $userId = Flight::request()->data->user['id'];
    Flight::challengeService()->declineChallenge((int) $id, $userId);
    Flight::json(['success' => true]);
});

// Cancel a challenge
Flight::route('POST /api/challenges/@id/cancel', function($id) {
    Rest\Middleware\AuthMiddleware::handle();
    
    $userId = Flight::request()->data->user['id'];
    Flight::challengeService()->cancelChallenge((int) $id, $userId);
    Flight::json(['success' => true]);
});

// Get challenge by ID
Flight::route('GET /api/challenges/@id', function($id) {
    Rest\Middleware\AuthMiddleware::handle();
    
    $challenge = Flight::challengeService()->getById((int) $id);
    if ($challenge === null) {
        Flight::json(['error' => 'Challenge not found'], 404);
        return;
    }
    Flight::json($challenge);
});
?>
