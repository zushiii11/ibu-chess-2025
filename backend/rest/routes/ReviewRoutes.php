<?php
/**
 * @OA\Get(
 *      path="/api/reviews",
 *      tags={"Reviews"},
 *      summary="Get all reviews",
 *      @OA\Response(
 *           response=200,
 *           description="Array of all reviews"
 *      )
 * )
 */
Flight::route('GET /api/reviews', function(){
    $filters = Flight::request()->query->getData();
    Flight::json(Flight::reviewService()->getAll($filters));
});

/**
 * @OA\Get(
 *     path="/api/reviews/{id}",
 *     tags={"Reviews"},
 *     summary="Get review by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Returns review"
 *     )
 * )
 */
Flight::route('GET /api/reviews/@id', function($id){
    $review = Flight::reviewService()->getById((int) $id);
    if ($review === null) {
        Flight::json(['error' => 'Review not found'], 404);
        return;
    }
    Flight::json($review);
});

/**
 * @OA\Post(
 *     path="/api/reviews",
 *     tags={"Reviews"},
 *     summary="Create a review",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"game_id", "user_id", "rating"},
 *             @OA\Property(property="game_id", type="integer", example=1),
 *             @OA\Property(property="user_id", type="integer", example=1),
 *             @OA\Property(property="rating", type="integer", example=5),
 *             @OA\Property(property="comment", type="string", example="Great game!")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Review created"
 *     )
 * )
 */
Flight::route('POST /api/reviews', function(){
    Rest\Middleware\LoggingMiddleware::handle();
    Rest\Middleware\AuthMiddleware::handle();
    
    $currentUser = Flight::get('user');
    $data = Flight::request()->data->getData();
    
    // Set user_id from authenticated user
    $data['user_id'] = $currentUser['id'];
    
    $review = Flight::reviewService()->create($data);
    Flight::json($review, 201);
});

/**
 * @OA\Put(
 *     path="/api/reviews/{id}",
 *     tags={"Reviews"},
 *     summary="Update a review",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Review updated"
 *     )
 * )
 */
Flight::route('PUT /api/reviews/@id', function($id){
    Rest\Middleware\LoggingMiddleware::handle();
    Rest\Middleware\AuthMiddleware::handle();
    
    $currentUser = Flight::get('user');
    $review = Flight::reviewService()->getById((int) $id);
    
    if ($review === null) {
        Flight::json(['error' => 'Review not found'], 404);
        return;
    }
    
    // Users can only update their own reviews, unless they're admin
    if ($currentUser['role'] !== 'admin' && $review['user_id'] != $currentUser['id']) {
        Flight::json(['error' => 'Forbidden - You can only update your own reviews'], 403);
        return;
    }
    
    $data = Flight::request()->data->getData();
    $review = Flight::reviewService()->update((int) $id, $data);
    Flight::json($review);
});

/**
 * @OA\Patch(
 *     path="/api/reviews/{id}",
 *     tags={"Reviews"},
 *     summary="Partially update review",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Review updated"
 *     )
 * )
 */
Flight::route('PATCH /api/reviews/@id', function($id){
    Rest\Middleware\LoggingMiddleware::handle();
    Rest\Middleware\AuthMiddleware::handle();
    
    $currentUser = Flight::get('user');
    $review = Flight::reviewService()->getById((int) $id);
    
    if ($review === null) {
        Flight::json(['error' => 'Review not found'], 404);
        return;
    }
    
    // Users can only update their own reviews, unless they're admin
    if ($currentUser['role'] !== 'admin' && $review['user_id'] != $currentUser['id']) {
        Flight::json(['error' => 'Forbidden - You can only update your own reviews'], 403);
        return;
    }
    
    $data = Flight::request()->data->getData();
    $review = Flight::reviewService()->update((int) $id, $data);
    Flight::json($review);
});

/**
 * @OA\Delete(
 *     path="/api/reviews/{id}",
 *     tags={"Reviews"},
 *     summary="Delete a review",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Review deleted"
 *     )
 * )
 */
Flight::route('DELETE /api/reviews/@id', function($id){
    Rest\Middleware\LoggingMiddleware::handle();
    Rest\Middleware\AuthMiddleware::handle();
    
    $currentUser = Flight::get('user');
    $review = Flight::reviewService()->getById((int) $id);
    
    if ($review === null) {
        Flight::json(['error' => 'Review not found'], 404);
        return;
    }
    
    // Users can only delete their own reviews, unless they're admin
    if ($currentUser['role'] !== 'admin' && $review['user_id'] != $currentUser['id']) {
        Flight::json(['error' => 'Forbidden - You can only delete your own reviews'], 403);
        return;
    }
    
    $deleted = Flight::reviewService()->delete((int) $id);
    if (!$deleted) {
        Flight::json(['error' => 'Review not found'], 404);
        return;
    }
    http_response_code(204);
});
?>
