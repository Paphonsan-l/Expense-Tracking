<?php
/**
 * Authentication Helper
 */

class Auth {
    /**
     * Check if user is logged in
     */
    public static function check() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    /**
     * Get current user ID
     */
    public static function id() {
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Get current user data
     */
    public static function user() {
        if (!self::check()) {
            return null;
        }
        
        require_once BASE_PATH . '/app/models/User.php';
        $userModel = new User();
        return $userModel->findById(self::id());
    }
    
    /**
     * Login user
     */
    public static function login($userId, $remember = false) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $userId;
        $_SESSION['logged_in_at'] = time();
        
        if ($remember) {
            // Set remember me cookie (30 days)
            $token = bin2hex(random_bytes(32));
            setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', false, true);
            // Store token in database (implement as needed)
        }
        
        return true;
    }
    
    /**
     * Logout user
     */
    public static function logout() {
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/', '', false, true);
        }
        
        $_SESSION = array();
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
        return true;
    }
    
    /**
     * Require authentication
     */
    public static function require() {
        if (!self::check()) {
            header('Location: /login');
            exit;
        }
    }
    
    /**
     * Redirect if authenticated
     */
    public static function guest() {
        if (self::check()) {
            header('Location: /dashboard');
            exit;
        }
    }
}
