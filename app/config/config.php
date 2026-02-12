<?php
/**
 * Application Configuration
 */

// Load environment variables
if (file_exists(BASE_PATH . '/.env')) {
    $lines = file(BASE_PATH . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        if (!array_key_exists($name, $_ENV)) {
            putenv("$name=$value");
            $_ENV[$name] = $value;
        }
    }
}

// Helper function to get environment variables
function env($key, $default = null) {
    $value = getenv($key);
    return $value !== false ? $value : $default;
}

// Application settings
define('APP_NAME', env('APP_NAME', 'Expense Tracking'));
define('APP_ENV', env('APP_ENV', 'development'));
define('APP_DEBUG', env('APP_DEBUG', 'true') === 'true');
define('APP_URL', env('APP_URL', 'http://localhost:8080'));

// Database settings (imported from database.php)
define('DB_HOST', env('DB_HOST', 'mysql'));
define('DB_PORT', env('DB_PORT', '3306'));
define('DB_NAME', env('DB_NAME', 'expense_tracking'));
define('DB_USER', env('DB_USER', 'expense_user'));
define('DB_PASSWORD', env('DB_PASSWORD', 'your_secure_password'));

// Session settings
define('SESSION_LIFETIME', env('SESSION_LIFETIME', 7200));
define('SESSION_NAME', env('SESSION_NAME', 'expense_tracking_session'));

// File upload settings
define('MAX_FILE_SIZE', env('MAX_FILE_SIZE', 5242880)); // 5MB
define('ALLOWED_FILE_TYPES', env('ALLOWED_FILE_TYPES', 'jpg,jpeg,png,pdf'));
define('UPLOAD_PATH', BASE_PATH . '/public/assets/images/uploads/receipts/');

// Timezone
date_default_timezone_set(env('TIMEZONE', 'Asia/Bangkok'));

// Error reporting
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
session_name(SESSION_NAME);

// Security settings
define('CSRF_TOKEN_NAME', 'csrf_token');
define('PASSWORD_HASH_ALGO', PASSWORD_BCRYPT);
define('PASSWORD_HASH_COST', 12);
