<?php
/**
 * Setup Diagnostic Script
 * Run this to check if everything is configured correctly
 */

echo "========================================\n";
echo "IBU Chess - Setup Diagnostic\n";
echo "========================================\n\n";

// Check PHP version
echo "1. PHP Version: ";
$phpVersion = phpversion();
echo $phpVersion . "\n";
if (version_compare($phpVersion, '8.2.0', '<')) {
    echo "   ⚠️  WARNING: PHP 8.2+ required. You have: $phpVersion\n";
} else {
    echo "   ✅ PHP version OK\n";
}
echo "\n";

// Check required extensions
echo "2. Required Extensions:\n";
$required = ['pdo', 'pdo_mysql', 'json'];
foreach ($required as $ext) {
    if (extension_loaded($ext)) {
        echo "   ✅ $ext\n";
    } else {
        echo "   ❌ $ext - MISSING!\n";
    }
}
echo "\n";

// Check if vendor directory exists
echo "3. Dependencies:\n";
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo "   ✅ Composer dependencies installed\n";
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    echo "   ❌ Composer dependencies NOT installed\n";
    echo "   Run: composer install\n";
}
echo "\n";

// Check if .env file exists
echo "4. Configuration:\n";
$envPath = __DIR__ . '/backend/.env';
if (file_exists($envPath)) {
    echo "   ✅ .env file exists\n";
    $env = parse_ini_file($envPath);
    $requiredEnv = ['DB_HOST', 'DB_NAME', 'DB_USER', 'JWT_SECRET'];
    foreach ($requiredEnv as $key) {
        if (isset($env[$key]) && !empty($env[$key])) {
            echo "   ✅ $key is set\n";
        } else {
            echo "   ⚠️  $key is missing or empty\n";
        }
    }
} else {
    echo "   ⚠️  .env file NOT found at: $envPath\n";
    echo "   Create backend/.env file with database configuration\n";
}
echo "\n";

// Check database connection
echo "5. Database Connection:\n";
if (file_exists($envPath)) {
    try {
        require_once __DIR__ . '/backend/bootstrap.php';
        $pdo = \App\Config\Database::connection();
        echo "   ✅ Database connection successful\n";
        
        // Check if tables exist
        $tables = ['users', 'tournaments', 'games', 'moves', 'tournament_participants', 'reviews'];
        $stmt = $pdo->query("SHOW TABLES");
        $existingTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($tables as $table) {
            if (in_array($table, $existingTables)) {
                echo "   ✅ Table '$table' exists\n";
            } else {
                echo "   ❌ Table '$table' MISSING - Run schema.sql\n";
            }
        }
    } catch (Exception $e) {
        echo "   ❌ Database connection failed: " . $e->getMessage() . "\n";
    }
} else {
    echo "   ⚠️  Cannot test database - .env file missing\n";
}
echo "\n";

// Check file structure
echo "6. File Structure:\n";
$requiredDirs = [
    'backend/App/Config',
    'backend/rest/Dao',
    'backend/rest/Services',
    'backend/rest/Middleware',
    'backend/rest/routes',
    'backend/public',
    'frontend/js',
    'frontend/views'
];

foreach ($requiredDirs as $dir) {
    if (is_dir(__DIR__ . '/' . $dir)) {
        echo "   ✅ $dir\n";
    } else {
        echo "   ❌ $dir - MISSING!\n";
    }
}
echo "\n";

// Check key files
echo "7. Key Files:\n";
$requiredFiles = [
    'backend/bootstrap.php',
    'backend/public/index.php',
    'backend/rest/Services/AuthService.php',
    'backend/rest/Middleware/AuthMiddleware.php',
    'backend/rest/routes/AuthRoutes.php',
    'frontend/index.html',
    'frontend/js/api.js',
    'frontend/js/auth.js'
];

foreach ($requiredFiles as $file) {
    if (file_exists(__DIR__ . '/' . $file)) {
        echo "   ✅ $file\n";
    } else {
        echo "   ❌ $file - MISSING!\n";
    }
}
echo "\n";

// Check if Firebase JWT is installed
echo "8. JWT Library:\n";
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
    if (class_exists('Firebase\JWT\JWT')) {
        echo "   ✅ Firebase JWT library installed\n";
    } else {
        echo "   ❌ Firebase JWT library NOT found\n";
        echo "   Run: composer require firebase/php-jwt\n";
    }
} else {
    echo "   ⚠️  Cannot check - dependencies not installed\n";
}
echo "\n";

echo "========================================\n";
echo "Diagnostic Complete\n";
echo "========================================\n";
echo "\n";
echo "Next Steps:\n";
echo "1. Fix any issues shown above\n";
echo "2. Start backend: php -S localhost:8000 -t backend/public\n";
echo "3. Start frontend: php -S localhost:8080 -t frontend\n";
echo "4. Open browser: http://localhost:8080\n";

