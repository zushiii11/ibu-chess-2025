<?php

declare(strict_types=1);

namespace Rest\Middleware;

final class LoggingMiddleware
{
    public static function handle(): void
    {
        $request = \Flight::request();
        $method = $request->method;
        $url = $request->url;
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $timestamp = date('Y-m-d H:i:s');

        $logMessage = sprintf(
            "[%s] %s %s from %s\n",
            $timestamp,
            $method,
            $url,
            $ip
        );

        // Log to file (optional - can be disabled in production)
        $logFile = __DIR__ . '/../../logs/api.log';
        $logDir = dirname($logFile);
        
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        @file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
}

