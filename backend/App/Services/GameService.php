<?php

declare(strict_types=1);

namespace App\Services;

use InvalidArgumentException;

final class GameService extends BaseService
{
    public function create(array $data): array
    {
        $this->validateGameData($data, true);
        return $this->dao->create($data);
    }

    public function update(int $id, array $data): ?array
    {
        $this->validateGameData($data, false);
        return $this->dao->update($id, $data);
    }

    private function validateGameData(array $data, bool $isCreate): void
    {
        // White player validation
        if ($isCreate && (!isset($data['white_player_id']) || empty($data['white_player_id']))) {
            throw new InvalidArgumentException('White player is required');
        }

        // Black player validation
        if ($isCreate && (!isset($data['black_player_id']) || empty($data['black_player_id']))) {
            throw new InvalidArgumentException('Black player is required');
        }

        // Ensure players are different
        if (isset($data['white_player_id']) && isset($data['black_player_id'])) {
            if ($data['white_player_id'] == $data['black_player_id']) {
                throw new InvalidArgumentException('White and black players must be different');
            }
        }

        // Result validation
        if (isset($data['result']) && !in_array($data['result'], ['white', 'black', 'draw', 'in_progress'], true)) {
            throw new InvalidArgumentException('Invalid result. Must be one of: white, black, draw, in_progress');
        }

        // PGN validation
        if (isset($data['pgn']) && strlen($data['pgn']) > 65535) {
            throw new InvalidArgumentException('PGN data is too large');
        }
    }

    public function getGameWithMoves(int $gameId): ?array
    {
        $game = $this->dao->findById($gameId);
        
        if ($game === null) {
            return null;
        }
        
        // Get moves for this game
        $stmt = $this->dao->db->prepare("
            SELECT * FROM moves 
            WHERE game_id = :game_id 
            ORDER BY move_number ASC
        ");
        $stmt->execute(['game_id' => $gameId]);
        $game['moves'] = $stmt->fetchAll();
        
        // Get player information
        $stmt = $this->dao->db->prepare("
            SELECT id, username, rating FROM users WHERE id IN (:white, :black)
        ");
        $stmt->execute([
            'white' => $game['white_player_id'],
            'black' => $game['black_player_id']
        ]);
        $players = $stmt->fetchAll();
        
        foreach ($players as $player) {
            if ($player['id'] == $game['white_player_id']) {
                $game['white_player'] = $player;
            } else {
                $game['black_player'] = $player;
            }
        }
        
        return $game;
    }

    public function getGamesByTournament(int $tournamentId): array
    {
        return $this->dao->findAll(['tournament_id' => $tournamentId]);
    }
}
