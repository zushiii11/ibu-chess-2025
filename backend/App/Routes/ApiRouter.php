<?php

declare(strict_types=1);

namespace App\Routes;

use App\Services\BaseService;
use InvalidArgumentException;
use Throwable;

final class ApiRouter
{
    /** @var array<string, BaseService> */
    private array $resources = [];

    public function register(string $resource, BaseService $service): void
    {
        $this->resources[$resource] = $service;
    }

    public function handle(string $method, string $uri, array $queryParameters = []): void
    {
        $this->applyCorsHeaders();

        if ($method === 'OPTIONS') {
            http_response_code(204);
            return;
        }

        $path = parse_url($uri, PHP_URL_PATH) ?? '/';
        $segments = array_values(array_filter(explode('/', trim($path, '/'))));

        if (empty($segments) || $segments[0] !== 'api') {
            $this->respond(200, [
                'message' => 'Welcome to the IBU Chess API',
                'resources' => array_keys($this->resources),
            ]);
            return;
        }

        array_shift($segments);

        if (empty($segments)) {
            $this->respond(200, [
                'resources' => array_keys($this->resources),
            ]);
            return;
        }

        $resourceName = $segments[0];

        if (!isset($this->resources[$resourceName])) {
            $this->respond(404, ['error' => 'Resource not found']);
            return;
        }

        $service = $this->resources[$resourceName];

        $id = null;

        if (isset($segments[1]) && $segments[1] !== '') {
            $id = (int) $segments[1];
        }

        try {
            $handler = match (strtoupper($method)) {
                'GET' => fn () => $this->handleGet($service, $id, $queryParameters),
                'POST' => fn () => $this->handlePost($service),
                'PUT', 'PATCH' => fn () => $this->handlePut($service, $id),
                'DELETE' => fn () => $this->handleDelete($service, $id),
                default => null,
            };

            if ($handler === null) {
                $this->respond(405, ['error' => 'Method not allowed']);
                return;
            }

            $handler();
        } catch (InvalidArgumentException $exception) {
            $this->respond(400, ['error' => $exception->getMessage()]);
        } catch (Throwable $exception) {
            $this->respond(500, ['error' => 'Unexpected server error', 'details' => $exception->getMessage()]);
        }
    }

    private function handleGet(BaseService $service, ?int $id, array $queryParameters): void
    {
        if ($id === null) {
            $this->respond(200, $service->getAll($queryParameters));
            return;
        }

        $record = $service->getById($id);

        if ($record === null) {
            $this->respond(404, ['error' => 'Record not found']);
            return;
        }

        $this->respond(200, $record);
    }

    private function handlePost(BaseService $service): void
    {
        $payload = $this->parseJsonBody();
        $record = $service->create($payload);
        $this->respond(201, $record);
    }

    private function handlePut(BaseService $service, ?int $id): void
    {
        if ($id === null) {
            throw new InvalidArgumentException('Resource identifier is required');
        }

        $payload = $this->parseJsonBody();
        $record = $service->update($id, $payload);

        if ($record === null) {
            $this->respond(404, ['error' => 'Record not found']);
            return;
        }

        $this->respond(200, $record);
    }

    private function handleDelete(BaseService $service, ?int $id): void
    {
        if ($id === null) {
            throw new InvalidArgumentException('Resource identifier is required');
        }

        $deleted = $service->delete($id);

        if (!$deleted) {
            $this->respond(404, ['error' => 'Record not found']);
            return;
        }

        http_response_code(204);
    }

    private function parseJsonBody(): array
    {
        $rawBody = file_get_contents('php://input');

        if ($rawBody === false) {
            throw new InvalidArgumentException('Unable to read request body');
        }

        if ($rawBody === '' || trim($rawBody) === '') {
            return [];
        }

        $decoded = json_decode($rawBody, true);

        if (!is_array($decoded)) {
            throw new InvalidArgumentException('Invalid JSON payload');
        }

        return $decoded;
    }

    private function respond(int $statusCode, $payload): void
    {
        http_response_code($statusCode);
        echo json_encode($payload);
    }

    private function applyCorsHeaders(): void
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
    }
}
