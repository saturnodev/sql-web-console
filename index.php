<?php
session_start();

// Autoloader simple para las clases
spl_autoload_register(function ($class) {
    $file = str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Cargar configuración
require_once 'config.php';

// Obtener la acción solicitada
$action = $_GET['action'] ?? 'console';

// Verificar autenticación para acciones protegidas
$protectedActions = ['console', 'tables', 'tokens', 'profile'];
if (in_array($action, $protectedActions)) {
    $authModel = new App\Models\AuthModel();
    if (!$authModel->validateSession()) {
        header('Location: index.php?action=login');
        exit;
    }
}

// Router simple
switch ($action) {
    case 'login':
        $content = include 'app/Views/login.php';
        break;
    case 'console':
        $content = include 'app/Views/console.php';
        break;
    case 'tables':
        $content = include 'app/Views/tables.php';
        break;
    case 'tokens':
        $content = include 'app/Views/tokens.php';
        break;
    case 'profile':
        $content = include 'app/Views/profile.php';
        break;
    default:
        $content = include 'app/Views/console.php';
}

// Cargar el layout principal
include 'app/Views/layout.php';
?> 