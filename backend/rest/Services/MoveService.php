<?php

declare(strict_types=1);

namespace Rest\Services;

use InvalidArgumentException;

final class MoveService extends BaseService
{
    public function create(array $data): array
    {
        $this->validateMoveData($data, true);
        return $this->dao->create($data);
    }

    public function update(int $id, array $data): ?array
    {
        $this->validateMoveData($data, false);
        return $this->dao->update($id, $data);
    }

    private function validateMoveData(array $data, bool $isCreate): void
    {
        // Game ID validation
        if ($isCreate && (!isset($data['game_id']) || empty($data['game_id']))) {
            throw new InvalidArgumentException('Game ID is required');
        }

        // Move number validation
        if ($isCreate && (!isset($data['move_number']) || $data['move_number'] < 1)) {
            throw new InvalidArgumentException('Move number is required and must be positive');
        }
        
        if (isset($data['move_number']) && $data['move_number'] < 1) {
            throw new InvalidArgumentException('Move number must be positive');
        }

        // Notation validation
        if ($isCreate && (!isset($data['notation']) || empty(trim($data['notation'])))) {
            throw new InvalidArgumentException('Move notation is required');
        }
        
        if (isset($data['notation'])) {
            if (strlen($data['notation']) > 15) {
                throw new InvalidArgumentException('Move notation must not exceed 15 characters');
            }
            // Basic chess notation validation
            if (!preg_match('/^[KQRBN]?[a-h]?[1-8]?x?[a-h][1-8](=[QRBN])?[+#]?$|^O-O(-O)?[+#]?$/i', $data['notation'])) {
                throw new InvalidArgumentException('Invalid chess notation format');
            }
        }

        // Time spent validation
        if (isset($data['time_spent_seconds']) && $data['time_spent_seconds'] < 0) {
            throw new InvalidArgumentException('Time spent cannot be negative');
        }
    }

    public function getMovesByGame(int $gameId): array
    {
        return $this->dao->findAll(['game_id' => $gameId, 'order_by' => 'move_number', 'order_dir' => 'ASC']);
    }
}

