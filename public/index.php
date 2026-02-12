<?php
/**
 * Front Controller
 * Entry point for the application
 */

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Load configuration (this sets session ini settings)
require_once BASE_PATH . '/app/config/config.php';
require_once BASE_PATH . '/app/config/database.php';

// Start session AFTER configuration is loaded
session_start();

// Load helpers
require_once BASE_PATH . '/app/helpers/Auth.php';
require_once BASE_PATH . '/app/helpers/Validator.php';
require_once BASE_PATH . '/app/helpers/Session.php';

// Simple routing
$request = $_SERVER['REQUEST_URI'];
$request = parse_url($request, PHP_URL_PATH); // Strip query string
$request = str_replace('/index.php', '', $request);
$request = trim($request, '/');

// Parse URL
$url = explode('/', $request);
$controller = !empty($url[0]) ? ucfirst($url[0]) . 'Controller' : 'DashboardController';
$method = isset($url[1]) && !empty($url[1]) ? $url[1] : 'index';
$params = array_slice($url, 2);

// Route to appropriate controller
$controllerFile = BASE_PATH . '/app/controllers/' . $controller . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;

    if (class_exists($controller)) {
        $controllerInstance = new $controller();

        if (method_exists($controllerInstance, $method)) {
            call_user_func_array([$controllerInstance, $method], $params);
        } else {
            // Method not found
            http_response_code(404);
            echo "404 - Method not found";
        }
    } else {
        // Class not found
        http_response_code(404);
        echo "404 - Controller class not found";
    }
} else {
    // Check if this is an auth route
    if ($url[0] === 'auth' || $url[0] === 'login' || $url[0] === 'register') {
        require_once BASE_PATH . '/app/controllers/AuthController.php';
        $authController = new AuthController();

        if ($url[0] === 'login' || ($url[0] === 'auth' && $method === 'login')) {
            $authController->login();
        } elseif ($url[0] === 'register' || ($url[0] === 'auth' && $method === 'register')) {
            $authController->register();
        } else {
            $authController->index();
        }
    } else {
        // Controller not found - redirect to dashboard
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        require_once BASE_PATH . '/app/controllers/DashboardController.php';
        $dashboardController = new DashboardController();
        $dashboardController->index();
    }
}
