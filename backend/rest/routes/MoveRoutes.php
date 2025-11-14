<?php
/**
 * @OA\Get(
 *      path="/api/moves",
 *      tags={"Moves"},
 *      summary="Get all moves",
 *      @OA\Response(
 *           response=200,
 *           description="Array of all moves"
 *      )
 * )
 */
Flight::route('GET /api/moves', function(){
    $filters = Flight::request()->query->getData();
    Flight::json(Flight::moveService()->getAll($filters));
});

/**
 * @OA\Get(
 *     path="/api/moves/{id}",
 *     tags={"Moves"},
 *     summary="Get move by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Returns move"
 *     )
 * )
 */
Flight::route('GET /api/moves/@id', function($id){
    $move = Flight::moveService()->getById((int) $id);
    if ($move === null) {
        Flight::json(['error' => 'Move not found'], 404);
        return;
    }
    Flight::json($move);
});

/**
 * @OA\Post(
 *     path="/api/moves",
 *     tags={"Moves"},
 *     summary="Record a new move",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"game_id", "move_number", "notation"},
 *             @OA\Property(property="game_id", type="integer", example=1),
 *             @OA\Property(property="move_number", type="integer", example=1),
 *             @OA\Property(property="notation", type="string", example="e4"),
 *             @OA\Property(property="time_taken", type="integer", example=30),
 *             @OA\Property(property="comment", type="string", example="Opening move")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Move recorded"
 *     )
 * )
 */
Flight::route('POST /api/moves', function(){
    $data = Flight::request()->data->getData();
    $move = Flight::moveService()->create($data);
    Flight::json($move, 201);
});

/**
 * @OA\Put(
 *     path="/api/moves/{id}",
 *     tags={"Moves"},
 *     summary="Update a move",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Move updated"
 *     )
 * )
 */
Flight::route('PUT /api/moves/@id', function($id){
    $data = Flight::request()->data->getData();
    $move = Flight::moveService()->update((int) $id, $data);
    if ($move === null) {
        Flight::json(['error' => 'Move not found'], 404);
        return;
    }
    Flight::json($move);
});

/**
 * @OA\Patch(
 *     path="/api/moves/{id}",
 *     tags={"Moves"},
 *     summary="Partially update move",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Move updated"
 *     )
 * )
 */
Flight::route('PATCH /api/moves/@id', function($id){
    $data = Flight::request()->data->getData();
    $move = Flight::moveService()->update((int) $id, $data);
    if ($move === null) {
        Flight::json(['error' => 'Move not found'], 404);
        return;
    }
    Flight::json($move);
});

/**
 * @OA\Delete(
 *     path="/api/moves/{id}",
 *     tags={"Moves"},
 *     summary="Delete a move",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Move deleted"
 *     )
 * )
 */
Flight::route('DELETE /api/moves/@id', function($id){
    $deleted = Flight::moveService()->delete((int) $id);
    if (!$deleted) {
        Flight::json(['error' => 'Move not found'], 404);
        return;
    }
    http_response_code(204);
});
?>
