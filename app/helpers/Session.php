<?php
/**
 * Session Helper
 */

class Session {
    /**
     * Set flash message
     */
    public static function flash($key, $value = null) {
        if ($value === null) {
            // Get flash message
            $message = $_SESSION['flash'][$key] ?? null;
            unset($_SESSION['flash'][$key]);
            return $message;
        } else {
            // Set flash message
            $_SESSION['flash'][$key] = $value;
        }
    }
    
    /**
     * Set session data
     */
    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }
    
    /**
     * Get session data
     */
    public static function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }
    
    /**
     * Check if session key exists
     */
    public static function has($key) {
        return isset($_SESSION[$key]);
    }
    
    /**
     * Remove session data
     */
    public static function remove($key) {
        unset($_SESSION[$key]);
    }
    
    /**
     * Generate CSRF token
     */
    public static function generateToken() {
        if (!self::has(CSRF_TOKEN_NAME)) {
            self::set(CSRF_TOKEN_NAME, bin2hex(random_bytes(32)));
        }
        return self::get(CSRF_TOKEN_NAME);
    }
    
    /**
     * Verify CSRF token
     */
    public static function verifyToken($token) {
        return hash_equals(self::get(CSRF_TOKEN_NAME), $token);
    }
    
    /**
     * CSRF token field for forms
     */
    public static function csrfField() {
        $token = self::generateToken();
        return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . $token . '">';
    }
    
    /**
     * Set success message
     */
    public static function success($message) {
        self::flash('success', $message);
    }
    
    /**
     * Set error message
     */
    public static function error($message) {
        self::flash('error', $message);
    }
    
    /**
     * Set warning message
     */
    public static function warning($message) {
        self::flash('warning', $message);
    }
    
    /**
     * Set info message
     */
    public static function info($message) {
        self::flash('info', $message);
    }
}
