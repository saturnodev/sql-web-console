<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Autoloader
spl_autoload_register(function ($class) {
    $file = str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Cargar configuración
require_once 'config.php';

// Instanciar controladores
$authController = new App\Controllers\AuthController();
$databaseController = new App\Controllers\DatabaseController();

$response = ['success' => false, 'message' => 'Acción no válida'];

try {
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    
    switch ($action) {
        case 'login':
            $response = $authController->login();
            break;
            
        case 'logout':
            $response = $authController->logout();
            break;
            
        case 'check_auth':
            $response = [
                'success' => $authController->isAuthenticated(),
                'username' => $authController->getCurrentUser()
            ];
            break;
            
        case 'create_token':
            $response = $authController->createToken();
            break;
            
        case 'get_tokens':
            $response = $authController->getTokens();
            break;
            
        case 'revoke_token':
            $response = $authController->revokeToken();
            break;
            
        case 'execute_query':
            $response = $databaseController->executeQuery();
            break;
            
        case 'get_tables':
            $response = $databaseController->getTables();
            break;
            
        case 'get_table_structure':
            $response = $databaseController->getTableStructure();
            break;
            
        case 'test_connection':
            $response = $databaseController->testConnection();
            break;
            
        default:
            $response = ['success' => false, 'message' => 'Acción no reconocida'];
    }
    
} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}

echo json_encode($response);
?> 