<?php
/**
 * @OA\Get(
 *      path="/api/tournament-participants",
 *      tags={"Tournament Participants"},
 *      summary="Get all tournament participants",
 *      @OA\Response(
 *           response=200,
 *           description="Array of all participants"
 *      )
 * )
 */
Flight::route('GET /api/tournament-participants', function(){
    $filters = Flight::request()->query->getData();
    Flight::json(Flight::participantService()->getAll($filters));
});

/**
 * @OA\Get(
 *     path="/api/tournament-participants/{id}",
 *     tags={"Tournament Participants"},
 *     summary="Get participant by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Returns participant"
 *     )
 * )
 */
Flight::route('GET /api/tournament-participants/@id', function($id){
    $participant = Flight::participantService()->getById((int) $id);
    if ($participant === null) {
        Flight::json(['error' => 'Participant not found'], 404);
        return;
    }
    Flight::json($participant);
});

/**
 * @OA\Post(
 *     path="/api/tournament-participants",
 *     tags={"Tournament Participants"},
 *     summary="Register a participant",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"tournament_id", "user_id"},
 *             @OA\Property(property="tournament_id", type="integer", example=1),
 *             @OA\Property(property="user_id", type="integer", example=1),
 *             @OA\Property(property="seed", type="integer", example=1),
 *             @OA\Property(property="status", type="string", example="registered")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Participant registered"
 *     )
 * )
 */
Flight::route('POST /api/tournament-participants', function(){
    $data = Flight::request()->data->getData();
    $participant = Flight::participantService()->create($data);
    Flight::json($participant, 201);
});

/**
 * @OA\Put(
 *     path="/api/tournament-participants/{id}",
 *     tags={"Tournament Participants"},
 *     summary="Update a participant",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Participant updated"
 *     )
 * )
 */
Flight::route('PUT /api/tournament-participants/@id', function($id){
    $data = Flight::request()->data->getData();
    $participant = Flight::participantService()->update((int) $id, $data);
    if ($participant === null) {
        Flight::json(['error' => 'Participant not found'], 404);
        return;
    }
    Flight::json($participant);
});

/**
 * @OA\Patch(
 *     path="/api/tournament-participants/{id}",
 *     tags={"Tournament Participants"},
 *     summary="Partially update participant",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Participant updated"
 *     )
 * )
 */
Flight::route('PATCH /api/tournament-participants/@id', function($id){
    $data = Flight::request()->data->getData();
    $participant = Flight::participantService()->update((int) $id, $data);
    if ($participant === null) {
        Flight::json(['error' => 'Participant not found'], 404);
        return;
    }
    Flight::json($participant);
});

/**
 * @OA\Delete(
 *     path="/api/tournament-participants/{id}",
 *     tags={"Tournament Participants"},
 *     summary="Delete a participant",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Participant deleted"
 *     )
 * )
 */
Flight::route('DELETE /api/tournament-participants/@id', function($id){
    $deleted = Flight::participantService()->delete((int) $id);
    if (!$deleted) {
        Flight::json(['error' => 'Participant not found'], 404);
        return;
    }
    http_response_code(204);
});
?>
