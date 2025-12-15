<?php

declare(strict_types=1);

namespace Rest\Dao;

use InvalidArgumentException;
use PDO;
use RuntimeException;

abstract class BaseDao
{
    protected PDO $db;
    protected string $table;
    /** @var string[] */
    protected array $fillable;

    public function __construct(PDO $db, string $table, array $fillable)
    {
        $this->db = $db;
        $this->table = $table;
        $this->fillable = $fillable;
    }

    public function findAll(array $filters = []): array
    {
        $conditions = [];
        $params = [];
        $limit = null;
        $offset = null;
        $orderBy = null;
        $orderDir = 'ASC';

        foreach ($filters as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            if ($key === 'limit') {
                $limit = max((int) $value, 1);
                continue;
            }

            if ($key === 'offset') {
                $offset = max((int) $value, 0);
                continue;
            }

            if ($key === 'order_by' && $this->isSelectableColumn($value)) {
                $orderBy = $value;
                continue;
            }

            if ($key === 'order_dir' && in_array(strtoupper((string) $value), ['ASC', 'DESC'], true)) {
                $orderDir = strtoupper((string) $value);
                continue;
            }

            if ($this->isSelectableColumn($key)) {
                $conditions[] = sprintf('%s = :%s', $key, $key);
                $params[$key] = $value;
            }
        }

        $sql = sprintf('SELECT * FROM %s', $this->table);

        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        if ($orderBy !== null) {
            $sql .= sprintf(' ORDER BY %s %s', $orderBy, $orderDir);
        }

        if ($limit !== null) {
            $sql .= ' LIMIT :limit';
        }

        if ($offset !== null) {
            $sql .= ' OFFSET :offset';
        }

        $statement = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $statement->bindValue(':' . $key, $value);
        }

        if ($limit !== null) {
            $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        }

        if ($offset !== null) {
            $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        }

        $statement->execute();

        return $statement->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $statement = $this->db->prepare(sprintf('SELECT * FROM %s WHERE id = :id', $this->table));
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetch();

        return $result !== false ? $result : null;
    }

    public function create(array $data): array
    {
        $payload = $this->filterFillable($data);

        if (empty($payload)) {
            throw new InvalidArgumentException('Payload is empty or contains no allowed fields');
        }

        $columns = array_keys($payload);
        $placeholders = array_map(static fn ($column) => ':' . $column, $columns);

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $statement = $this->db->prepare($sql);

        foreach ($payload as $column => $value) {
            $statement->bindValue(':' . $column, $value);
        }

        $statement->execute();

        $id = (int) $this->db->lastInsertId();

        $record = $this->findById($id);

        if ($record === null) {
            throw new RuntimeException('Failed to fetch inserted record');
        }

        return $record;
    }

    public function update(int $id, array $data): ?array
    {
        $payload = $this->filterFillable($data);

        if (empty($payload)) {
            return $this->findById($id);
        }

        $assignments = [];

        foreach (array_keys($payload) as $column) {
            $assignments[] = sprintf('%s = :%s', $column, $column);
        }

        $sql = sprintf(
            'UPDATE %s SET %s WHERE id = :id',
            $this->table,
            implode(', ', $assignments)
        );

        $statement = $this->db->prepare($sql);

        $statement->bindValue(':id', $id, PDO::PARAM_INT);

        foreach ($payload as $column => $value) {
            $statement->bindValue(':' . $column, $value);
        }

        $statement->execute();

        return $this->findById($id);
    }

    public function delete(int $id): bool
    {
        $statement = $this->db->prepare(sprintf('DELETE FROM %s WHERE id = :id', $this->table));
        $statement->bindValue(':id', $id, PDO::PARAM_INT);

        $statement->execute();

        return $statement->rowCount() > 0;
    }

    protected function filterFillable(array $payload): array
    {
        $allowed = array_flip($this->fillable);

        return array_intersect_key($payload, $allowed);
    }

    protected function isSelectableColumn(string $column): bool
    {
        return $column === 'id' || in_array($column, $this->fillable, true);
    }
}

