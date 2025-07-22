<?php
// Cargar configuración desde archivo de ejemplo si no existe config.php
if (!file_exists('config.php')) {
    copy('config.example.php', 'config.php');
}

// Configuración de la aplicación
define('APP_SECRET_KEY', 'tu_clave_secreta_aqui');
define('APP_ENV', 'development');

// Configuración de la base de datos MySQL
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_USER', 'tu_usuario');
define('DB_PASSWORD', 'tu_password');
define('DB_NAME', 'tu_base_de_datos');

// Configuración de seguridad
define('JWT_SECRET_KEY', 'tu_jwt_secret_key_aqui');
define('TOKEN_EXPIRY_HOURS', 24);

// Configuración global para las clases
$config = [
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
?> 