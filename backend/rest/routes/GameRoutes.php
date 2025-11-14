<?php
/**
 * @OA\Get(
 *      path="/api/games",
 *      tags={"Games"},
 *      summary="Get all games",
 *      @OA\Response(
 *           response=200,
 *           description="Array of all games"
 *      )
 * )
 */
Flight::route('GET /api/games', function(){
    $filters = Flight::request()->query->getData();
    Flight::json(Flight::gameService()->getAll($filters));
});

/**
 * @OA\Get(
 *     path="/api/games/{id}",
 *     tags={"Games"},
 *     summary="Get game by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Returns game"
 *     )
 * )
 */
Flight::route('GET /api/games/@id', function($id){
    $game = Flight::gameService()->getById((int) $id);
    if ($game === null) {
        Flight::json(['error' => 'Game not found'], 404);
        return;
    }
    Flight::json($game);
});

/**
 * @OA\Get(
 *     path="/api/games/{id}/details",
 *     tags={"Games"},
 *     summary="Get game with moves and players",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Game with moves"
 *     )
 * )
 */
Flight::route('GET /api/games/@id/details', function($id){
    $game = Flight::gameService()->getGameWithMoves((int) $id);
    if ($game === null) {
        Flight::json(['error' => 'Game not found'], 404);
        return;
    }
    Flight::json($game);
});

/**
 * @OA\Post(
 *     path="/api/games",
 *     tags={"Games"},
 *     summary="Create a new game",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"tournament_id", "white_player_id", "black_player_id"},
 *             @OA\Property(property="tournament_id", type="integer", example=1),
 *             @OA\Property(property="white_player_id", type="integer", example=1),
 *             @OA\Property(property="black_player_id", type="integer", example=2),
 *             @OA\Property(property="result", type="string", example="in_progress"),
 *             @OA\Property(property="pgn", type="string", example="1.e4 e5")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Game created"
 *     )
 * )
 */
Flight::route('POST /api/games', function(){
    $data = Flight::request()->data->getData();
    $game = Flight::gameService()->create($data);
    Flight::json($game, 201);
});

/**
 * @OA\Put(
 *     path="/api/games/{id}",
 *     tags={"Games"},
 *     summary="Update a game",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Game updated"
 *     )
 * )
 */
Flight::route('PUT /api/games/@id', function($id){
    $data = Flight::request()->data->getData();
    $game = Flight::gameService()->update((int) $id, $data);
    if ($game === null) {
        Flight::json(['error' => 'Game not found'], 404);
        return;
    }
    Flight::json($game);
});

/**
 * @OA\Patch(
 *     path="/api/games/{id}",
 *     tags={"Games"},
 *     summary="Partially update game",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Game updated"
 *     )
 * )
 */
Flight::route('PATCH /api/games/@id', function($id){
    $data = Flight::request()->data->getData();
    $game = Flight::gameService()->update((int) $id, $data);
    if ($game === null) {
        Flight::json(['error' => 'Game not found'], 404);
        return;
    }
    Flight::json($game);
});

/**
 * @OA\Delete(
 *     path="/api/games/{id}",
 *     tags={"Games"},
 *     summary="Delete a game",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Game deleted"
 *     )
 * )
 */
Flight::route('DELETE /api/games/@id', function($id){
    $deleted = Flight::gameService()->delete((int) $id);
    if (!$deleted) {
        Flight::json(['error' => 'Game not found'], 404);
        return;
    }
    http_response_code(204);
});
?>
