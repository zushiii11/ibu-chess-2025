<?php

declare(strict_types=1);

namespace Rest\Services;

use InvalidArgumentException;

final class AiGameService extends BaseService
{
    public function createAiGame(int $userId, string $difficulty, string $playerColor): array
    {
        // Get or create AI player
        $stmt = $this->dao->db->prepare("SELECT id FROM users WHERE username = 'AI_Player' LIMIT 1");
        $stmt->execute();
        $aiPlayer = $stmt->fetch();
        
        if (!$aiPlayer) {
            throw new InvalidArgumentException('AI player not found in database');
        }
        
        $aiPlayerId = (int) $aiPlayer['id'];

        // Determine colors
        $whitePlayerId = $playerColor === 'white' ? $userId : $aiPlayerId;
        $blackPlayerId = $playerColor === 'white' ? $aiPlayerId : $userId;

        // Random color
        if ($playerColor === 'random') {
            if (rand(0, 1) === 0) {
                $whitePlayerId = $userId;
                $blackPlayerId = $aiPlayerId;
            } else {
                $whitePlayerId = $aiPlayerId;
                $blackPlayerId = $userId;
            }
        }

        // Create game
        $stmt = $this->dao->db->prepare("
            INSERT INTO games (white_player_id, black_player_id, result, created_at)
            VALUES (:white, :black, 'in_progress', NOW())
        ");
        $stmt->execute([
            'white' => $whitePlayerId,
            'black' => $blackPlayerId
        ]);
        $gameId = (int) $this->dao->db->lastInsertId();

        // Create AI game entry
        $aiGameData = [
            'game_id' => $gameId,
            'difficulty' => $difficulty,
            'ai_engine' => 'basic'
        ];
        $aiGame = $this->dao->create($aiGameData);

        return [
            'game_id' => $gameId,
            'ai_game_id' => $aiGame['id'],
            'user_color' => $whitePlayerId === $userId ? 'white' : 'black',
            'difficulty' => $difficulty,
            'white_player_id' => $whitePlayerId,
            'black_player_id' => $blackPlayerId
        ];
    }

    public function makeAiMove(int $gameId): array
    {
        $aiGame = $this->dao->findAll(['game_id' => $gameId]);
        
        if (empty($aiGame)) {
            throw new InvalidArgumentException('AI game not found');
        }

        // Get game
        $stmt = $this->dao->db->prepare("SELECT * FROM games WHERE id = :id");
        $stmt->execute(['id' => $gameId]);
        $game = $stmt->fetch();

        // Get move count
        $stmt = $this->dao->db->prepare("SELECT COUNT(*) as count FROM moves WHERE game_id = :game_id");
        $stmt->execute(['game_id' => $gameId]);
        $moveCount = (int) $stmt->fetch()['count'];

        // Simple AI: make a random legal-looking move
        $moves = ['e4', 'e5', 'Nf3', 'Nc6', 'd4', 'd5', 'c4', 'c5', 'Bc4', 'Bb5'];
        $randomMove = $moves[array_rand($moves)];

        // Insert move
        $stmt = $this->dao->db->prepare("
            INSERT INTO moves (game_id, move_number, notation, created_at)
            VALUES (:game_id, :move_number, :notation, NOW())
        ");
        $stmt->execute([
            'game_id' => $gameId,
            'move_number' => $moveCount + 1,
            'notation' => $randomMove
        ]);

        return [
            'move_number' => $moveCount + 1,
            'notation' => $randomMove,
            'game_id' => $gameId
        ];
    }
}
