<?php
// Iniciar buffer de salida para capturar cualquier salida antes de los headers
ob_start();

session_start();

// Autoloader simple para las clases
spl_autoload_register(function ($class) {
    // Solo cargar clases del namespace App
    if (strpos($class, 'App\\') === 0) {
        $file = str_replace('\\', '/', $class) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
});

// Cargar configuración
require_once 'config.php';

// Configurar las clases de configuración después del autoloader
App\Config\App::setConfig([
    'APP_SECRET_KEY' => APP_SECRET_KEY,
    'APP_ENV' => APP_ENV,
    'JWT_SECRET_KEY' => JWT_SECRET_KEY,
    'TOKEN_EXPIRY_HOURS' => TOKEN_EXPIRY_HOURS
]);

App\Config\Database::setConfig([
    'DB_HOST' => DB_HOST,
    'DB_PORT' => DB_PORT,
    'DB_USER' => DB_USER,
    'DB_PASSWORD' => DB_PASSWORD,
    'DB_NAME' => DB_NAME
]);

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