<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once 'config.php';
require_once 'src/Database.php';
require_once 'src/Auth.php';

$auth = new Auth($config);
$response = ['success' => false, 'message' => 'Acción no válida'];

try {
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    
    switch ($action) {
        case 'login':
            $username = $_POST['username'] ?? '';
            $token = $_POST['token'] ?? '';
            
            if (empty($username) || empty($token)) {
                $response = ['success' => false, 'message' => 'Usuario y token son requeridos'];
                break;
            }
            
            $authResult = $auth->authenticate($username, $token);
            if ($authResult['success']) {
                $auth->createSession($username, $authResult['expires']);
                $response = [
                    'success' => true,
                    'message' => 'Autenticación exitosa',
                    'username' => $username
                ];
            } else {
                $response = $authResult;
            }
            break;
            
        case 'logout':
            $auth->destroySession();
            $response = ['success' => true, 'message' => 'Sesión cerrada'];
            break;
            
        case 'create_token':
            if (!$auth->validateSession()) {
                $response = ['success' => false, 'message' => 'No autorizado'];
                break;
            }
            
            $username = $_POST['username'] ?? '';
            if (empty($username)) {
                $response = ['success' => false, 'message' => 'Usuario requerido'];
                break;
            }
            
            $token = $auth->createToken($username);
            $response = [
                'success' => true,
                'message' => 'Token creado exitosamente',
                'token' => $token
            ];
            break;
            
        case 'execute_query':
            if (!$auth->validateSession()) {
                $response = ['success' => false, 'message' => 'No autorizado'];
                break;
            }
            
            $sql = $_POST['sql'] ?? '';
            if (empty($sql)) {
                $response = ['success' => false, 'message' => 'Consulta SQL requerida'];
                break;
            }
            
            $db = new Database($config);
            $result = $db->query($sql);
            
            $response = [
                'success' => true,
                'data' => $result,
                'message' => 'Consulta ejecutada correctamente'
            ];
            break;
            
        case 'get_tables':
            if (!$auth->validateSession()) {
                $response = ['success' => false, 'message' => 'No autorizado'];
                break;
            }
            
            $db = new Database($config);
            $tables = $db->getTables();
            
            $response = [
                'success' => true,
                'tables' => $tables
            ];
            break;
            
        case 'get_table_structure':
            if (!$auth->validateSession()) {
                $response = ['success' => false, 'message' => 'No autorizado'];
                break;
            }
            
            $tableName = $_POST['table'] ?? '';
            if (empty($tableName)) {
                $response = ['success' => false, 'message' => 'Nombre de tabla requerido'];
                break;
            }
            
            $db = new Database($config);
            $structure = $db->getTableStructure($tableName);
            
            $response = [
                'success' => true,
                'structure' => $structure['data']
            ];
            break;
            
        default:
            $response = ['success' => false, 'message' => 'Acción no reconocida'];
    }
    
} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}

echo json_encode($response);
?> 