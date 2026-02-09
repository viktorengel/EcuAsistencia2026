<?php
require_once '../config/config.php';

$action = $_GET['action'] ?? 'login';

switch ($action) {
    case 'register':
        require_once BASE_PATH . '/controllers/AuthController.php';
        $auth = new AuthController();
        $auth->register();
        break;
    
    case 'login':
        require_once BASE_PATH . '/controllers/AuthController.php';
        $auth = new AuthController();
        $auth->login();
        break;
    
    case 'logout':
        require_once BASE_PATH . '/controllers/AuthController.php';
        $auth = new AuthController();
        $auth->logout();
        break;
    
    case 'forgot':
        require_once BASE_PATH . '/controllers/AuthController.php';
        $auth = new AuthController();
        $auth->forgotPassword();
        break;
    
    case 'reset':
        require_once BASE_PATH . '/controllers/AuthController.php';
        $auth = new AuthController();
        $auth->resetPassword();
        break;
    
    case 'dashboard':
        Security::requireLogin();
        echo "<h1>Dashboard - Bienvenido " . $_SESSION['username'] . "</h1>";
        echo "<p>Roles: " . implode(', ', $_SESSION['roles']) . "</p>";
        echo "<a href='?action=logout'>Cerrar sesión</a>";
        break;
    
    default:
        header('Location: ?action=login');
}