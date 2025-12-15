<?php

declare(strict_types=1);

namespace Rest\Services;

use InvalidArgumentException;

final class TournamentParticipantService extends BaseService
{
    public function create(array $data): array
    {
        $this->validateParticipantData($data, true);
        return $this->dao->create($data);
    }

    public function update(int $id, array $data): ?array
    {
        $this->validateParticipantData($data, false);
        return $this->dao->update($id, $data);
    }

    private function validateParticipantData(array $data, bool $isCreate): void
    {
        // Tournament ID validation
        if ($isCreate && (!isset($data['tournament_id']) || empty($data['tournament_id']))) {
            throw new InvalidArgumentException('Tournament ID is required');
        }

        // User ID validation
        if ($isCreate && (!isset($data['user_id']) || empty($data['user_id']))) {
            throw new InvalidArgumentException('User ID is required');
        }

        // Seed validation
        if (isset($data['seed']) && $data['seed'] < 1) {
            throw new InvalidArgumentException('Seed must be a positive integer');
        }

        // Status validation
        if (isset($data['status']) && !in_array($data['status'], ['registered', 'checked_in', 'eliminated', 'winner'], true)) {
            throw new InvalidArgumentException('Invalid status. Must be one of: registered, checked_in, eliminated, winner');
        }
    }

    public function getParticipantsByTournament(int $tournamentId): array
    {
        return $this->dao->findAll(['tournament_id' => $tournamentId, 'order_by' => 'seed', 'order_dir' => 'ASC']);
    }

    public function getParticipantsByUser(int $userId): array
    {
        return $this->dao->findAll(['user_id' => $userId, 'order_by' => 'registered_at', 'order_dir' => 'DESC']);
    }
}

