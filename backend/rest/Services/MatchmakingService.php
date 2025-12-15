<?php

declare(strict_types=1);

namespace Rest\Services;

use InvalidArgumentException;

final class MatchmakingService extends BaseService
{
    public function joinQueue(int $userId, string $timeControl, int $userRating): array
    {
        // Check if user is already in queue
        $existing = $this->dao->findAll(['user_id' => $userId, 'status' => 'waiting']);
        if (!empty($existing)) {
            throw new InvalidArgumentException('You are already in the matchmaking queue');
        }

        // Calculate rating range (±200)
        $ratingMin = max(0, $userRating - 200);
        $ratingMax = $userRating + 200;

        $data = [
            'user_id' => $userId,
            'time_control' => $timeControl,
            'rating_min' => $ratingMin,
            'rating_max' => $ratingMax,
            'status' => 'waiting'
        ];

        $queue = $this->dao->create($data);

        // Try to find a match
        $match = $this->findMatch($userId, $timeControl, $ratingMin, $ratingMax);
        
        if ($match !== null) {
            return $this->createMatch($queue['id'], $match['id']);
        }

        return $queue;
    }

    private function findMatch(int $userId, string $timeControl, int $ratingMin, int $ratingMax): ?array
    {
        $sql = "
            SELECT * FROM matchmaking_queue 
            WHERE user_id != :user_id 
            AND time_control = :time_control
            AND status = 'waiting'
            AND rating_min <= :rating_max
            AND rating_max >= :rating_min
            ORDER BY created_at ASC
            LIMIT 1
        ";

        $stmt = $this->dao->db->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'time_control' => $timeControl,
            'rating_min' => $ratingMin,
            'rating_max' => $ratingMax
        ]);

        $result = $stmt->fetch();
        return $result ?: null;
    }

    private function createMatch(int $queueId1, int $queueId2): array
    {
        $queue1 = $this->dao->findById($queueId1);
        $queue2 = $this->dao->findById($queueId2);

        // Randomly assign colors
        $whitePlayerId = rand(0, 1) === 0 ? $queue1['user_id'] : $queue2['user_id'];
        $blackPlayerId = $whitePlayerId === $queue1['user_id'] ? $queue2['user_id'] : $queue1['user_id'];

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

        // Update both queue entries
        $this->dao->update($queueId1, ['status' => 'matched', 'game_id' => $gameId]);
        $this->dao->update($queueId2, ['status' => 'matched', 'game_id' => $gameId]);

        return [
            'game_id' => $gameId,
            'white_player_id' => $whitePlayerId,
            'black_player_id' => $blackPlayerId,
            'status' => 'matched'
        ];
    }

    public function getStatus(int $userId): ?array
    {
        $queue = $this->dao->findAll(['user_id' => $userId, 'status' => ['waiting', 'matched']]);
        
        if (empty($queue)) {
            return null;
        }

        $entry = $queue[0];
        
        if ($entry['status'] === 'matched') {
            // Get opponent info
            $stmt = $this->dao->db->prepare("
                SELECT u.id, u.username, u.rating 
                FROM games g
                JOIN users u ON (u.id = g.white_player_id OR u.id = g.black_player_id)
                WHERE g.id = :game_id AND u.id != :user_id
            ");
            $stmt->execute([
                'game_id' => $entry['game_id'],
                'user_id' => $userId
            ]);
            $opponent = $stmt->fetch();

            return [
                'status' => 'matched',
                'game_id' => $entry['game_id'],
                'opponent' => $opponent
            ];
        }

        return [
            'status' => 'waiting',
            'queue_id' => $entry['id']
        ];
    }

    public function cancelQueue(int $userId): bool
    {
        $queue = $this->dao->findAll(['user_id' => $userId, 'status' => 'waiting']);
        
        if (empty($queue)) {
            return false;
        }

        $this->dao->update($queue[0]['id'], ['status' => 'cancelled']);
        return true;
    }
}
