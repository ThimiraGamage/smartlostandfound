<?php
/**
 * Simple .env File Loader
 * Loads environment variables from .env file
 */

function loadEnv($filePath) {
    if (!file_exists($filePath)) {
        return false;
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Parse line
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remove quotes if present
            if (in_array($value[0], ['"', "'"])) {
                $value = substr($value, 1, -1);
            }
            
            // Set as environment variable
            putenv("{$key}={$value}");
            $_ENV[$key] = $value;
        }
    }
    
    return true;
}

// Load .env file from project root
$envFile = __DIR__ . '/../.env';
loadEnv($envFile);
?>
