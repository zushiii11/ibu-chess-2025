<?php
/**
 * @OA\Get(
 *      path="/api/users",
 *      tags={"Users"},
 *      summary="Get all users",
 *      @OA\Parameter(
 *          name="role",
 *          in="query",
 *          required=false,
 *          @OA\Schema(type="string"),
 *          description="Optional role to filter users"
 *      ),
 *      @OA\Parameter(
 *          name="limit",
 *          in="query",
 *          required=false,
 *          @OA\Schema(type="integer"),
 *          description="Number of results to return"
 *      ),
 *      @OA\Response(
 *           response=200,
 *           description="Array of all users in the database"
 *      )
 * )
 */
Flight::route('GET /api/users', function(){
    $filters = Flight::request()->query->getData();
    Flight::json(Flight::userService()->getAll($filters));
});

/**
 * @OA\Get(
 *     path="/api/users/{id}",
 *     tags={"Users"},
 *     summary="Get user by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the user",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Returns the user with the given ID"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found"
 *     )
 * )
 */
Flight::route('GET /api/users/@id', function($id){
    $user = Flight::userService()->getById((int) $id);
    if ($user === null) {
        Flight::json(['error' => 'User not found'], 404);
        return;
    }
    Flight::json($user);
});

/**
 * @OA\Post(
 *     path="/api/users",
 *     tags={"Users"},
 *     summary="Create a new user",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"username", "email", "password"},
 *             @OA\Property(property="username", type="string", example="newuser"),
 *             @OA\Property(property="email", type="string", example="user@example.com"),
 *             @OA\Property(property="password", type="string", example="password123"),
 *             @OA\Property(property="role", type="string", example="player"),
 *             @OA\Property(property="rating", type="integer", example=1200)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="New user created"
 *     )
 * )
 */
Flight::route('POST /api/users', function(){
    $data = Flight::request()->data->getData();
    $user = Flight::userService()->create($data);
    Flight::json($user, 201);
});

/**
 * @OA\Put(
 *     path="/api/users/{id}",
 *     tags={"Users"},
 *     summary="Update an existing user by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"username", "email"},
 *             @OA\Property(property="username", type="string", example="Updated Name"),
 *             @OA\Property(property="email", type="string", example="updated@example.com"),
 *             @OA\Property(property="role", type="string", example="player"),
 *             @OA\Property(property="rating", type="integer", example=1500)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User updated"
 *     )
 * )
 */
Flight::route('PUT /api/users/@id', function($id){
    $data = Flight::request()->data->getData();
    $user = Flight::userService()->update((int) $id, $data);
    if ($user === null) {
        Flight::json(['error' => 'User not found'], 404);
        return;
    }
    Flight::json($user);
});

/**
 * @OA\Patch(
 *     path="/api/users/{id}",
 *     tags={"Users"},
 *     summary="Partially update a user by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="username", type="string", example="Partial update"),
 *             @OA\Property(property="email", type="string", example="partial@example.com"),
 *             @OA\Property(property="rating", type="integer", example=1600)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User partially updated"
 *     )
 * )
 */
Flight::route('PATCH /api/users/@id', function($id){
    $data = Flight::request()->data->getData();
    $user = Flight::userService()->update((int) $id, $data);
    if ($user === null) {
        Flight::json(['error' => 'User not found'], 404);
        return;
    }
    Flight::json($user);
});

/**
 * @OA\Delete(
 *     path="/api/users/{id}",
 *     tags={"Users"},
 *     summary="Delete a user by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="User deleted"
 *     )
 * )
 */
Flight::route('DELETE /api/users/@id', function($id){
    $deleted = Flight::userService()->delete((int) $id);
    if (!$deleted) {
        Flight::json(['error' => 'User not found'], 404);
        return;
    }
    http_response_code(204);
});
?>
