<?php

declare(strict_types=1);

namespace App\Services;

use InvalidArgumentException;
use DateTime;

final class TournamentService extends BaseService
{
    public function create(array $data): array
    {
        $this->validateTournamentData($data, true);
        return $this->dao->create($data);
    }

    public function update(int $id, array $data): ?array
    {
        $this->validateTournamentData($data, false);
        return $this->dao->update($id, $data);
    }

    private function validateTournamentData(array $data, bool $isCreate): void
    {
        // Name validation
        if ($isCreate && (!isset($data['name']) || empty(trim($data['name'])))) {
            throw new InvalidArgumentException('Tournament name is required');
        }
        
        if (isset($data['name'])) {
            if (strlen($data['name']) < 3) {
                throw new InvalidArgumentException('Tournament name must be at least 3 characters long');
            }
            if (strlen($data['name']) > 120) {
                throw new InvalidArgumentException('Tournament name must not exceed 120 characters');
            }
        }

        // Location validation
        if (isset($data['location']) && strlen($data['location']) > 120) {
            throw new InvalidArgumentException('Location must not exceed 120 characters');
        }

        // Date validation
        if (isset($data['start_date']) && isset($data['end_date'])) {
            $startDate = new DateTime($data['start_date']);
            $endDate = new DateTime($data['end_date']);
            
            if ($endDate < $startDate) {
                throw new InvalidArgumentException('End date must be after start date');
            }
        }

        // Prize pool validation
        if (isset($data['prize_pool'])) {
            $prizePool = (float) $data['prize_pool'];
            if ($prizePool < 0) {
                throw new InvalidArgumentException('Prize pool cannot be negative');
            }
            if ($prizePool > 9999999.99) {
                throw new InvalidArgumentException('Prize pool exceeds maximum allowed value');
            }
        }
    }

    public function getTournamentWithParticipants(int $tournamentId): ?array
    {
        $tournament = $this->dao->findById($tournamentId);
        
        if ($tournament === null) {
            return null;
        }
        
        // Get participants for this tournament
        $stmt = $this->dao->db->prepare("
            SELECT tp.*, u.username, u.email, u.rating 
            FROM tournament_participants tp
            JOIN users u ON tp.user_id = u.id
            WHERE tp.tournament_id = :tournament_id
            ORDER BY tp.seed ASC
        ");
        $stmt->execute(['tournament_id' => $tournamentId]);
        $tournament['participants'] = $stmt->fetchAll();
        
        return $tournament;
    }
}
