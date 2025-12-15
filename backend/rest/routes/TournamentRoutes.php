<?php
/**
 * @OA\Get(
 *      path="/api/tournaments",
 *      tags={"Tournaments"},
 *      summary="Get all tournaments",
 *      @OA\Parameter(
 *          name="location",
 *          in="query",
 *          required=false,
 *          @OA\Schema(type="string"),
 *          description="Optional location to filter tournaments"
 *      ),
 *      @OA\Response(
 *           response=200,
 *           description="Array of all tournaments"
 *      )
 * )
 */
Flight::route('GET /api/tournaments', function(){
    $filters = Flight::request()->query->getData();
    Flight::json(Flight::tournamentService()->getAll($filters));
});

/**
 * @OA\Get(
 *     path="/api/tournaments/{id}",
 *     tags={"Tournaments"},
 *     summary="Get tournament by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Tournament ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Returns tournament"
 *     )
 * )
 */
Flight::route('GET /api/tournaments/@id', function($id){
    $tournament = Flight::tournamentService()->getById((int) $id);
    if ($tournament === null) {
        Flight::json(['error' => 'Tournament not found'], 404);
        return;
    }
    Flight::json($tournament);
});

/**
 * @OA\Get(
 *     path="/api/tournaments/{id}/participants",
 *     tags={"Tournaments"},
 *     summary="Get tournament with participants",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Tournament ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Returns tournament with all participants"
 *     )
 * )
 */
Flight::route('GET /api/tournaments/@id/participants', function($id){
    $tournament = Flight::tournamentService()->getTournamentWithParticipants((int) $id);
    if ($tournament === null) {
        Flight::json(['error' => 'Tournament not found'], 404);
        return;
    }
    Flight::json($tournament);
});

/**
 * @OA\Post(
 *     path="/api/tournaments",
 *     tags={"Tournaments"},
 *     summary="Create a new tournament",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name", "start_date", "end_date"},
 *             @OA\Property(property="name", type="string", example="World Championship 2025"),
 *             @OA\Property(property="location", type="string", example="Dubai"),
 *             @OA\Property(property="start_date", type="string", format="date", example="2025-11-01"),
 *             @OA\Property(property="end_date", type="string", format="date", example="2025-11-30"),
 *             @OA\Property(property="prize_pool", type="number", example=500000)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Tournament created"
 *     )
 * )
 */
Flight::route('POST /api/tournaments', function(){
    Rest\Middleware\LoggingMiddleware::handle();
    Rest\Middleware\AuthMiddleware::handle();
    Rest\Middleware\RoleMiddleware::handle(['admin']);
    
    $data = Flight::request()->data->getData();
    $tournament = Flight::tournamentService()->create($data);
    Flight::json($tournament, 201);
});

/**
 * @OA\Put(
 *     path="/api/tournaments/{id}",
 *     tags={"Tournaments"},
 *     summary="Update a tournament",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="location", type="string"),
 *             @OA\Property(property="prize_pool", type="number")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Tournament updated"
 *     )
 * )
 */
Flight::route('PUT /api/tournaments/@id', function($id){
    Rest\Middleware\LoggingMiddleware::handle();
    Rest\Middleware\AuthMiddleware::handle();
    Rest\Middleware\RoleMiddleware::handle(['admin']);
    
    $data = Flight::request()->data->getData();
    $tournament = Flight::tournamentService()->update((int) $id, $data);
    if ($tournament === null) {
        Flight::json(['error' => 'Tournament not found'], 404);
        return;
    }
    Flight::json($tournament);
});

/**
 * @OA\Patch(
 *     path="/api/tournaments/{id}",
 *     tags={"Tournaments"},
 *     summary="Partially update tournament",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Tournament updated"
 *     )
 * )
 */
Flight::route('PATCH /api/tournaments/@id', function($id){
    Rest\Middleware\LoggingMiddleware::handle();
    Rest\Middleware\AuthMiddleware::handle();
    Rest\Middleware\RoleMiddleware::handle(['admin']);
    
    $data = Flight::request()->data->getData();
    $tournament = Flight::tournamentService()->update((int) $id, $data);
    if ($tournament === null) {
        Flight::json(['error' => 'Tournament not found'], 404);
        return;
    }
    Flight::json($tournament);
});

/**
 * @OA\Delete(
 *     path="/api/tournaments/{id}",
 *     tags={"Tournaments"},
 *     summary="Delete a tournament",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Tournament deleted"
 *     )
 * )
 */
Flight::route('DELETE /api/tournaments/@id', function($id){
    Rest\Middleware\LoggingMiddleware::handle();
    Rest\Middleware\AuthMiddleware::handle();
    Rest\Middleware\RoleMiddleware::handle(['admin']);
    
    $deleted = Flight::tournamentService()->delete((int) $id);
    if (!$deleted) {
        Flight::json(['error' => 'Tournament not found'], 404);
        return;
    }
    http_response_code(204);
});
?>
