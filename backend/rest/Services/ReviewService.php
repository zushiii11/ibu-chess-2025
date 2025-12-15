<?php

declare(strict_types=1);

namespace Rest\Services;

use InvalidArgumentException;

final class ReviewService extends BaseService
{
    public function create(array $data): array
    {
        $this->validateReviewData($data, true);
        return $this->dao->create($data);
    }

    public function update(int $id, array $data): ?array
    {
        $this->validateReviewData($data, false);
        return $this->dao->update($id, $data);
    }

    private function validateReviewData(array $data, bool $isCreate): void
    {
        // User ID validation
        if ($isCreate && (!isset($data['user_id']) || empty($data['user_id']))) {
            throw new InvalidArgumentException('User ID is required');
        }

        // Rating validation
        if ($isCreate && (!isset($data['rating']) || $data['rating'] < 1 || $data['rating'] > 5)) {
            throw new InvalidArgumentException('Rating is required and must be between 1 and 5');
        }
        
        if (isset($data['rating']) && ($data['rating'] < 1 || $data['rating'] > 5)) {
            throw new InvalidArgumentException('Rating must be between 1 and 5');
        }

        // Comment validation
        if (isset($data['comment']) && strlen($data['comment']) > 5000) {
            throw new InvalidArgumentException('Comment must not exceed 5000 characters');
        }
    }

    public function getReviewsByGame(int $gameId): array
    {
        return $this->dao->findAll(['game_id' => $gameId, 'order_by' => 'created_at', 'order_dir' => 'DESC']);
    }

    public function getReviewsByUser(int $userId): array
    {
        return $this->dao->findAll(['user_id' => $userId, 'order_by' => 'created_at', 'order_dir' => 'DESC']);
    }

    public function getAverageRatingByGame(int $gameId): ?float
    {
        $stmt = $this->dao->db->prepare("
            SELECT AVG(rating) as avg_rating 
            FROM reviews 
            WHERE game_id = :game_id
        ");
        $stmt->execute(['game_id' => $gameId]);
        $result = $stmt->fetch();
        
        return $result['avg_rating'] ? (float) $result['avg_rating'] : null;
    }
}

