<?php

declare(strict_types=1);

use App\Config\Environment;

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';

    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $relativePath = str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';

    $baseDir = __DIR__ . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR;
    $candidatePaths = [
        $baseDir . $relativePath,
        $baseDir . lcfirst($relativePath),
    ];

    foreach ($candidatePaths as $path) {
        if (is_file($path)) {
            require_once $path;
            return;
        }
    }
});

// Load .env style configuration if available.
if (is_file(__DIR__ . '/.env')) {
    Environment::bootstrap(__DIR__ . '/.env');
}
