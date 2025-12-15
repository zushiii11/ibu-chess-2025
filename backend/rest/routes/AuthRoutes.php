<?php

declare(strict_types=1);

use Rest\Services\AuthService;

/**
 * @OA\Post(
 *     path="/api/auth/register",
 *     tags={"Authentication"},
 *     summary="Register a new user",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"username", "email", "password"},
 *             @OA\Property(property="username", type="string", example="newuser"),
 *             @OA\Property(property="email", type="string", example="user@example.com"),
 *             @OA\Property(property="password", type="string", example="password123"),
 *             @OA\Property(property="role", type="string", example="player")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="User registered successfully"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation error"
 *     )
 * )
 */
Flight::route('POST /api/auth/register', function () {
    try {
        $data = Flight::request()->data->getData();
        
        if (empty($data)) {
            Flight::json(['error' => 'Request body is empty'], 400);
            return;
        }
        
        $authService = Flight::authService();
        $result = $authService->register($data);
        
        Flight::json([
            'message' => 'User registered successfully',
            'user' => $result['user'],
            'token' => $result['token']
        ], 201);
    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    } catch (Throwable $e) {
        // Catch any other errors
        error_log('Registration error: ' . $e->getMessage());
        error_log('Stack trace: ' . $e->getTraceAsString());
        Flight::json(['error' => 'Server error: ' . $e->getMessage()], 500);
    }
});

/**
 * @OA\Post(
 *     path="/api/auth/login",
 *     tags={"Authentication"},
 *     summary="Login user",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"username", "password"},
 *             @OA\Property(property="username", type="string", example="testuser"),
 *             @OA\Property(property="password", type="string", example="password123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login successful"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Invalid credentials"
 *     )
 * )
 */
Flight::route('POST /api/auth/login', function () {
    try {
        $data = Flight::request()->data->getData();
        
        if (empty($data)) {
            Flight::json(['error' => 'Request body is empty'], 400);
            return;
        }
        
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';
        
        $authService = Flight::authService();
        $result = $authService->login($username, $password);
        
        Flight::json([
            'message' => 'Login successful',
            'user' => $result['user'],
            'token' => $result['token']
        ]);
    } catch (InvalidArgumentException $e) {
        $statusCode = str_contains($e->getMessage(), 'Invalid') ? 401 : 400;
        Flight::json(['error' => $e->getMessage()], $statusCode);
    } catch (Throwable $e) {
        // Catch any other errors
        error_log('Login error: ' . $e->getMessage());
        error_log('Stack trace: ' . $e->getTraceAsString());
        Flight::json(['error' => 'Server error: ' . $e->getMessage()], 500);
    }
});

/**
 * @OA\Get(
 *     path="/api/auth/me",
 *     tags={"Authentication"},
 *     summary="Get current user",
 *     security={{"bearerAuth": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="Current user information"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     )
 * )
 */
Flight::route('GET /api/auth/me', function () {
    // Apply authentication middleware
    Rest\Middleware\AuthMiddleware::handle();
    
    $user = Flight::get('user');
    
    if ($user === null) {
        Flight::json(['error' => 'Unauthorized'], 401);
        return;
    }
    
    // Get full user data from database
    $userService = Flight::userService();
    $fullUser = $userService->getById($user['id']);
    
    if ($fullUser === null) {
        Flight::json(['error' => 'User not found'], 404);
        return;
    }
    
    // Remove password hash
    unset($fullUser['password_hash']);
    
    Flight::json($fullUser);
});

