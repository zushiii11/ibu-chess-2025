<?php

declare(strict_types=1);

namespace Rest\Middleware;

final class MiddlewareHelper
{
    /**
     * Apply middleware to a route handler
     * @param callable[] $middlewares Array of middleware callables
     */
    public static function apply(array $middlewares, callable $handler): callable
    {
        return function (...$args) use ($middlewares, $handler) {
            // Execute all middleware first
            foreach ($middlewares as $middleware) {
                if (is_callable($middleware)) {
                    $middleware();
                }
            }
            // Then execute the handler
            return $handler(...$args);
        };
    }
}

