<?php

declare(strict_types=1);

namespace Rest\Middleware;

final class ValidationMiddleware
{
    /**
     * @param array<string, callable> $rules
     */
    public static function handle(array $rules): void
    {
        $data = \Flight::request()->data->getData();

        foreach ($rules as $field => $validator) {
            if (!isset($data[$field])) {
                \Flight::json(['error' => "Validation failed: Field '{$field}' is required"], 400);
                \Flight::stop();
                return;
            }

            $result = $validator($data[$field]);
            if ($result !== true) {
                \Flight::json(['error' => "Validation failed: {$result}"], 400);
                \Flight::stop();
                return;
            }
        }
    }
}

