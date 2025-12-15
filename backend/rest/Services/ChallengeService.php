<?php

declare(strict_types=1);

namespace Rest\Services;

use InvalidArgumentException;

final class ChallengeService extends BaseService
{
    public function create(array $data): array
    {
        $this->validateChallengeData($data);
        
        // Set default values
        $data['status'] = 'pending';
        $data['expires_at'] = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        return $this->dao->create($data);
    }

    public function update(int $id, array $data): ?array
    {
        return $this->dao->update($id, $data);
    }

    private function validateChallengeData(array $data): void
    {
        if (!isset($data['challenger_id']) || empty($data['challenger_id'])) {
            throw new InvalidArgumentException('Challenger ID is required');
        }

        if (!isset($data['challenged_id']) || empty($data['challenged_id'])) {
            throw new InvalidArgumentException('Challenged player ID is required');
        }

        if ($data['challenger_id'] == $data['challenged_id']) {
            throw new InvalidArgumentException('Cannot challenge yourself');
        }

        if (isset($data['time_control']) && !in_array($data['time_control'], ['bullet', 'blitz', 'rapid', 'classical'], true)) {
            throw new InvalidArgumentException('Invalid time control');
        }

        if (isset($data['challenger_color']) && !in_array($data['challenger_color'], ['white', 'black', 'random'], true)) {
            throw new InvalidArgumentException('Invalid color choice');
        }
    }

    public function getChallengesForUser(int $userId): array
    {
        $sent = $this->dao->findAll(['challenger_id' => $userId]);
        $received = $this->dao->findAll(['challenged_id' => $userId]);
        
        return [
            'sent' => $sent,
            'received' => $received
        ];
    }

    public function acceptChallenge(int $challengeId, int $userId): array
    {
        $challenge = $this->dao->findById($challengeId);
        
        if ($challenge === null) {
            throw new InvalidArgumentException('Challenge not found');
        }

        if ($challenge['challenged_id'] != $userId) {
            throw new InvalidArgumentException('You are not the challenged player');
        }

        if ($challenge['status'] !== 'pending') {
            throw new InvalidArgumentException('Challenge is no longer pending');
        }

        // Create the game
        $gameData = [
            'white_player_id' => $challenge['challenger_color'] === 'black' ? $challenge['challenged_id'] : $challenge['challenger_id'],
            'black_player_id' => $challenge['challenger_color'] === 'black' ? $challenge['challenger_id'] : $challenge['challenged_id'],
            'result' => 'in_progress'
        ];

        // If random, randomize
        if ($challenge['challenger_color'] === 'random') {
            if (rand(0, 1) === 1) {
                $temp = $gameData['white_player_id'];
                $gameData['white_player_id'] = $gameData['black_player_id'];
                $gameData['black_player_id'] = $temp;
            }
        }

        // Create game in database
        $stmt = $this->dao->db->prepare("
            INSERT INTO games (white_player_id, black_player_id, result, created_at)
            VALUES (:white, :black, :result, NOW())
        ");
        $stmt->execute([
            'white' => $gameData['white_player_id'],
            'black' => $gameData['black_player_id'],
            'result' => 'in_progress'
        ]);
        $gameId = (int) $this->dao->db->lastInsertId();

        // Update challenge
        $this->dao->update($challengeId, [
            'status' => 'accepted',
            'game_id' => $gameId
        ]);

        return [
            'challenge_id' => $challengeId,
            'game_id' => $gameId,
            'white_player_id' => $gameData['white_player_id'],
            'black_player_id' => $gameData['black_player_id']
        ];
    }

    public function declineChallenge(int $challengeId, int $userId): bool
    {
        $challenge = $this->dao->findById($challengeId);
        
        if ($challenge === null) {
            throw new InvalidArgumentException('Challenge not found');
        }

        if ($challenge['challenged_id'] != $userId) {
            throw new InvalidArgumentException('You are not the challenged player');
        }

        $this->dao->update($challengeId, ['status' => 'declined']);
        return true;
    }

    public function cancelChallenge(int $challengeId, int $userId): bool
    {
        $challenge = $this->dao->findById($challengeId);
        
        if ($challenge === null) {
            throw new InvalidArgumentException('Challenge not found');
        }

        if ($challenge['challenger_id'] != $userId) {
            throw new InvalidArgumentException('You are not the challenger');
        }

        $this->dao->update($challengeId, ['status' => 'cancelled']);
        return true;
    }
}
