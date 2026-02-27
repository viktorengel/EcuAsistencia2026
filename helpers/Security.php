<?php
class Security {
    public static function generateToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validateToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function sanitize($data) {
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }

    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    public static function requireLogin() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/?action=login');
            exit;
        }
    }

    public static function hasRole($roleNames) {
        // Superusuario siempre tiene acceso
        if (!empty($_SESSION['is_superadmin'])) return true;
        if (!isset($_SESSION['roles'])) return false;
        $roleNames = (array) $roleNames;
        return count(array_intersect($roleNames, $_SESSION['roles'])) > 0;
    }

    public static function isSuperAdmin() {
        return !empty($_SESSION['is_superadmin']);
    }

    public static function startSession($rememberMe = false) {
        if ($rememberMe) {
            // Sesión de 7 días
            $lifetime = 7 * 24 * 60 * 60; // 7 días
        } else {
            // Sesión expira al cerrar navegador
            $lifetime = 0;
        }
        
        session_set_cookie_params([
            'lifetime' => $lifetime,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
        
        session_start();
        
        // Regenerar ID por seguridad
        if (!isset($_SESSION['initiated'])) {
            session_regenerate_id(true);
            $_SESSION['initiated'] = true;
        }
        
        // Timeout de inactividad (30 minutos)
        if (isset($_SESSION['last_activity']) && 
            (time() - $_SESSION['last_activity'] > 1800)) {
            session_unset();
            session_destroy();
            return false;
        }
        
        $_SESSION['last_activity'] = time();
        return true;
    }
}