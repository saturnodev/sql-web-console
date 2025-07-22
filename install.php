<?php
/**
 * Script de instalación para SQL Web Console
 * Ejecutar una sola vez para configurar la aplicación
 */

// Verificar si ya está instalado
if (file_exists('config.php') && !isset($_GET['force'])) {
    die('La aplicación ya está instalada. Si deseas reinstalar, agrega ?force=1 a la URL.');
}

$errors = [];
$success = [];

// Procesar formulario de instalación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbHost = $_POST['db_host'] ?? 'localhost';
    $dbPort = $_POST['db_port'] ?? '3306';
    $dbUser = $_POST['db_user'] ?? '';
    $dbPassword = $_POST['db_password'] ?? '';
    $dbName = $_POST['db_name'] ?? '';
    $appSecret = $_POST['app_secret'] ?? '';
    $jwtSecret = $_POST['jwt_secret'] ?? '';
    
    // Validaciones
    if (empty($dbUser)) $errors[] = 'Usuario de base de datos es requerido';
    if (empty($dbName)) $errors[] = 'Nombre de base de datos es requerido';
    if (empty($appSecret)) $errors[] = 'Clave secreta de aplicación es requerida';
    if (empty($jwtSecret)) $errors[] = 'Clave JWT es requerida';
    
    // Probar conexión a MySQL
    if (empty($errors)) {
        try {
            $dsn = "mysql:host={$dbHost};port={$dbPort};charset=utf8mb4";
            $pdo = new PDO($dsn, $dbUser, $dbPassword);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Verificar si la base de datos existe
            $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$dbName}'");
            if ($stmt->rowCount() === 0) {
                $errors[] = "La base de datos '{$dbName}' no existe";
            }
            
        } catch (PDOException $e) {
            $errors[] = 'Error de conexión a MySQL: ' . $e->getMessage();
        }
    }
    
    // Crear archivos si no hay errores
    if (empty($errors)) {
        try {
            // Crear directorio data
            if (!is_dir('data')) {
                mkdir('data', 0755, true);
            }
            
            // Crear archivo de configuración
            $configContent = "<?php
// Configuración de la aplicación
define('APP_SECRET_KEY', '{$appSecret}');
define('APP_ENV', 'production');

// Configuración de la base de datos MySQL
define('DB_HOST', '{$dbHost}');
define('DB_PORT', '{$dbPort}');
define('DB_USER', '{$dbUser}');
define('DB_PASSWORD', '{$dbPassword}');
define('DB_NAME', '{$dbName}');

// Configuración de seguridad
define('JWT_SECRET_KEY', '{$jwtSecret}');
define('TOKEN_EXPIRY_HOURS', 24);

// Configuración global para las clases
\$config = [
    'APP_SECRET_KEY' => APP_SECRET_KEY,
    'APP_ENV' => APP_ENV,
    'DB_HOST' => DB_HOST,
    'DB_PORT' => DB_PORT,
    'DB_USER' => DB_USER,
    'DB_PASSWORD' => DB_PASSWORD,
    'DB_NAME' => DB_NAME,
    'JWT_SECRET_KEY' => JWT_SECRET_KEY,
    'TOKEN_EXPIRY_HOURS' => TOKEN_EXPIRY_HOURS
];

// Configurar las clases de configuración
App\\Config\\App::setConfig([
    'APP_SECRET_KEY' => APP_SECRET_KEY,
    'APP_ENV' => APP_ENV,
    'JWT_SECRET_KEY' => JWT_SECRET_KEY,
    'TOKEN_EXPIRY_HOURS' => TOKEN_EXPIRY_HOURS
]);

App\\Config\\Database::setConfig([
    'DB_HOST' => DB_HOST,
    'DB_PORT' => DB_PORT,
    'DB_USER' => DB_USER,
    'DB_PASSWORD' => DB_PASSWORD,
    'DB_NAME' => DB_NAME
]);
?>";
            
            file_put_contents('config.php', $configContent);
            
            // Crear archivo de tokens inicial
            file_put_contents('data/tokens.json', json_encode([]));
            chmod('data/tokens.json', 0600);
            
            $success[] = 'Configuración completada exitosamente';
            $success[] = 'Archivo config.php creado';
            $success[] = 'Directorio data/ creado y configurado';
            $success[] = 'Puedes acceder a la aplicación ahora';
            
        } catch (Exception $e) {
            $errors[] = 'Error al crear archivos: ' . $e->getMessage();
        }
    }
}

// Generar claves secretas aleatorias
$randomAppSecret = bin2hex(random_bytes(32));
$randomJwtSecret = bin2hex(random_bytes(32));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalación - SQL Web Console</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #0a0a0a 0%, #050505 100%);
            color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .install-card {
            background: rgba(26, 26, 26, 0.9);
            border: 2px solid #00ffff;
            border-radius: 10px;
            box-shadow: 0 0 30px rgba(0, 255, 255, 0.2);
        }
        .neon-text {
            color: #00ffff;
            text-shadow: 0 0 10px #00ffff;
        }
        .cyberpunk-btn {
            background: linear-gradient(45deg, #00ffff, #8000ff);
            border: none;
            color: #0a0a0a;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .cyberpunk-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 255, 255, 0.4);
            color: #0a0a0a;
        }
        .cyberpunk-input {
            background: rgba(10, 10, 10, 0.8);
            border: 2px solid #00ffff;
            color: #ffffff;
            border-radius: 5px;
        }
        .cyberpunk-input:focus {
            outline: none;
            border-color: #ff0080;
            box-shadow: 0 0 15px rgba(255, 0, 128, 0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-8 col-lg-6">
                <div class="install-card p-4">
                    <div class="text-center mb-4">
                        <h2 class="neon-text">
                            <i class="fas fa-database"></i> SQL Web Console
                        </h2>
                        <p class="text-muted">Instalación y Configuración</p>
                    </div>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-exclamation-triangle"></i> Errores encontrados:</h6>
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success">
                            <h6><i class="fas fa-check-circle"></i> Instalación completada:</h6>
                            <ul class="mb-0">
                                <?php foreach ($success as $msg): ?>
                                    <li><?php echo htmlspecialchars($msg); ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <hr>
                            <a href="index.php" class="btn cyberpunk-btn">
                                <i class="fas fa-rocket"></i> Ir a la Aplicación
                            </a>
                        </div>
                    <?php else: ?>
                        <form method="POST">
                            <h5 class="neon-text mb-3">
                                <i class="fas fa-cog"></i> Configuración de Base de Datos
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Host MySQL</label>
                                        <input type="text" class="form-control cyberpunk-input" name="db_host" 
                                               value="<?php echo htmlspecialchars($_POST['db_host'] ?? 'localhost'); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Puerto MySQL</label>
                                        <input type="number" class="form-control cyberpunk-input" name="db_port" 
                                               value="<?php echo htmlspecialchars($_POST['db_port'] ?? '3306'); ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Usuario MySQL</label>
                                <input type="text" class="form-control cyberpunk-input" name="db_user" 
                                       value="<?php echo htmlspecialchars($_POST['db_user'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Contraseña MySQL</label>
                                <input type="password" class="form-control cyberpunk-input" name="db_password" 
                                       value="<?php echo htmlspecialchars($_POST['db_password'] ?? ''); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Nombre de Base de Datos</label>
                                <input type="text" class="form-control cyberpunk-input" name="db_name" 
                                       value="<?php echo htmlspecialchars($_POST['db_name'] ?? ''); ?>" required>
                            </div>
                            
                            <h5 class="neon-text mb-3 mt-4">
                                <i class="fas fa-shield-alt"></i> Configuración de Seguridad
                            </h5>
                            
                            <div class="mb-3">
                                <label class="form-label">Clave Secreta de Aplicación</label>
                                <div class="input-group">
                                    <input type="text" class="form-control cyberpunk-input" name="app_secret" 
                                           value="<?php echo htmlspecialchars($_POST['app_secret'] ?? $randomAppSecret); ?>" required>
                                    <button type="button" class="btn cyberpunk-btn" onclick="generateSecret('app_secret')">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Clave única para la aplicación</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Clave JWT</label>
                                <div class="input-group">
                                    <input type="text" class="form-control cyberpunk-input" name="jwt_secret" 
                                           value="<?php echo htmlspecialchars($_POST['jwt_secret'] ?? $randomJwtSecret); ?>" required>
                                    <button type="button" class="btn cyberpunk-btn" onclick="generateSecret('jwt_secret')">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Clave para firmar tokens JWT</small>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn cyberpunk-btn btn-lg">
                                    <i class="fas fa-magic"></i> Instalar Aplicación
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    function generateSecret(fieldName) {
        const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        let result = '';
        for (let i = 0; i < 64; i++) {
            result += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.querySelector(`[name="${fieldName}"]`).value = result;
    }
    </script>
</body>
</html> 