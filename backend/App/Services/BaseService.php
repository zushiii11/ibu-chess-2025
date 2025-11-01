<?php

declare(strict_types=1);

namespace App\Services;

use App\Dao\BaseDao;

abstract class BaseService
{
    protected BaseDao $dao;

    public function __construct(BaseDao $dao)
    {
        $this->dao = $dao;
    }

    public function getAll(array $filters = []): array
    {
        return $this->dao->findAll($filters);
    }

    public function getById(int $id): ?array
    {
        return $this->dao->findById($id);
    }

    public function create(array $data): array
    {
        return $this->dao->create($data);
    }

    public function update(int $id, array $data): ?array
    {
        return $this->dao->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->dao->delete($id);
    }
}
